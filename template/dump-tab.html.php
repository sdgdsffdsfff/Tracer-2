					<script type="text/javascript">
						function show_code(file_path, line_num) {
							$.post(
								"file_popper.php", 
								{ "file": file_path, "line": line_num },
								function(data){
									var first = data.first;
									var last = data.last;
									var table = '';
									for (var l = first; l <= last; l++) {
										var bold = '';
										var zebra = ((l%2) == 0) ? 'even' : 'odd';
										if (l == line_num) {
											bold = ' style="font-weight:bold; font-style:italic;"';
										}
										var content = $("<div/>").html(data[l]).html();
										table += '<tr class="'+zebra+'"><td'+bold+'>'+l+':</td><td'+bold+'>'+content+'</td></tr>';
									}
									$('#popper_rows').html(table);
									$('#popper').dialog({ 
										modal: true,
										width: 960
									});
								},
								"json"
							);
						}
					</script>
					<div id="popper" title="Code" style="display:none;">
						<table id="popper_rows" style="width:934px;">
						</table>
					</div>
					<h3>Trace Data</h3>
					<table style="width:926px;">
						<thead>
							<tr>
								<th style="width:76px;">Time</th>
								<th style="width:76px;">Time &#916;</th>
								<th style="width:76px;">Memory</th>
								<th style="width:76px;">Memory &#916;</th>
								<th>Function</th>
							</tr>
							<tr>
								<th colspan="5" style="text-align:right;">Reference File and Line #</th>
							</tr>
							<tr>
								<th colspan="5" style="text-align:left;">Function Data</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						ob_start();
						foreach ($trace->lines as $r => $row_data) {
							$class = (($r%2) == 0) ? 'even' : 'odd';
							$slant = ($row_data['point'] == 1) ? 'font-style:italic;' : '';
							$time_delta_class = ($row_data['time_delta'] > $td_thresh) ? ' class="threshold"' : '';
							$mem_delta_class = ($row_data['memory_delta'] > $md_thresh) ? ' class="threshold' : '';
							if ($mem_delta_class === '') {
								$mem_delta_class = ' class="stable"';
								if ($row_data['memory_delta'] > 0) {
									$mem_delta_class = ' class="increase"';
								} else if ($row_data['memory_delta'] < 0 ) {
									$mem_delta_class = ' class="decrease"';
								}
							}
							$function = '<div style="text-align:right;width:' . ($row_data['level'] * 12) . 'px;">';
							$function .= ($row_data['point'] === 0) ? '&raquo; </div> ' : '&laquo; </div> ';
							if (isset($row_data['function'])) {
								$function .= '<div style="font-weight:bold;'.$slant.'">' . $row_data['function'] . '</div>';
							}
							$line = ($row_data['line'] !== '') ? ' on Line #' . $row_data['line'] : '';
							$code_line = '';
							if ($row_data['line'] !== '' && $row_data['file'] !== '') {
								$code_line = '<a href="javascript:show_code(\'' . $row_data['file'].'\', ' . $row_data['line'] . ');">' . $row_data['file'] . $line . '</a>';
							}
							$vars = '';
							if (is_array($row_data['vars'])) {
								foreach ($row_data['vars'] as $var) {
									$vars .= highlight_string($var, true) . '<br />';
								}
								$vars = '<div class="vardump">' . $vars . '</div>';
							}
							$data = <<<EOD
							<tr class="{$class}">
								<td style="text-align:right;width:76px;">{$row_data['time']}</td>
								<td{$time_delta_class} style="text-align:right;width:76px;">{$row_data['time_delta']}</td>
								<td style="text-align:right;width:76px;">{$row_data['memory']}</td>
								<td{$mem_delta_class} style="text-align:right;width:76px;">{$row_data['memory_delta']}</td>
								<td>{$function}</td>
							</tr>
							<tr class="{$class}">
								<td colspan="5" style="text-align:right;">{$code_line}</td>
							</tr>
							<tr class="{$class}">
								<td colspan="5">{$vars}</td>
							</tr>
EOD;
							echo $data;
						}
						ob_end_flush();
						?>
						</tbody>
					</table>
