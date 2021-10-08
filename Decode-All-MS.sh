#!/bin/bash

mkdir -p Mol_images

l=`ls $1*.csv`
for i in $l
do 
 name=`basename $i .csv`
 echo $name
 php Decode-MS.php -t remove -p add -d remove -g "0:30;3000:100;4800:100;6000:30" -f $i | tee $name-decoded.html
done
