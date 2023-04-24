
list=`ls *.csv`
for i in $list
do
  Name=`basename $i .csv`
  echo "Make html: $Name"
  php Print-MS-html.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" < $Name.csv > $Name.html
done
