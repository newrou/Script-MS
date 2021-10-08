#!/usr/bin/env python3
import sys
from pyopenms import *

fname1 = sys.argv[1]
fname2 = sys.argv[2]

inp = MSExperiment()
MzMLFile().load(fname1, inp)
e = MSExperiment()
for s in inp:
#	if s.getMSLevel() > 1:
	filtered_mz = []
	filtered_int = []
	for mz, i in zip(*s.get_peaks()):
		correct_mz = mz+0.00565
#		print( '%f -> %f' % (mz, correct_mz) )
		filtered_mz.append(correct_mz)
		filtered_int.append(i)
	s.set_peaks((filtered_mz, filtered_int))
	e.addSpectrum(s)

MzMLFile().store(fname2, e)
