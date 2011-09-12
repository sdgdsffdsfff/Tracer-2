					<h3>Trace Data</h3>
					<table style="margin:0 6px;min-width:940px;max-width:1600px;">
						<thead>
							<tr>
								<th style="width:76px;">Time</th>
								<th style="width:76px;">Time &#916;</th>
								<th style="width:76px;">Memory</th>
								<th style="width:76px;">Memory &#916;</th>
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
							echo '<td style="text-align:right;width:76px;">'.$row_data['time'].'</td>';
							if ($row_data['time_delta'] > $td_thresh) {
								echo '<td class="threshold" style="text-align:right;width:76px;">' . $row_data['time_delta'] . '</td>';
							} else {
								echo '<td style="text-align:right;width:76px;">' . $row_data['time_delta'] . '</td>';
							}
							echo '<td style="text-align:right;width:76px;">'.$row_data['memory'].'</td>';
							if ($row_data['memory_delta'] > $md_thresh) {
								echo '<td class="threshold" style="text-align:right;width:76px;">' . $row_data['memory_delta'] . '</td>';
							} else {
								$stat = 'stable';
								if ($row_data['memory_delta'] > 0) {
									$stat = 'increase';
								} else if ($row_data['memory_delta'] < 0 ) {
									$stat = 'decrease';
								}
								echo '<td class="'.$stat.'" style="text-align:right;width:76px;">' . $row_data['memory_delta'] . '</td>';
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
							echo '<tr class="'.$class.'"><td colspan="1"></td>';
							$vars = '';
							if (is_array($row_data['vars'])) {
								foreach ($row_data['vars'] as $var) {
									$vars .= htmlentities($var) . '<br />';
								}
								$vars = '<div class="vardump"><code>' . $vars . '</code></div>';
							}
							echo '<td colspan="6">' . $vars . '</td>';
							echo '</tr>';
						}
						ob_end_flush();
						?>
						</tbody>
					</table>
