<?php
/*!
 * Tracefile object
 * 
 * This class creates an object of the trace file making it easier to access and 
 * manipulate the data stored in the original trace file without mucking up the
 * original source. 
 * 
 * @author Mark Litchfield <ldapmonkey@gmail.com>
 * @date 2011-08-28 16:13:00 EST
 * @version 0.1-alpha
 */
class TraceFile {
	const LVL = 0;                //> Execution level
	const ID = 1;                 //> Function ID
	const POINT = 2;              //> Entry/Exit (0 == Entry)
	const TIME = 3;               //> Duh
	const MEM = 4;                //> Memory Utilization at this point in the execution
	const NAME = 5;               //> The function being executed
	const TYPE = 6;               //> PHP Internal or user-defined function (0 == internal)
	const INC = 7;                //> Include/Require file being loaded/referenced
	const REF = 8;                //> The script containing the function being executed
	const LINE = 9;               //> The line in file(REF) where the execution happens
	const VARCNT = 10;            //> The line in file(REF) where the execution happens
	const DATA_START = 11;        //> The line in file(REF) where the execution happens

	public $trace_data = array(); //> Holds instance data for the selected trace file
	public $raw = array();        //> Holds the raw data returned from the Python script that parses the trace file.

	/*!
	 * TraceFile object constructor
	 * 
	 * Creates and returns and instance of a trace file in an easy-to-access 
	 * format.
	 * 
	 * @param string $trace_file The full path to the selected trace file to parse
	 * @return object Instance of the TraceFile object
	 */
	public function __construct ($trace_file) {
		$this->raw = json_decode(shell_exec('./externals/trace_read.py '.$trace_file), true);
		preg_match("/\[(.*)\]/", $this->raw['start'], $time);
		$this->trace_data['start'] = date('U', strtotime($time[1]));
		preg_match("/\[(.*)\]/", $this->raw['end'], $time);
		$this->trace_data['end'] = date('U', strtotime($time[1]));
		$this->trace_data['peak_mem'] = 0;
		for ($index = 0; $index < count($this->raw['lines']); $index++) {
			$this->trace_data['lines'][] = $this->build_line_data($index);
		}
		return $this;
	}

	public function __get ($key) {
		if (isset($this->trace_data[$key])) {
			return $this->trace_data[$key];
		}
		return false;
	}

	public static function constant ($const) {
		switch ($const) {
			case 'LVL' : return self::LVL;
			case 'ID' : return self::ID;
			case 'POINT' : return self::POINT;
			case 'TIME' : return self::TIME;
			case 'MEM' : return self::MEM;
			case 'NAME' : return self::NAME;
			case 'TYPE' : return self::TYPE;
			case 'INC' : return self::INC;
			case 'REF' : return self::REF;
			case 'LINE' : return self::LINE;
			case 'VARCNT' : return self::VARCNT;
			case 'DATA_START' : return self::DATA_START;
			default: return 999999;
		}
	}

	public function function_types () {
		$funcs = array_map (
			function ($line) {
				if ((string)$line['point'] === '0') { //> Deal only with 'entry' points
					return ((string)$line['type'] === '0') ? 'php' : 'user';
				}
				return 0;
			},
			$this->trace_data['lines']
		);
		$funcs = array_count_values($funcs);
		$tot = $funcs['php'] + $funcs['user'];
		return array($funcs['php'], $funcs['user'], $tot);
	}

	private function mem_delta ($before, $after) {
		return $after - $before;
	}

	private function time_delta ($before, $after) {
		// To prevent oddball scientific notation, return a string.
		// Need to figure something out so we can do math on this value at some
		// point in the future
		return sprintf("%01.6f", ($after - $before));
	}

	public function run_time () {
		reset($this->trace_data['lines']);
		end($this->trace_data['lines']);
		$last = current($this->trace_data['lines']);
		return $last['time'];
	}

	public function top_functions () {
		$funcs = array_map (
			function ($line) {
				if (isset($line['function']) && $line['function'] !== '') {
					return $line['function'];
				}
				return 0;
			},
			$this->trace_data['lines']
		);
		$funcs = array_count_values($funcs);
		unset($funcs[0]);
		arsort($funcs);
		return array_slice($funcs, 0, 10);
	}

	public function timeline () {
		$timeline = array_map (
			function ($line) {
				return array($line['time'], $line['memory']);
			},
			$this->trace_data['lines']
		);
		return $timeline;
	}

	public function memory_peak ($mem, $line_num) {
		if ($mem > $this->trace_data['peak_mem']) {
			$this->trace_data['peak_mem'] = $mem;
			$this->trace_data['peak_line'] = $line_num;
		}
	}

	/*!
	 * Parses the trace data line and returns an array of the trace data.
	 */
	public function build_line_data ($row) {
		$row_data = array();
		if (isset($this->raw['lines'][$row][self::LVL]) !== '' && $row >= 0) {
			$row_data['level'] = (int)$this->raw['lines'][$row][self::LVL];
			$row_data['point'] = (int)$this->raw['lines'][$row][self::POINT];
			$row_data['time'] = $this->raw['lines'][$row][self::TIME];
			$row_data['time_delta'] = 0;
			if ($row > 0 && isset($this->raw['lines'][$row-1][self::TIME])) {
				$row_data['time_delta'] = $this->time_delta($this->raw['lines'][$row-1][self::TIME], $this->raw['lines'][$row][self::TIME]);
			}
			$row_data['memory'] = $this->raw['lines'][$row][self::MEM];
			$row_data['memory_delta'] = 0;
			if ($row > 0 && isset($this->raw['lines'][$row-1][self::MEM])) {
				$this->memory_peak($this->raw['lines'][$row][self::MEM], $row);
				$row_data['memory_delta'] = $this->mem_delta($this->raw['lines'][$row-1][self::MEM], $this->raw['lines'][$row][self::MEM]);
			}
			$row_data['function'] = '';
			if (isset($this->raw['lines'][$row][self::NAME])) {
				$row_data['function'] = $this->raw['lines'][$row][self::NAME];
			}
			$row_data['type'] = '';
			if (isset($this->raw['lines'][$row][self::TYPE])) {
				$row_data['type'] = $this->raw['lines'][$row][self::TYPE];
			}
			$row_data['inc'] = '';
			if (isset($this->raw['lines'][$row][self::INC])) {
				$row_data['file'] = $this->raw['lines'][$row][self::INC];
			}
			$row_data['file'] = '';
			if (isset($this->raw['lines'][$row][self::REF])) {
				$row_data['file'] = $this->raw['lines'][$row][self::REF];
			}
			$row_data['line'] = '';
			if (isset($this->raw['lines'][$row][self::LINE])) {
				$row_data['line'] = $this->raw['lines'][$row][self::LINE];
			}
			$row_data['vars'] = '';
			if (isset($this->raw['lines'][$row][self::VARCNT]) && $this->raw['lines'][$row][self::VARCNT] > 0) {
				$vars = array();
				for ($v = 0; $v < $this->raw['lines'][$row][self::VARCNT]; $v++) {
					$vars[] = $this->raw['lines'][$row][self::DATA_START + $v];
				}
				$row_data['vars'] = $vars;
			}
		} else {
			// Last entry never has a level set
			$row_data['level'] = 0;
			$row_data['point'] = (int)$this->raw['lines'][$row][self::POINT];
			$row_data['time'] = $this->raw['lines'][$row][self::TIME];
			if ($row > 0 && isset($this->raw['lines'][$row-1][self::TIME])) {
				$row_data['time_delta'] = $this->time_delta($this->raw['lines'][$row-1][self::TIME], $this->raw['lines'][$row][self::TIME]);
			}
			$row_data['memory'] = $this->raw['lines'][$row][self::MEM];
			if ($row > 0 && isset($this->raw['lines'][$row-1][self::MEM])) {
				$row_data['memory_delta'] = $this->mem_delta($this->raw['lines'][$row-1][self::MEM], $this->raw['lines'][$row][self::MEM]);
			}
			$row_data['function'] = 'Application Terminated';
			$row_data['type'] = '';
			$row_data['inc'] = '';
			$row_data['file'] = '';
			$row_data['line'] = '';
			$row_data['vars'] = '';
		}
		return $row_data;
	}
}