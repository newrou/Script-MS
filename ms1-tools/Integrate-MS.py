#!/usr/bin/env python3
from pyopenms import *
import sys
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.colors as colors

def plot_spectr_2D(exp, ms_level=1, marker_size = 5):
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
     plt.savefig("test-2D.png", dpi=300)

def plot_spectr_1D(spectrum):
     # plot every peak in spectrum and annotate with it's m/z
     for mz, i in zip(*spectrum.get_peaks()):
         plt.plot([mz, mz], [0, i], color = 'black')
         if i>20.0 : plt.text(mz, i, "%.4f" % mz)
     # for the title add RT and Precursor m/z if available
     title = ''
#     if spectrum.getRT() >= 0:
#         title += 'RT: ' + str(spectrum.getRT())
#     if len(spectrum.getPrecursors()) >= 1:
#         title += '   Precursor m/z: ' + str(spectrum.getPrecursors()[0].getMZ())
     plt.title(title)
     plt.ylabel('intensity')
     plt.xlabel('m/z')
     plt.ylim(bottom=0)
#     plt.show()
     plt.savefig("test-1D.png", dpi=300)

def find_ms(m_mz, mz):
    i=0
    for x in m_mz :
        if abs(x-mz)< 0.0100 :
#            print(i)
            return i
        i=i+1
    return -1

def integrate_ms1(src_ms):
#    r_ms = MSExperiment()
    m_mz = []
    m_int = []
    for s in src_ms :
        if s.getMSLevel()==1 :
            for mz, i in zip(*s.get_peaks()):
                ind = find_ms(m_mz, mz)
                if ind<0 : 
                    m_mz.append(mz)
                    m_int.append(i)
                else :
                    m_mz[ind]=(m_mz[ind]+mz)/2
                    m_int[ind]+=i
    int_max = max(m_int)
    m_int = [100.0*x/int_max for x in m_int]
    s.set_peaks((m_mz, m_int))
#    r_ms.addSpectrum(s)
#    print(m_mz)
    return s

fname = sys.argv[1]

#from urllib.request import urlretrieve
#gh = "https://raw.githubusercontent.com/OpenMS/pyopenms-extra/master"
#urlretrieve (gh + "/src/data/FeatureFinderMetaboIdent_1_input.mzML", "test.mzML")

exp = MSExperiment()
MzMLFile().load(fname, exp)

print('integrate...')
r=integrate_ms1(exp)
print('plot...')
plot_spectr_1D(r)

#plot_spectr_2D(exp)
