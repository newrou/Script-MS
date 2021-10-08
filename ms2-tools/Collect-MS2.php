<?php

#include 'MS-Lib.php';

function ReadMS2($fname, $Ev, &$MS2Lines) {
    if (($fin = fopen($fname, 'r')) !== FALSE) {
	while (($data = fgetcsv($fin, 1000, ";")) !== FALSE) {
	    $num = count($data); if($num<5) continue;
	    $mz=0.0+$data[1];
	    $int=0.0+$data[2]; if($mz<10.0) continue;
	    $pint=0.0+$data[3];
	    if( $pint>10.0 ) $comment=sprintf("%.2f",$pint); else $comment="";
	    $MS2Lines[] = array( 'mz'=>$mz, 'int'=>$int, 'pint'=>$pint, 'collision'=>$Ev, 'comment'=>$comment, 'ex'=>false );
	}
	fclose($fin);
    }
}

function SaveMS2($fname, $MS2Lines) {
    $fout = fopen($fname, 'w');
    for($i=0; $i<count($MS2Lines); $i++) {
	$ms = $MS2Lines[$i];
	$mz = $ms['mz'];
	$int = $ms['int'];
	$pint = $ms['pint'];
	$collision = $ms['collision'];
	$comment = $ms['comment'];
	if($ms['ex']==false) fprintf($fout, "%3ld; \t%9.4f; \t%7.0f; \t%5.1f; \t%5.2f; \t\"%s\";\n", $i, $mz, $int, $pint, $collision, $comment);
    }
    fclose($fout);
}

function SavePlotMS2($fname, $MS2Lines, $trace) {
    $fout = fopen($fname, 'w');
    $collision = array(0.00, 0.01, 0.10, 1.00, 10.00, 20.00, 40.00);
    fprintf($fout,"# Ev;");
    foreach($trace as $mz) fprintf($fout," \t%9.4f;",$mz);
    fprintf($fout,"\n");
    foreach($collision as $Ev) {
	fprintf($fout,"%5.2f;", $Ev);
	foreach($trace as $mz) {
	    $pint=0.0;
	    for($i=0; $i<count($MS2Lines); $i++) {
		$ms=$MS2Lines[$i];
		if( $ms['collision']==$Ev && abs($ms['mz']-$mz)<0.1 ) {$pint=$ms['pint'];break;}
	    }
	    fprintf($fout, "\t%9.4f;", $pint);
	}
	fprintf($fout,"\n");
    }
    fclose($fout);
}

function GetCollision($str) {
    if (strpos($str, '-0Ev-') !== false) return 0.0;
    if (strpos($str, '-0_01Ev-') !== false) return 0.01;
    if (strpos($str, '-0_1Ev-') !== false) return 0.1;
    if (strpos($str, '-1Ev-') !== false) return 1.0;
    if (strpos($str, '-10Ev-') !== false) return 10.0;
    if (strpos($str, '-20Ev-') !== false) return 20.0;
    if (strpos($str, '-40Ev-') !== false) return 40.0;
}

$ms = array();
$v = false;
$stderr = fopen('php://stderr', 'w');

for($i=1; $i<count($argv); $i++) {
    if($argv[$i]=='-v') { $v=true; continue; }
    if($argv[$i]=='-t') { $strace=$argv[$i+1]; $i++; continue; }
    ReadMS2($argv[$i], GetCollision($argv[$i]), $ms);
}

$trace = Array();
$mtrace = explode(";", $strace);
foreach($mtrace as $tr) if(strlen($tr)>1) $trace[] = (double)$tr;

if(count($trace)>0) {
    for($i=0; $i<count($ms); $i++) {
	$flag=true;
	foreach($trace as $tr) if(abs($tr-$ms[$i]['mz'])<0.3) {$flag=false; break;}
	$ms[$i]['ex']=$flag;
    }
}

$data_m=array();
for($i=0; $i<count($ms); $i++) {
    $data_m[$i]=$ms[$i]['collision'];
}
//array_multisort($data_m, SORT_NUMERIC, SORT_DESC, $MSLine);
array_multisort($data_m, SORT_NUMERIC, SORT_ASC, $ms);

if(count($trace)<1) SaveMS2('php://stdout', $ms);
else SavePlotMS2('php://stdout', $ms, $trace);

fclose($stderr);

?>
