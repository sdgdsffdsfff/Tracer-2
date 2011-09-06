#!/usr/bin/env python

import argparse
import json

def trace_load(my_file):
	row_data = {}
	lines = []
	file = open(my_file, "r")
	for line in file:
		if 'Version:' in line:
			pass
		elif 'File format:' in line:
			pass
		elif line.strip() == '':
			pass
		elif 'TRACE START' in line:
			row_data['start'] = line.strip()
		elif 'TRACE END'in line:
			row_data['end'] = line.strip()
		else:
			line_data = []
			for x in line.split("\t"):
		 		line_data.append (x.strip())
		 	lines.append (line_data)
	row_data['lines'] = lines
	return json.dumps(row_data)

def main():
	parser = argparse.ArgumentParser(description='Loads and parses PHP XDebug trace files.')
	parser.add_argument('file', metavar='F', nargs='?', help='The full path and filename of the tracefile to parse')

	args = parser.parse_args()
	if args.file != None:
		print trace_load(args.file)
	else:
		parser.print_help()

main()