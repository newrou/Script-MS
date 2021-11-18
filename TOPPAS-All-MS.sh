
list=`ls *.mzdata`
for i in $list
do
 Name=`basename $i .mzdata`
 FileConverter -in $i -out $Name.mzML
 FeatureFinderCentroided -in $Name.mzML -out $Name.featureXML
 TextExporter -in $Name.featureXML -out $Name.tsv
 mv $Name.tsv $Name.csv
# php Print-MS-html.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" < $Name.csv | tee $Name.html
done
