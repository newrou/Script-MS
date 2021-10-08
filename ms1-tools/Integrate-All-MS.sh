#!/bin/bash

for i in `find ./ -name "*.mzData"`
do
  name=`basename $i .mzData`
  FileConverter -in $name.mzData -out tmp-ms.mzML
  FileFilter -in tmp-ms.mzML -out tmp-ms.mzML -int 100: -rt 35:47 -mz 40:2000
  FileConverter -in tmp-ms.mzML -out $name.dta2d
#  php Integrate-MS.php $name.dta2d > tmp-ms.dat
#  gnuplot Plot-Integrated-MS.gnu
#  mv tmp-ms.dat $name.dat
#  mv tmp-ms.svg $name.svg
  rm tmp-ms.mzML
done

echo "#SEC	MZ	INT" > All.dta2d
cat *.dta2d | grep -v "SEC" >> All.dta2d
php Integrate-MS.php All.dta2d > tmp-ms.dat
gnuplot Plot-Integrated-MS.gnu
mv tmp-ms.dat All.dat
mv tmp-ms.svg All.svg
rm *.dta2d
