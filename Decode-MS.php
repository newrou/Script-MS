<?php

include 'MS-Lib.php';

function DecodeMSLine($Ref)
{
 $r = array();
// $r = sprintf("<table border=0>\n");
 $fs = file_get_contents($Ref);
 $lines = file($Ref);
 foreach ($lines as $line_num => $line)
   if( ! (stristr($line, 'metabo_info')===FALSE) ) {
    preg_match_all("/<tr[^>]*>.+<\/tr>/Ui", $line, $mtr, PREG_PATTERN_ORDER);
    foreach($mtr[0] as $tr) {
	preg_match_all("/<t[dh][^>]*>.+<\/t[dh]>/Ui", $tr, $mtd , PREG_PATTERN_ORDER);
	$molid = strip_tags($mtd[0][0]);
	$name = strip_tags($mtd[0][2]);
	$formula = strip_tags($mtd[0][3]);
//	if (!file_exists("Mol_images/$molid.png")) copy("https://metlin.scripps.edu/Mol_images/$molid.png","Mol_images/$molid.png");
	if (!file_exists("Mol_images/$molid.png")) copy("http://metlin.scripps.edu/Mol_images/$molid.png","Mol_images/$molid.png");
	$r[] = array('FORMULA'=>$formula, 'NAME'=>$name, 'MOLID'=>$molid);
//	$r = sprintf("%s <p>%s %s <img src=\"Mol_images/%s.png\"></p>\n", $r, $formula, $name, $molid);
//	$r = sprintf("%s <tr> <td>%s</td> <td>%s</td> <td><img width=\"100%%\" src=\"Mol_images/%s.png\"></td> </tr>\n", $r, $formula, $name, $molid);
    }
 }
// $r = sprintf("%s </table>\n", $r);
 return $r;
}

$tox='add'; $pep='add'; $drug='add'; $grad='0:30;3000:100;4800:100;6000:30';
for($i=0; $i<count($argv); $i++) {
    if($argv[$i]=='-f') { $fname=$argv[$i+1]; $MSLine=ReadMS($fname); $i++; continue; }
    if($argv[$i]=='-t') { $tox=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-p') { $pep=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-d') { $drug=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-g') { $grad=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-v') { $v=true; continue; }
}
if(count($MSLine)<1) $MSLine=ReadMS($argv[1]);

//$stderr = fopen('php://stderr', 'w');
//if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
//fclose($stderr);

//$n=0;
//for($i=0; $i<count($MSLineSrc); $i++) if( $MSLineSrc[$i]['EX']==false ) {$MSLine[$n]=$MSLineSrc[$i];$n++;}
//printf("MSLine: %ld\n",$n);

$data_m=array();
for($i=0; $i<count($MSLine); $i++) {
//    $data_m[$i]=0.0+$MSLine[$i]['INT'];
//    $data_m[$i]=0.0+$MSLine[$i]['rt'];
    $data_m[$i]=0.0+$MSLine[$i]['mz'];
}

//array_multisort($data_m, SORT_NUMERIC, SORT_DESC, $MSLine);
array_multisort($data_m, SORT_NUMERIC, SORT_ASC, $MSLine);
//var_dump($data_m);
//var_dump($MSLine);

$MSL = array();$j=0;
for($i=0; $i<count($MSLine); $i++) {
    $MZmin=$MSLine[$i]['mz']*0.999950;
    $MZmax=$MSLine[$i]['mz']*1.000050;
    if($j>0 && $MSL[$j-1]['MZ']<$MZmax && $MSL[$j-1]['MZ']>$MZmin) {$MSL[$j-1]['N'][] = $i;}
    else {
        $MSL[$j] = array('N'=>array(), 'MZ'=>$MSLine[$i]['mz']);
	$MSL[$j]['N'][] = $i;
	$j++;
    }
//    printf("%.4f < %.4f <%.4f\n", $MZmin, $MSL[$j-1]['MZ'], $MZmax);
}

// var_dump($MSL);

printf("<!DOCTYPE HTML>\n");
printf("<html>\n");
printf("<head>\n");
printf("  <meta charset=\"utf-8\">\n");
printf("  <style>\n");
printf("    td {\n");
printf("      vertical-align: top;\n");
printf("    }\n");
printf("    th {\n");
printf("      cursor: pointer;\n");
printf("      vertical-align: top;\n");
printf("    }\n");
printf("    th:hover {\n");
printf("      background: yellow;\n");
printf("    }\n");
printf("  </style>\n");
printf("</head>\n");
printf("<body>\n");

printf("<table border=1 id=grid>\n<tr> <td data-type=number>N</td>");
// printf(" <th data-type=number>Mz</th> <td>Metlin ref</td>");
// printf(" <th data-type=number>Corrected Mz</th> <td>Metlin ref</td>");
printf(" <td data-type=number>M/Z</td> ");
// printf(" <th data-type=number>Quality</th> <th data-type=number>Int.</th> <th data-type=number>RTS,sec</th> <th data-type=number>RTE, sec</th> <th data-type=number>ACN, %%</th> </tr>\n");
//printf(" <td data-type=number>Int.</td> <td data-type=number>RTS,sec</td> <td data-type=number>ACN, %%</td> <td>Decode</td> </tr>\n");
//printf(" <td data-type=number>Int.</td> <td data-type=number>RTS,sec</td> <td>Formula / Name</td> <td>Image</td> </tr>\n");
printf(" <td data-type=number>Интенсивность</td> <td data-type=number>Время выхода, с (%% ACN)</td> <td>Формула / Название</td> <td>Структура</td> </tr>\n");
for($i=0; $i<count($MSL); $i++) {
    $Line = $MSL[$i];

    $rts=10000; $rte=0; $n=count($Line['N']);
    for($j=0; $j<$n; $j++) {
	$s=$MSLine[ $Line['N'][$j] ]['rt_start'];
	$e=$MSLine[ $Line['N'][$j] ]['rt_end'];
	if($s<$rts) $rts=$s;
	if($e>$rte) $rte=$e;
    }

    $RT=($rts+$rte)/2;
    $ACN=RT2ACN($RT,$grad);

    $MZs=$MSLine[ $Line['N'][0] ]['mz']; $MZe=$MZs;
    for($j=1; $j<$n; $j++) {
	$m=$MSLine[ $Line['N'][$j] ]['mz'];
	if($m<$MZs) $MZs=$m;
	if($m>$MZe) $MZe=$m;
    }
    $MZCorrect=($MZs+$MZe)/2;
    $MZmin=$MZs*0.999970;
    $MZmax=$MZe*1.000030;

    $INT=0; for($j=0; $j<$n; $j++) $INT+=$MSLine[ $Line['N'][$j] ]['intensity'];

//#FEATURE,rt,mz,intensity,charge,width,quality,rt_quality,mz_quality,rt_start,rt_end
// https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min=714.0&mass_max=715.0&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid=remove&drug=remove&toxinEPA=remove&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#
    $ref='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.$MZmin.'&mass_max='.$MZmax.'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refH='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-1.0073).'&mass_max='.($MZmax-1.0073).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refNa='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-22.9893).'&mass_max='.($MZmax-22.9893).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refK='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-38.9632).'&mass_max='.($MZmax-38.9632).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';

    $DecodedMSLine = DecodeMSLine($refH);
    $span=""; $n=count($DecodedMSLine);
    if($n>0) $span=sprintf(" rowspan=%ld", count($DecodedMSLine));
    printf("<tr>\n");
    printf(" <td%s>%ld</td>", $span, $i+1);
    printf(" <td%s>%.4f</td>", $span, $MZCorrect);
    printf(" <td%s>%.0f</td>", $span, $INT);
    printf(" <td%s>%.0f (%.2f)</td>\n", $span, $RT, $ACN);

    if($n>0) 
	foreach($DecodedMSLine as $j => $line) {
	    if($j>0) printf("<tr>");
	    printf(" <td><p>%s</p><p>%s</p></td> <td><img width=\"100%%\"src=\"Mol_images/%s.png\"></td> </tr>\n", $line['FORMULA'], $line['NAME'], $line['MOLID'] );
	}
    if($n==0) printf(" <td></td> <td></td> </tr>\n");
}
printf("</table>\n");
printf("</body>\n");
printf("</html>\n");

?>
