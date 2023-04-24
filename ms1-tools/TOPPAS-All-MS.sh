
list=`ls *.mzdata`
for i in $list
do
  Name=`basename $i .mzdata`
  echo "Make: $Name"
#  FileConverter -in $i -out $Name.mzML
#  FeatureFinderCentroided -in $Name.mzML -out $Name.featureXML
#  TextExporter -consensus:sorting_method MZ -in $Name.featureXML -out $Name.tsv
  grep "FEATURE" < $Name.tsv > $Name.csv
  php Print-MS-html.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" < $Name.csv > $Name.html
done
