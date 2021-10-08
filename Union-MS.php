<?php

include 'MS-Lib.php';

$ms = array();
$rs = array();
$v = false;

for($i=1; $i<count($argv); $i++) {
    if($argv[$i]=='-v') { $v=true; continue; }
    $ms[] = ReadMS($argv[$i]);
}

$stderr = fopen('php://stderr', 'w');
for($i=0; $i<count($ms); $i++)
    for($j=0; $j<count($ms[$i]); $j++) {
	if( FindMSLine( $rs, $ms[$i][$j] ) == true ) continue;
	$rs[] = $ms[$i][$j];
//    if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
    }
fclose($stderr);

SaveMS('php://stdout', $rs);

?>
