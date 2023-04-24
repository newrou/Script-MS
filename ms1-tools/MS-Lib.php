<?php

function ReadMSnew($fname) {
    $MSLines = array();
    $row = 0;
    if (($fin = fopen($fname, 'r')) !== FALSE) {
	while (($data = fgetcsv($fin, 1000, "\t")) !== FALSE) {
	    $num = count($data); if($num<9) continue;
	    $rt=0.0+$data[1];
	    $mz=0.0+$data[2]; if($mz<10.0) continue;
	    $intensity=0.0+$data[3];
	    $charge=0.0+$data[4];
	    $width=0.0+$data[5];
	    $quality=0.0+$data[6];
	    $rt_quality=0.0+$data[7];
	    $mz_quality=0.0+$data[8];
	    $rt_start=0.0+$data[9];
	    $rt_end=0.0+$data[10];
	    $comment="";
	    if ($num>11) $comment=$data[11];
	    $MSLines[$row] = array( 'rt'=>$rt, 'mz'=>$mz, 'intensity'=>$intensity, 'charge'=>$charge, 'width'=>$width, 'quality'=>$quality, 
		'rt_quality'=>$rt_quality, 'mz_quality'=>$mz_quality, 'rt_start'=>$rt_start, 'rt_end'=>$rt_end, 'ex'=>false, 'comment'=>$comment );
	    $row++;
	}
	fclose($fin);
    }
    return $MSLines;
}

function ReadMS($fname) {
    $MSLines = array();
    $row = 0;
    if (($fin = fopen($fname, 'r')) !== FALSE) {
	while (($data = fgetcsv($fin, 1000, ",")) !== FALSE) {
	    $num = count($data); if($num<9) continue;
	    $rt=0.0+$data[1];
	    $mz=0.0+$data[2]; if($mz<10.0) continue;
	    $intensity=0.0+$data[3];
	    $charge=0.0+$data[4];
	    $width=0.0+$data[5];
	    $quality=0.0+$data[6];
	    $rt_quality=0.0+$data[7];
	    $mz_quality=0.0+$data[8];
	    $rt_start=0.0+$data[9];
	    $rt_end=0.0+$data[10];
	    $MSLines[$row] = array( 'rt'=>$rt, 'mz'=>$mz, 'intensity'=>$intensity, 'charge'=>$charge, 'width'=>$width, 'quality'=>$quality, 
		'rt_quality'=>$rt_quality, 'mz_quality'=>$mz_quality, 'rt_start'=>$rt_start, 'rt_end'=>$rt_end, 'ex'=>false );
	    $row++;
	}
	fclose($fin);
    }
    return $MSLines;
}

function SaveMS($fname, $MSLines) {
    $fout = fopen($fname, 'w');
    fprintf($fout,"#FEATURE,rt,mz,intensity,charge,width,quality,rt_quality,mz_quality,rt_start,rt_end\n");
    for($i=0; $i<count($MSLines); $i++) {
	$ms = $MSLines[$i];
	$rt = $ms['rt'];
	$mz = $ms['mz'];
	$intensity = $ms['intensity'];
	$charge = $ms['charge'];
	$width = $ms['width'];
	$quality = $ms['quality'];
	$rt_quality = $ms['rt_quality'];
	$mz_quality = $ms['mz_quality'];
	$rt_start = $ms['rt_start'];
	$rt_end = $ms['rt_end'];
	if($ms['ex']==false)
	    fprintf($fout,"#FEATURE, %f, %f, %.2f, %.0f, %f, %f, %.0f, %.0f, %f, %f\n",$rt,$mz,$intensity,$charge,$width,$quality,$rt_quality,$mz_quality,$rt_start,$rt_end);
    }
    fclose($fout);
}

function RT2ACN($RT, $str) {
    $grad = Array();
    $m = explode(";", $str);
    foreach($m as $pt) {
	list($t, $v) = explode(":", $pt);
	$grad[] = array('t'=>0.0+$t, 'v'=>0.0+$v);
//	printf("grad %s - %s\n", $t, $v);
    }
    $ACN = 0; 
    for($i=1; $i<count($grad); $i++) {
	$t1 = $grad[$i-1]['t'];
	$t2 = $grad[$i]['t'];
	$v1 = $grad[$i-1]['v'];
	$v2 = $grad[$i]['v'];
	if($RT==$t1) { $ACN=$v1; break; }
	if($RT==$t2) { $ACN=$v2; break; }
	if(($RT > $t1) and ($RT < $t2)) { $ACN=$v1+($v2-$v1)*($RT-$t1)/($t2-$t1); break; }
    }
//    $RT=($rts+$rte)/2; $ACN=0;
//    if($RT<=3000) $ACN=30+70*$RT/3000;
//    if($RT>3000 && $RT<=4800 ) $ACN=100;
//    if($RT>4800 && $RT<6000 ) $ACN=100-70*($RT-4800)/1200;
    return $ACN;
}

function CompareMSLine( $a, $b ) {
    $MZmin=$a['mz']*0.99997;
    $MZmax=$a['mz']*1.00003;
    $MZ=$b['mz'];
    if( $MZmin<$MZ && $MZ<$MZmax ) {
	if( $a['rt_start'] < $b['rt_end'] ) return true;
	if( $b['rt_start'] < $a['rt_end'] ) return true;
    }
    return false;
}

function FindMSLine( $m, $l ) {
    $flag = false;
    for( $i=0; $i<count($m); $i++ ) {
	if( CompareMSLine($m[$i],$l)==true ) { $flag=true; break; }
    }
    return $flag;
}

?>