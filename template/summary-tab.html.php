					<!-- First row -->
					<div class="grid_4 alpha">
						<h3>Runtime Summary</h3>
						<ul>
							<li><b>Runtime:</b> <?php echo $trace->run_time(); ?> seconds</li>
							<li><b>Total Memory Used:</b> <?php echo $trace->total_mem(); ?> bytes</li>
							<li><b>Start Time:</b> <?php echo date('Y-m-d H:i:s', $trace->start); ?></li>
							<li><b>End Time:</b> <?php echo date('Y-m-d H:i:s', $trace->end); ?></li>
						</ul>
					</div>
					<div class="grid_6">
						<h3>Functions Summary</h3>
						<script type="text/javascript">
							google.load("visualization", "1", {packages:["corechart"]});
							google.setOnLoadCallback(drawFunctionsSummary);
							function drawFunctionsSummary() {
								var f_summary = new google.visualization.DataTable();
								f_summary.addColumn('string', 'Type');
								f_summary.addColumn('number', 'Count');
								f_summary.addRows(2);
								f_summary.setValue(0, 0, 'PHP Internal');
								f_summary.setValue(0, 1, <?php echo $internal; ?>);
								f_summary.setValue(1, 0, 'User-Defined');
								f_summary.setValue(1, 1, <?php echo $user_defined; ?>);
								var fsp = new google.visualization.PieChart(document.getElementById('f_summary_pie'));
								fsp.draw(f_summary, {width: 400, height: 200, chartArea:{ left:0, top:0, width:"100%",height:"100%"}});
							}
						</script>
						<div id="f_summary_pie"></div>
					</div>
					<div class="grid_6">
						<h3>Most Common (Top 10) Functions</h3>
						<script type="text/javascript">
							google.load("visualization", "1", {packages:["corechart"]});
							google.setOnLoadCallback(drawTop10Functions);
							function drawTop10Functions() {
								var f_top10 = new google.visualization.DataTable();
								f_top10.addColumn('string', 'Function Name');
								f_top10.addColumn('number', 'Count');
								f_top10.addRows(10);
							<?php 
							$t10_row = 0;
							foreach ($trace->top_functions() as $func => $count){ 
							?>
								f_top10.setValue(<?php echo $t10_row;?>, 0, '<?php echo $func; ?>');
								f_top10.setValue(<?php echo $t10_row;?>, 1, <?php echo $count; ?>);
							<?php
								$t10_row++;
							}
							?>
								var t10fp = new google.visualization.PieChart(document.getElementById('top10_functions'));
								t10fp.draw(f_top10, {width: 400, height: 200, chartArea:{ left:0, top:0, width:"100%",height:"100%"}});
							}
						</script>
						<div id="top10_functions"></div>
					</div>
					<div class="clear"></div>
					<!-- Second row -->
					<div class="grid_16">
						<h3>Timeline</h3>
						<script type="text/javascript">
							google.load("visualization", "1", {packages:["corechart"]});
							google.setOnLoadCallback(drawTimeLine);
							function drawTimeLine() {
								var mem_data = new google.visualization.DataTable();
								mem_data.addColumn('string', 'Time');
								mem_data.addColumn('number', 'Memory');
								mem_data.addColumn('number', 'Memory Delta');
							<?php
								$timeline = $trace->timeline();
							?>
								mem_data.addRows(<?php echo count($timeline); ?>);
							<?php
							$mem_row = 0;
							foreach ($timeline as $idx => $row){
								$delta = $row[1] - 0;
								if ($idx > 0) {
									$delta = $row[1] - $timeline[$idx-1][1];
								} 
							?>
								mem_data.setValue(<?php echo $mem_row;?>, 0, '<?php echo $row[0]; ?>');
								mem_data.setValue(<?php echo $mem_row;?>, 1, <?php echo $row[1]; ?>);
								mem_data.setValue(<?php echo $mem_row;?>, 2, <?php echo $delta; ?>);
							<?php
								$mem_row++;
							}
							?>
								var timeline = new google.visualization.LineChart(document.getElementById('timeline_container'));
								timeline.draw(mem_data, {height: 350 });
							}
						</script>
						<div id="timeline_container" style="width:100%;"></div>
					</div>
					<div class="clear"></div>