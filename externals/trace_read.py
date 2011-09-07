#!/usr/bin/env python

import argparse
import json
from itertools import ifilter

def should_keep(line):
	if 'Version:' in line:
		return False
	if 'File format:' in line:
		return False
	if line.strip() == '':
		return False
	return True

def trace_load(filename):
	row_data = {}
	lines = []
	with open(filename) as f:
		for line in ifilter(should_keep, f):
			if 'TRACE START' in line:
				row_data['start'] = line.strip()
			elif 'TRACE END'in line:
				row_data['end'] = line.strip()
			else:
				lines.append([x.strip() for x in line.split("\t")])
	row_data['lines'] = lines
	return json.dumps(row_data)

if __name__ == "__main__":
	parser = argparse.ArgumentParser(description='Loads and parses PHP XDebug trace files.')
	parser.add_argument('file', help='The full path and filename of the tracefile to parse')

	args = parser.parse_args()
	if args.file != None:
		print trace_load(args.file)
	else:
		parser.print_help()