<?php
$vars = array(
			'file' => FILTER_SANITIZE_STRING,
			'line' => FILTER_VALIDATE_INT,
		);
$post = filter_input_array(INPUT_POST, $vars);

$lines = @file($post['file']);
$start = 0;
if (($post['line'] - 1) > 5) {
	$start = $post['line'] - 6;
}
$end = $post['line'] + 4;
if ($end >= count($lines)) {
	$end = count($lines) - 1;
}

$code = array('first' => $start+1, 'last' => $end+1);
for ($i = $start; $i <= $end; $i++) {
	$code[$i+1] = highlight_string($lines[$i], true);
}

echo json_encode($code);
