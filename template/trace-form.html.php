				<form name="trace-options" id="tracer-options" method="post" action="<?php $_SERVER['REQUEST_URI']; ?>">
					<table width="100%">
						<tr>
							<td><h2>Tracer Settings: </h2>
							<td>
								<label for="file">File: 
									<select name="file" id="file">
										<option value="">-- Select a trace file --</option>
<?php 
										foreach ($this->file_list->files as $filename) {
echo '										<option value="'.$_ENV['xdebug']['trace_output_dir'].DS.$filename.'">'.$filename.'</option>'.PHP_EOL;
										}
?>
									</select>
								</label>
							</td>
							<td rowspan="2">
								<input type="submit" value="Parse" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="memory_alert">Memory &#916; threshold (Kb): 
								<input type="text" name="memory_alert" id="memory_alert" value="6" size="5"/></label>
							</td>
							<td>
								<label for="time_alert">Time &#916; threshold (ms): 
								<input type="text" name="time_alert" id="time_alert" value="10" size="5"/></label>
							</td>
						</tr>
					</table>
				</form>
