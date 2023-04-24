#!/usr/bin/env python3

#from pyopenms import *
import csv
import sys
import math

def comp_str(x) : 
    if len(x)>2 : 
        return('%4d  %9.4f  %e' % (x['rt'], x['mz'], x['intensity'])) 
    else : return('')

def read_ms(fname) :
    lst = []
    with open(fname) as csvfile:
        reader = csv.DictReader(csvfile, delimiter='\t')
        for row in reader:
            lst.append({'rt': float(row['rt']), 'mz': float(row['mz']), 'intensity': float(row['intensity']), 'src': row})
    return(lst)

def row_to_str(row) :
    r = ""
    for x in row.items() :
        r = r + str(x[1]) + '\t'
    return(r)

def find_ms(c, m) :
    for x in m :
        if math.fabs(c['mz']-x['mz'])/c['mz'] < 0.0001 : return(comp_str(x))
    return("Not found!")

ms1 = sorted(read_ms(sys.argv[1]), key=lambda x: x['mz'])
ms2 = sorted(read_ms(sys.argv[2]), key=lambda x: x['mz'])


r = ""
for x in ms1[0]['src'].items() :
    r = r + str(x[0]) + '\t'
#print(r, 'Compare', sys.argv[1], ' ~ ', sys.argv[2])
print(r, 'comment')

for x in ms1 :
    r = find_ms(x, ms2)
    print(row_to_str(x['src']), r)
#    if r=="Not found!" : print(comp_str(x), '  ==>  ', r)
#    print(comp_str(x), '  ==>  ', r)
