<?php

include 'MS-Lib.php';

$k = 1.00039761;
$b = -0.1109976;
$v = false;

for($i=0; $i<count($argv); $i++) {
    if($argv[$i]=='-k') { $k=0.0+$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-b') { $b=0.0+$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-v') { $v=true; continue; }
}

$MassSpectr = ReadMS('php://stdin');

$stderr = fopen('php://stderr', 'w');
for($i=0; $i<count($MassSpectr); $i++) {
    $mz = $MassSpectr[$i]['mz'];
    $MassSpectr[$i]['mz'] = $k*$mz + $b;
    if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
}
fclose($stderr);

SaveMS('php://stdout', $MassSpectr);

?>