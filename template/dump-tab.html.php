					<div class="grid_16 alpha omega">
						<div id="trace">
							<table width="100%">
								<thead>
									<tr>
										<th>Time</th>
										<th>Time &#916;</th>
										<th>Memory</th>
										<th>Memory &#916;</th>
										<th>Function</th>
										<th>File</th>
										<th>Line</th>
									</tr>
								</thead>
								<tbody>
								<?php 
								ob_start();
								for ($r = 0; $r < count($trace->lines); $r++) {
									$row_data = $trace->build_row($r);
									// build our table row
									$class = (($r%2) == 0) ? 'even' : 'odd';
									echo '<tr class="'.$class.'">';
									$slant = ($row_data['point'] == 1) ? 'font-style:italic;' : '';
									echo '<td style="text-align:right;">'.$row_data['time'].'</td>';
									if ($row_data['time_delta'] > $td_thresh) {
										echo '<td class="threshold" style="text-align:right;">' . $row_data['time_delta'] . '</td>';
									} else {
										echo '<td style="text-align:right;">' . $row_data['time_delta'] . '</td>';
									}
									echo '<td style="text-align:right;">'.$row_data['memory'].'</td>';
									if ($row_data['memory_delta'] > $md_thresh) {
										echo '<td class="threshold" style="text-align:right;">' . $row_data['memory_delta'] . '</td>';
									} else {
										$class = 'stable';
										if ($row_data['memory_delta'] > 0) {
											$class = 'increase';
										} else if ($row_data['memory_delta'] < 0 ) {
											$class = 'decrease';
										}
										echo '<td class="'.$class.'" style="text-align:right;">' . $row_data['memory_delta'] . '</td>';
									}
									
									$function = '<div style="text-align:right;width:' . ($row_data['level'] * 10) . 'px;">';
									$function .= ($row_data['point'] == 0) ? '&raquo; </div> ' : '&laquo; </div> ';
									if (isset($row_data['function'])) {
										$function .= '<div style="font-weight:bold;'.$slant.'">' . $row_data['function'] . '</div>';
									}
									echo '<td>' . $function . '</td>';
									$file = '';
									if (isset($row_data['file'])) {
										$file = $row_data['file'];
									}
									echo '<td>' . $file . '</td>';
									$line_num = '';
									if (isset($row_data['line'])) {
										$line_num = $row_data['line'];
									}
									echo '<td>' . $line_num . '</td>';
									echo '</tr>';
								}
								ob_end_flush();
								?>
								</tbody>
							</table>
						</div>
					</div>