#!/usr/bin/env python3
from pyopenms import *
import sys
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.colors as colors

def plot_spectr_2D(exp, ms_level=1, marker_size = 7):
     exp.updateRanges()
     print('collecting peak data...')
     for spec in exp:
         if spec.getMSLevel() == ms_level:
             mz, intensity = spec.get_peaks()
             p = intensity.argsort() # sort by intensity to plot highest on top
             rt = np.full([mz.shape[0]], spec.getRT(), float)
             plt.scatter(rt, mz[p], c = intensity[p], cmap = 'afmhot_r', s=marker_size,
                         norm=colors.LogNorm(exp.getMinInt()+1, exp.getMaxInt()))
     print('plot 2D...')
     plt.clim(exp.getMinInt()+1, exp.getMaxInt())
     plt.xlabel('time (s)')
     plt.ylabel('m/z')
     plt.colorbar()
     #plt.figure(figsize=(10,7))
     #plt.show() # slow for larger data sets
#     plt.savefig("test-2D.png", dpi=300)

def plot_spectr_1D(spectrum):
     print('plot 1D...')
     # plot every peak in spectrum and annotate with it's m/z
     for mz, i in zip(*spectrum.get_peaks()):
         plt.plot([0, i], [mz, mz], color = 'black')
         if i>7.0 : plt.text(i+2, mz, "%.4f" % mz)
     # for the title add RT and Precursor m/z if available
     title = ''
#     if spectrum.getRT() >= 0:
#         title += 'RT: ' + str(spectrum.getRT())
#     if len(spectrum.getPrecursors()) >= 1:
#         title += '   Precursor m/z: ' + str(spectrum.getPrecursors()[0].getMZ())
     plt.title(title)
     plt.xlabel('intensity')
     plt.ylabel('m/z')
#     plt.ylim(bottom=0)
     plt.xlim(left=0, right=119)
#     plt.show()
#     plt.savefig("test-1D.png", dpi=300)

def plot_chrom(m_rt, m_int):
     print('plot 1D chrom...')
#     for rt, i in chrom:
     plt.plot(m_rt, m_int, color = 'black')
         #if i>10.0 : plt.text(i+2, mz, "%.4f" % mz)
     # for the title add RT and Precursor m/z if available
     title = ''
     plt.title(title)
     plt.ylabel('intensity')
     plt.xlabel('time (s)')
#     plt.figure(figsize=(10,3))

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
    print('integrate m/z...')
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
#                    m_mz[ind]=(m_mz[ind]+mz)/2
                    m_mz[ind]=(m_mz[ind]*m_int[ind]+mz*i)/(m_int[ind]+i)
                    m_int[ind]+=i
    int_max = max(m_int)
    m_int = [100.0*x/int_max for x in m_int]
    s.set_peaks((m_mz, m_int))
#    r_ms.addSpectrum(s)
#    print(m_mz)
    return s

def integrate_int(src_ms):
    print('integrate int...')
    m_rt = []
    m_int = []
    for s in src_ms :
        if s.getMSLevel()==1 :
            m_rt.append(s.getRT())
            sum_int = 0.0
            for mz, i in zip(*s.get_peaks()): sum_int+=i
            m_int.append(sum_int)
    return m_rt, m_int

fname = sys.argv[1]

#from urllib.request import urlretrieve
#gh = "https://raw.githubusercontent.com/OpenMS/pyopenms-extra/master"
#urlretrieve (gh + "/src/data/FeatureFinderMetaboIdent_1_input.mzML", "test.mzML")

exp = MSExperiment()
MzMLFile().load(fname, exp)
r_mz = integrate_ms1(exp)
m_rt, m_int = integrate_int(exp)

#widths = [1.5, 3]
#heights = [2, 3]
fig=plt.figure(figsize=(15,5))
#spec = fig.add_gridspec(ncols=2, nrows=2, width_ratios=widths, height_ratios=heights)
#fig, axs = plt.subplots(2,2)

#fig, axs = plt.subplots(2, 2, gridspec_kw={'width_ratios': [2, 1], 'height_ratios': [1, 2]})
#axs[1,0].set_visible(False)

##plt.subplot(2,2,1)
##plot_chrom(m_rt, m_int)

plt.subplot(1,2,1)
plot_spectr_2D(exp)

plt.subplot(1,2,2)
plot_spectr_1D(r_mz)

plt.tight_layout()
plt_name=fname.replace(".mzML", "-var2.png", 1)
print('save plot...')
plt.savefig(plt_name, dpi=300)

