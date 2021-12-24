<?php

include 'MS-Lib.php';

$ms = array();
$rs = array();
$v = false;

$tox='add'; $pep='add'; $drug='add'; $grad='0:30;3000:100;4800:100;6000:30';
for($i=0; $i<count($argv); $i++) {
    if($argv[$i]=='-t') { $tox=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-p') { $pep=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-d') { $drug=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-g') { $grad=$argv[$i+1]; $i++; continue; }
    if($argv[$i]=='-v') { $v=true; continue; }
}

$MSLine = ReadMSnew('php://stdin');
$stdout = fopen('php://stdout', 'w');
$stderr = fopen('php://stderr', 'w');

for($i=0; $i<count($ms); $i++)
    for($j=0; $j<count($ms[$i]); $j++) {
	if( FindMSLine( $rs, $ms[$i][$j] ) == true ) continue;
	$rs[] = $ms[$i][$j];
//    if($v) fprintf($stderr,"mz %.4f => %.4f\n", $mz, $MassSpectr[$i]['mz'] );
    }

$data_m=array();
for($i=0; $i<count($MSLine); $i++) {
//    $data_m[$i]=0.0+$MSLine[$i]['intensity'];
    $data_m[$i]=0.0+$MSLine[$i]['rt_start'];
}
//array_multisort($data_m, SORT_NUMERIC, SORT_DESC, $MSLine);
array_multisort($data_m, SORT_NUMERIC, SORT_ASC, $MSLine);

fprintf($stdout,"<!DOCTYPE HTML>\n");
fprintf($stdout,"<html>\n");
fprintf($stdout,"<head>\n");
fprintf($stdout,"  <meta charset=\"utf-8\">\n");
fprintf($stdout,"  <style>\n");
fprintf($stdout,"    th {\n");
fprintf($stdout,"      cursor: pointer;\n");
fprintf($stdout,"    }\n");
fprintf($stdout,"    th:hover {\n");
fprintf($stdout,"      background: yellow;\n");
fprintf($stdout,"    }\n");
fprintf($stdout,"  </style>\n");
fprintf($stdout,"</head>\n");
fprintf($stdout,"<body>\n");

fprintf($stdout,"<table border=1 id=grid>\n<tr> <th data-type=number>N</th> <th data-type=number>Mz</th> <td>Metlin ref</td> <th data-type=number>Charge</th> ");
fprintf($stdout,"<th data-type=number>Quality</th> <th data-type=number>Int.</th> <th data-type=number>RTS,sec</th> <th data-type=number>RTE, sec</th> <th data-type=number>ACN, %%</th> </tr>\n");
for($i=0; $i<count($MSLine); $i++) {
    fprintf($stdout,"<tr><td>%ld</td>", $i);

    $MZ=$MSLine[$i]['mz'];
    $MZmin=$MSLine[$i]['mz']*0.999970;
    $MZmax=$MSLine[$i]['mz']*1.000030;
    $RT=($MSLine[$i]['rt_start']+$MSLine[$i]['rt_end'])/2;
//    $ACN=RT2ACN($RT,$grad);
    $ACN='50%';

    $ref='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.$MZmin.'&mass_max='.$MZmax.'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refH='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-1.0073).'&mass_max='.($MZmax-1.0073).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refNa='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-22.9893).'&mass_max='.($MZmax-22.9893).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refK='https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min='.($MZmin-38.9632).'&mass_max='.($MZmax-38.9632).'&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid='.$pep.'&drug='.$drug.'&toxinEPA='.$tox.'&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false#';
    $refSimple='https://metlin.scripps.edu/simple_search_result.php?mass_mid='.$MZ.'&mass_tol=50&mass_mode=1&AminoAcid=add&toxinEPA=add&keggIDFilter=add&escElementList=&escMinNumElementList=&escMaxNumElementList=&adducts=M%2BH,M%2BNa,M%2BK#';
// https://metlin.scripps.edu/advanced_search_result.php?molid=&mass_min=240.1&mass_max=240.9&name=&formula=&cas=&kegg=&smilefile=&msmspeaks_min=&AminoAcid=add&drug=add&toxinEPA=add&keggIDFilter=add&escElementList=&escMinNumElementList=&escMaxNumElementList=&smilesExactMatchCheckBox=false&nameExactMatchCheckBox=false

    fprintf($stdout,"<td>%.4f</td> <td> <a href=\"%s\">[M]<sup>+</sup></a> <a href=\"%s\">[M+H]<sup>+</sup></a> <a href=\"%s\">[M+Na]<sup>+</sup></a> <a href=\"%s\">[M+K]<sup>+</sup></a> <a href=\"%s\">[Simple]</a> </td> ", $MSLine[$i]['mz'], $ref, $refH, $refNa, $refK, $refSimple);
    fprintf($stdout," <td>%.0f</td> <td>%.2f</td> <td>%.0f</td> <td>%.0f</td> <td>%.0f</td> <td>%.0f</td> </tr>\n", $MSLine[$i]['charge'], $MSLine[$i]['quality'], $MSLine[$i]['intensity'], $MSLine[$i]['rt_start'], $MSLine[$i]['rt_end'], $ACN);
}
fprintf($stdout,"</table>\n");


fprintf($stdout," <script>\n");
fprintf($stdout,"    var grid = document.getElementById('grid');\n");
fprintf($stdout,"    grid.onclick = function(e) {\n");
fprintf($stdout,"      if (e.target.tagName != 'TH') return;\n");
fprintf($stdout,"      sortGrid(e.target.cellIndex, e.target.getAttribute('data-type'));\n");
fprintf($stdout,"    };\n");
fprintf($stdout,"\n");
fprintf($stdout,"    function sortGrid(colNum, type) {\n");
fprintf($stdout,"      var tbody = grid.getElementsByTagName('tbody')[0];\n");
fprintf($stdout,"      var rowsArray = [].slice.call(tbody.rows);\n");
fprintf($stdout,"      var compare;\n");
fprintf($stdout,"      switch (type) {\n");
fprintf($stdout,"        case 'number':\n");
fprintf($stdout,"          compare = function(rowA, rowB) {\n");
fprintf($stdout,"            return rowA.cells[colNum].innerHTML - rowB.cells[colNum].innerHTML;\n");
fprintf($stdout,"          };\n");
fprintf($stdout,"          break;\n");
fprintf($stdout,"        case 'string':\n");
fprintf($stdout,"          compare = function(rowA, rowB) {\n");
fprintf($stdout,"            return rowA.cells[colNum].innerHTML > rowB.cells[colNum].innerHTML;\n");
fprintf($stdout,"          };\n");
fprintf($stdout,"          break;\n");
fprintf($stdout,"      }\n");
fprintf($stdout,"      rowsArray.sort(compare);\n");
fprintf($stdout,"      grid.removeChild(tbody);\n");
fprintf($stdout,"      for (var i = 0; i < rowsArray.length; i++) {\n");
fprintf($stdout,"        tbody.appendChild(rowsArray[i]);\n");
fprintf($stdout,"      }\n");
fprintf($stdout,"      grid.appendChild(tbody);\n");
fprintf($stdout,"    }\n");
fprintf($stdout," </script>\n");
fprintf($stdout,"\n");
fprintf($stdout,"</body>\n");
fprintf($stdout,"</html>\n");

fclose($stderr);
fclose($stdout);

?>
