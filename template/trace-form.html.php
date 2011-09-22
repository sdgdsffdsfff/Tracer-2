				<form name="trace-options" id="tracer-options" method="post" action="<?php $_SERVER['REQUEST_URI']; ?>">
					<table width="100%">
						<tr>
							<td colspan="2">
								<select name="file" id="file">
									<option value="">-- Select a trace file --</option>
<?php 
										foreach ($this->file_list->files as $filename) {
echo '									<option value="'.$_ENV['xdebug']['trace_output_dir'].DS.$filename.'">'.$filename.'</option>'.PHP_EOL;
										}
?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label for="memory_alert">Memory &#916; threshold (Kb): 
								<input type="text" name="memory_alert" id="memory_alert" value="6" size="5"/></label>
								<label for="time_alert">Time &#916; threshold (ms): 
								<input type="text" name="time_alert" id="time_alert" value="10" size="5"/></label>
							</td>
							<td>
								<input type="submit" value="Parse" />
							</td>
						</tr>
					</table>
				</form>
