#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys
import os
import re
import csv


def read_data(fname) :
    mInt = []
    for line in open(fname, 'r') :
	s = line.rstrip('\r\n')
	if '#SEC	MZ	INT' in s : continue
	if '\t' in s :
		dat = s.split('\t')
		mInt.append(float(dat[2]))
    return mInt

### main

mmInt = []
mSumInt = []

for fname in sys.argv[1:] :
#    print fname
    mInt = read_data(fname)
    mmInt.append(mInt)
    mSumInt.append(sum(mInt))

#print mmInt
#print mSumInt
#print sum(mSumInt)

for x in mSumInt :
    print '%.1f; ' % (x),
print '%.1f; ' % (sum(mSumInt))

