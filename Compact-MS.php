<?php

include 'MS-Lib.php';

$rs = array();
$v = false;

for($i=1; $i<count($argv); $i++) {
    if($argv[$i]=='-v') { $v=true; continue; }
}

$ms = ReadMS('php://stdin');

$stderr = fopen('php://stderr', 'w');
for($i=0; $i<count($ms); $i++) {
    if( FindMSLine( $rs, $ms[$i] ) == true ) continue;
    $rs[] = $ms[$i];
//    if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
}
fclose($stderr);

SaveMS('php://stdout', $rs);

?>
