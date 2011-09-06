<?php
/*!
 * This is the config / pre-loader file for Tracer
 */
define('DS', DIRECTORY_SEPARATOR);

/*!
 * Let's grab the xdebug settings and store those in an unused GLOBAL... $_ENV.
 * We'll need to references many of, if not all of, these later on.
 */
$_ENV['xdebug']['auto_trace'] = ini_get('xdebug.auto_trace');
$_ENV['xdebug']['trace_format'] = ini_get('xdebug.trace_format');
$_ENV['xdebug']['trace_output_dir'] = ini_get('xdebug.trace_output_dir');
$_ENV['xdebug']['trace_output_name'] = ini_get('xdebug.trace_output_name');
$_ENV['xdebug']['profiler_append'] = ini_get('xdebug.profiler_append');
$_ENV['xdebug']['profiler_enable'] = ini_get('xdebug.profiler_enable');
$_ENV['xdebug']['profiler_enable_trigger'] = ini_get('xdebug.profiler_enable_trigger');
$_ENV['xdebug']['profiler_output_name'] = ini_get('xdebug.profiler_output_name');
$_ENV['xdebug']['profiler_output_dir'] = ini_get('xdebug.profiler_output_dir');
$_ENV['xdebug']['collect_params'] = ini_get('xdebug.collect_params');
$_ENV['xdebug']['collect_includes'] = ini_get('xdebug.collect_includes');
$_ENV['xdebug']['collect_return'] = ini_get('xdebug.collect_return');

// Block trace reports for this script?
$_ENV['tracer']['block_tracer'] = true;
// Cache file of trace files created by this application.
$_ENV['tracer']['trace_cache'] = 'tracer.cache';
// Cache file of classes used this application.
$_ENV['tracer']['class_cache'] = 'tracer_class.cache';
// Where to store the cache file. Defaults to the trace_output_dir, falls back to the webroot
$_ENV['tracer']['cache_path'] = $_ENV['xdebug']['trace_output_dir'];


/*!
 * YOU SHOULD NOT NEED TO MODIFY ANYTHING BELOW THIS COMMENT BLOCK
 * 
 * Much of the functionality below was borrowed from the wonderful framework 
 * created by Jake Tews. It has been modified only as much as was needed to 
 * support this application without needing his entire framework.
 */

// Include path and siteroot:
$included_files = get_included_files();
$script_path = array_shift($included_files);
$include_path = $file_path = dirname($script_path);
while (!file_exists($file_path . "/tracer.config.php") && $file_path != ($tmp_path = dirname($file_path))) {
	$include_path .= PATH_SEPARATOR . ($file_path = $tmp_path);
}
ini_set('include_path', $include_path);
define('SITEROOT', $file_path);

// Determine webroot:
if (!isset($_SERVER['PHP_SELF'])) $_SERVER['PHP_SELF'] = $script_path;
if (!isset($_SERVER['PATH_INFO'])) $_SERVER['PATH_INFO'] = '';
define('WEBROOT', substr($_SERVER['PHP_SELF'], 0, -strlen(substr($script_path, strlen(SITEROOT)) . $_SERVER['PATH_INFO'])));
unset($include_path, $file_path, $tmp_path, $script_path, $included_files); ///< Clean up used variables so they don't show up in userland

// Check our cache dir / path for usability
if (!is_dir($_ENV['tracer']['cache_path']) && !mkdir($_ENV['tracer']['cache_path'], 0777)) {
	throw new Exception('Directory [' . $_ENV['tracer']['cache_path'] . '] does not exist and it cannot be created.');
}
if (!is_writeable($_ENV['tracer']['cache_path']) && !chmod($_ENV['tracer']['class_cache'], 0777)) {
	throw new Exception('Directory [' . $_ENV['tracer']['cache_path'] . '] is not writeable and cannot be modified.');
}

// Update the correct paths for caching and classes.
$_ENV['tracer']['class_cache'] = $_ENV['tracer']['cache_path'] . DS . $_ENV['tracer']['class_cache'];
$_ENV['tracer']['trace_cache'] = $_ENV['tracer']['cache_path'] . DS . $_ENV['tracer']['trace_cache'];
$_ENV['tracer']['library_dir'] = SITEROOT . DS . 'lib';

// Get a list of and add the current trace files for Tracer
$tracer_traces = array();
if (file_exists($_ENV['tracer']['trace_cache'])) {
	$tracer_traces = file_get_contents($_ENV['tracer']['trace_cache']);
	$tracer_traces = explode("\r\n",$tracer_traces);
}
$tracer_traces[] = $current_trace = xdebug_get_tracefile_name();
file_put_contents($_ENV['tracer']['trace_cache'], $current_trace . "\r\n", FILE_APPEND);
$_ENV['tracer']['tracer_traces'] = $tracer_traces;

// LET'S GO!!!
// Register spl_autoload functionality
spl_autoload_register('autoload');

// Autoload 
function autoload ($class_name) {
	static $class_list;
	if ($class_list === null) {
		$f = $_ENV['tracer']['class_cache'];
		file_exists($f) && include($f);
		unset($f);
	}
	if (!isset($class_list[$class_name]) || !file_exists($class_list[$class_name])) {
		$class_list = generate_class_list();
	}
	if (isset($class_list[$class_name])) {
		require($class_list[$class_name]);
		return true;
	}
	return false;
}

function generate_class_list () {
	$class_list = array();
	$rdi = new RecursiveDirectoryIterator($_ENV['tracer']['library_dir'], RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
	$fcf = new ClassFilter($rdi);
	$rii = new RecursiveIteratorIterator($fcf);
	foreach ($rii as $filename => $info) {
		$class_list[str_replace(array('.class.php', '.interface.php'), '', basename($filename))] = $filename;
	}
	file_put_contents(
		$_ENV['tracer']['class_cache'], 
		'<?php $class_list = ' . var_export($class_list, true) . ';'
	);
	@chmod($_ENV['tracer']['class_cache'], 0666);
	return $class_list;
}

class ClassFilter extends RecursiveFilterIterator {
	private static $excluded_dirs = array(
		'.svn' => true,
		'tests' => true
	);
	public function accept () {
		$file = $this->current();
		$is_file = $file->isFile();
		$filename = $file->getFilename();
		return (
			$is_file && (strpos($filename, '.class.php') !== false || strpos($filename, '.interface.php') !== false)
		) || (
			!$is_file && !isset(self::$excluded_dirs[$filename])
		);
	}
}
