#!/bin/bash

for i in *.mzML
do
    echo $i
    ./Integrate-MS-v1.py $i
done
mv *.png Variant1

for i in *.mzML
do
    echo $i
    ./Integrate-MS-v2.py $i
done
mv *.png Variant2
