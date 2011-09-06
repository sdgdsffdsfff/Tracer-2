<?php
class TraceFileList {
	public $files = array();

	public function __construct ($path) {
		$di = new DirectoryIterator($path);
		foreach ($di as $file) {
			$fname = $file->getFilename();
			// Do we need to exclude Tracer trace results?
			if ($_ENV['tracer']['block_tracer'] && in_array($path . DS . $fname, $_ENV['tracer']['tracer_traces'])) {
				continue;
			}
			// Is this a trace file?
			if (substr_count($fname, '.xt') == 0) {
				continue;
			} else {
				$this->files[] = $fname;
			}
		}
		return $this;
	}
}