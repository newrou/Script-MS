#!/bin/bash

#php Correct-MZ-MS.php -k 1.00039761 -b -0.1109976 -v < 1.csv 2> 1r.log | tee 1r.csv | php Clear-MS.php -v 2> 1rr.log > 1rr.csv

#mkdir -p Mol_images
#php Decode-MS.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" -f 2.csv | tee 2tpd.html
#php Decode-MS.php -t remove -p remove -d add -g "0:30;3000:100;4800:100;6000:30" -f 2.csv | tee 2d.html

#php Intersect-MS.php AMO-APCI.csv AME-ACTN-APCI.csv > Intersect-AMO--AME-ACTN-APCI.csv
#php Intersect-MS.php AMO-APCI.csv AME-ETAC-APCI.csv > Intersect-AMO--AME-ETAC-APCI.csv

#php Diff-MS.php AMO-APCI.csv AME-ACTN-APCI.csv > Diff-AMO--AME-ACTN-APCI.csv
#php Diff-MS.php AMO-APCI.csv AME-ETAC-APCI.csv > Diff-AMO--AME-ETAC-APCI.csv
#php Diff-MS.php AME-ACTN-APCI.csv AMO-APCI.csv > Diff-AME-ACTN--AMO-APCI.csv
#php Diff-MS.php AME-ETAC-APCI.csv AMO-APCI.csv > Diff-AME-ETAC--AMO-APCI.csv
#php Diff-MS.php AMO-APCI.csv AME-ACTN-APCI.csv AME-ETAC-APCI.csv > Diff-AMO--all-APCI.csv

#php Correct-MZ-MS.php -k 1.00039761 -b -0.1109976 < AMO-APCI.csv | php Compact-MS.php | php Print-MS-html.php -t add -p add -d add -g "0:30;3000:100;4800:100;6000:30" > AMO-APCI.html
