<?php
$trace = new TraceFile($this->post['file']);
if (isset($trace) && $trace instanceof TraceFile) {
	list($internal, $user_defined, $function_total) = $trace->function_types();
	$int_size = (($internal * 100) / ($function_total)) * 2;
	$user_size =  200 - $int_size;
	$md_thresh = $this->post['memory_alert'] * 1024;
	$td_thresh = $this->post['time_alert'] / 1000;
?>
			<div class="grid_16">
				<h2>Results for <?php echo $this->post['file']; ?></h2>
			</div>
			<div class="clear"></div>
			<div class="grid_16">
				<div id="tabs">
					<ul>
						<li><a href="#overview">Trace Overview</a></li>
						<li><a href="#raw-data">Trace File Dump</a></li>
					</ul>
					<div id="overview" class="grid_16 ui-tabs-hide">
<?php include ('template/summary-tab.html.php'); ?>
					</div>
					<div id="raw-data" class="grid_16 ui-tabs-hide">
<?php include ('template/dump-tab.html.php'); ?>
					</div>
				</div>
			</div>
<?php
}
?>