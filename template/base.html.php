<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="/css/text.css" />
		<link rel="stylesheet" type="text/css" href="/css/grid.css" />
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold&v1" />
		<link rel="stylesheet" type="text/css" href="/css/styles.css" />
		<link rel="stylesheet" type="text/css" href="/css/tracer-ui/jquery-ui.css" />
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript" src="/js/tracer.js"></script>
		<title>Tracer - An XDebug Trace File Analyzer</title>
	</head>
	<body>
		<div class="container_16" id="header">
			<div class="grid_6">
				<h1 id="app-name"><a href="/">Tracer</a> <span id="app-tagline">XDebug Trace File Analyzer</span></h1>
			</div>
			<div class="grid_10" id="tracer-form">
<?php echo $this->trace_form . PHP_EOL; ?>
			</div>
		</div>
		<div class="clear"></div>
		<div class="container_16">&nbsp;<!-- spacer. May use this for other info later. --></div>
		<div class="clear"></div>
		<div class="container_16" id="output">
<?php 
if (isset($this->output)) {
	echo $this->output . PHP_EOL;
}
?>
		</div>
	</body>
</html>