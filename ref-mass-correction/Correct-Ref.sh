
#./TOFCalibration -in SA-4-MeOPh-HFIP-APCI-Pos-85ACN-MSMS-20eV-i5.mzML -out SA-4-MeOPh-HFIP-APCI-Pos-85ACN-MSMS-20eV-i5-Ref-TOF.mzML -ref_masses ref.txt -peak_data -ext_calibrants SA-4-MeOPh-HFIP-APCI-Pos-85ACN-MSMS-20eV-i5.mzML
#InternalCalibration -in SA-4-MeOPh-HFIP-APCI-Pos-85ACN-MSMS-20eV-i5.mzML -out SA-4-MeOPh-HFIP-APCI-Pos-85ACN-MSMS-20eV-i5-Ref.mzML -ref_peaks ref.csv

fname1=$1
fname=`basename $fname1 .mzdata`

FileConverter -in $fname1 -out $fname.mzML
./Correct-Ref.py $fname.mzML $fname-Ref.mzML
FileConverter -in $fname-Ref.mzML -out $fname-Ref.mzdata
rm $fname-Ref.mzML
rm $fname.mzML