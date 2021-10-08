
cat k-start.toppas > k.toppas

list=`ls *.mzData`
for i in $list
do
 Name=`basename $i .mzData`
 echo "        <LISTITEM value=\""$i"\"/>" >> k.toppas
done
cat k-end.toppas >> k.toppas

ExecutePipeline -out_dir . -threads 3 -in k.toppas
mv TOPPAS_out/007-FileFilter-out/* .
mv TOPPAS_out/009-TextExporter-out/* .
rm TOPPAS.log
rm k.toppas
rm -rvf TOPPAS_out/

l=`ls *.csv`
for i in $l
do 
 name=`basename $i .csv`
 echo $name
 php Print-MS-html.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" < $i | tee $name.html
done

