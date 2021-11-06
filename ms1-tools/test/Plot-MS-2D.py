#!/usr/bin/env python3
from pyopenms import *
import sys
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.colors as colors

def plot_spectra_2D(exp, ms_level=1, marker_size = 5):
     exp.updateRanges()
     print('collecting peak data...')
     for spec in exp:
         if spec.getMSLevel() == ms_level:
             mz, intensity = spec.get_peaks()
             p = intensity.argsort() # sort by intensity to plot highest on top
             rt = np.full([mz.shape[0]], spec.getRT(), float)
             plt.scatter(rt, mz[p], c = intensity[p], cmap = 'afmhot_r', s=marker_size,
                         norm=colors.LogNorm(exp.getMinInt()+1, exp.getMaxInt()))
     plt.clim(exp.getMinInt()+1, exp.getMaxInt())
     plt.xlabel('time (s)')
     plt.ylabel('m/z')
     plt.colorbar()
     #plt.figure(figsize=(10,7))
     print('plot...')
     #plt.show() # slow for larger data sets
     plt.savefig("test.png", dpi=300)


fname = sys.argv[1]

#from urllib.request import urlretrieve
#gh = "https://raw.githubusercontent.com/OpenMS/pyopenms-extra/master"
#urlretrieve (gh + "/src/data/FeatureFinderMetaboIdent_1_input.mzML", "test.mzML")

exp = MSExperiment()
MzMLFile().load(fname, exp)

plot_spectra_2D(exp)
