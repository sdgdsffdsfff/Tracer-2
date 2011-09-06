<?php
/*!
 * Tracer is an XDebug trace output analyzer. It takes the trace output from 
 * XDebug and makes it pretty and easy to read allowing the developer to quickly
 * identify bottlenecks and areas of optimization.
 * 
 * @author Mark Litchfield <ldapmonkey@gmail.com>
 * @version 0.1-alpha
 */ 
require_once('tracer.config.php');

$tpl = new Savant3();

$tpl->file_list = new TraceFileList($_ENV['xdebug']['trace_output_dir']);

$fields = array(
			'file'          => FILTER_SANITIZE_STRING,
			'memory_alert'  => FILTER_VALIDATE_FLOAT,
			'time_alert'    => FILTER_VALIDATE_FLOAT,
		  );

$post = filter_input_array(INPUT_POST, $fields);

if (isset($post['file'])) {
	$tpl->post = $post;
	$tpl->output = $tpl->fetch('/template/output.html.php');
}

$tpl->trace_form = $tpl->fetch('/template/trace-form.html.php');
$tpl->display('template/base.html.php');
