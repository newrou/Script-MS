<?php

include 'MS-Lib.php';

$v = false;
for($i=0; $i<count($argv); $i++) {
    if($argv[$i]=='-v') { $v=true; continue; }
}

$stderr = fopen('php://stderr', 'w');

$MSLines = ReadMS('php://stdin');

// Clear 922.01
for($i=0; $i<count($MSLines); $i++) if( Abs($MSLines[$i]['mz']-922.01)<0.03) $MSLines[$i]['mz']=true;

// Clear PEG
for($i=0; $i<count($MSLines); $i++)
    for($j=0; $j<count($MSLines); $j++) {
	if( Abs(Abs($MSLines[$i]['mz']-$MSLines[$j]['mz'])-44.0262)<0.02 && Abs($MSLines[$i]['rt']-$MSLines[$j]['rt'])<100 ) {
	$MSLines[$i]['ex']=true; $MSLines[$j]['ex']=true;
	if($v) {
	    fprintf($stderr,"PEG %.4f  \t%.4f", $MSLines[$i]['mz'], $MSLines[$j]['mz']);
	    fprintf($stderr,"\t%.0f=(%.0f-%.0f)", $MSLines[$i]['rt'], $MSLines[$i]['rt_start'], $MSLines[$i]['rt_end']);
	    fprintf($stderr,"\t%.0f=(%.0f-%.0f)\n", $MSLines[$j]['rt'], $MSLines[$j]['rt_start'], $MSLines[$j]['rt_end']);
	}
    }
}

// Clear PPG
for($i=0; $i<count($MSLines); $i++)
    for($j=0; $j<count($MSLines); $j++) {
	if( Abs(Abs($MSLines[$i]['mz']-$MSLines[$j]['mz'])-58.0419)<0.02 && Abs($MSLines[$i]['rt']-$MSLines[$j]['rt'])<1000 ) {
	$MSLines[$i]['ex']=true; $MSLines[$j]['ex']=true;
	if($v) {
	    fprintf($stderr,"PEG %.4f  \t%.4f", $MSLines[$i]['mz'], $MSLines[$j]['mz']);
	    fprintf($stderr,"\t%.0f=(%.0f-%.0f)", $MSLines[$i]['rt'], $MSLines[$i]['rt_start'], $MSLines[$i]['rt_end']);
	    fprintf($stderr,"\t%.0f=(%.0f-%.0f)\n", $MSLines[$j]['rt'], $MSLines[$j]['rt_start'], $MSLines[$j]['rt_end']);
	}
    }
}

SaveMS('php://stdout', $MSLines);

fclose($stderr);

?>
