<?php

include 'MS-Lib.php';

$ms = array();
$ns = 0;
$rs = array();
$v = false;

for($i=1; $i<count($argv); $i++) {
    if($argv[$i]=='-v') { $v=true; continue; }
    $ms[$ns] = ReadMS($argv[$i]);
    $ns++;
}

$stderr = fopen('php://stderr', 'w');
for($i=0; $i<count($ms[0]); $i++) {
    $flag = true;
    for($j=1; $j<count($ms); $j++) {
	if( FindMSLine( $ms[$j], $ms[0][$i] ) == true) { $flag = false; break; }
//    if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
    }
    if( $flag == true) $rs[] = $ms[0][$i];
}
fclose($stderr);

SaveMS('php://stdout', $rs);

?>
