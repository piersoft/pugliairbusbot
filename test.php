<?php
$partenza="Bari-Matera";
$ore="5";
$minuti="11";
$datad="15/01/2016";

$mezzo="1";
$dettaglio=$_POST["d"];
//$testo=$_POST["q"];
//$testo="bm%11/01/2016?5-11";
//$datad=extractString($testo,"%","?");
//$ore=extractString($testo,"?","-");
//$minuti=substr($testo, -2, 2);
//$partenza=substr($testo, 0, 2);
//  echo $partenza;
//  echo $minuti;

function extractString($string, $start, $end) {
    $string = " ".$string;
    $ini = strpos($string, $start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

if ($partenza=="bm"||$partenza=="BM"){
    $partenza="partenza=36&arrivo=5";
}else{
    $partenza="partenza=5&arrivo=36";

}
if (intval($ore)<10){
  $ore="0".$ore;
}
if (intval($minuti)<10){
  $minuti="0".$minuti;
}
$datad=str_replace("/","%2F",$datad);
$filecsv = fopen('db/falair.txt', 'w+');
fwrite($filecsv,file_get_contents("http://pugliairbus.aeroportidipuglia.it/Corse_MATERA_103_21/01/2016_BARI_101.aspx")); // Write information to the file
   fclose($filecsv);
   /*
extract($_POST);
$url = 'http://pugliairbus.aeroportidipuglia.it/';
$ch = curl_init();
$options="";
$options="Corse_MATERA_103_19/01/2016_BARI_101.aspx";
$file = fopen('db/falair.txt', 'w+'); //da decommentare se si vuole il file locale
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/text','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
curl_setopt($ch, CURLOPT_FILE, $file);
curl_exec($ch);
curl_close($ch);
*/
	$html = file_get_contents("db/falair.txt");
  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
//  $html=str_replace("</td>","</td>;",$html);
  $html=str_replace("</th>","</th>;",$html);
    $html=str_replace("</tr>","</tr>;",$html);
//  $html=str_replace("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"613\">","<prova>",$html);
  $html=str_replace("<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"586\"","<prova>",$html);

//  $html=str_replace("<tr class=\"dettagli\">","",$html);
;
$doc = new DOMDocument;
$doc->loadHTML($html);

$xpa    = new DOMXPath($doc);
$count=0;
$rows = $doc->getElementsByTagName('prova');
foreach($rows as $row) {
    $values = array();
    foreach($row->childNodes as $cell) {
        $values[] = $cell->textContent;
    }
    $data[] = $values;
    $count++;
}

$allertatmp =[];
$allerta =[];
$countr=0;
//$text="Numero Bus,Partenza,Ora,Arrivo,Ora,Durata,Garantito in caso di sciopero,Info\n";
$text="";
for ($i=0;$i<$count;$i++){
  array_push($allerta,$data[$i]);
$countr++;
}
//var_dump($allerta);
//var_dump($allerta);
for ($tt=0;$tt<20;$tt++){
for ($t=1;$t<20;$t++){
  //array_push($allertatmp, explode(';',$allerta[$tt][$t]));
//  preg_replace( "", "", $allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("  ","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("   ","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("    ","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("

    						","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("
  						","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("							 						","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("					 ","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace(array("\r\n", "\r", "\n"),"",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("\n","",$allerta[$tt][$t]);
$allerta[$tt][$t]=str_replace("<br>","",$allerta[$tt][$t]);

$text .=$allerta[$tt][$t];
  //$text .=$allerta[$tt][$t].",";
//var_dump(explode(';',$allerta[$tt][$t]));
}
}
$text=str_replace("						","",$text);
$text=str_replace("					","",$text);
$text=str_replace("				 													","",$text);
$text=str_replace("				 	","",$text);
$text=str_replace("    ","",$text);
$text=str_replace("   ","",$text);
$text=str_replace("  ","",$text);
$text=str_replace("Â","",$text);
$text=str_replace(";",",",$text);
$text=str_replace(",,","",$text);
$text=str_replace("Info","\n",$text);
$text=str_replace("VERIFICA DISPONIBILITA'","\n",$text);
$text=str_replace("PARTENZA da","PARTENZA da ",$text);
$text=str_replace("ORARIO","ORARIO ",$text);
$text=str_replace("ARRIVO a","ARRIVO a ",$text);



$text=utf8_decode($text);

//echo $text;
//header('Content-type: text/csv');
//header("Content-Disposition: attachment;filename=delibere.csv");

//echo $text;
//$allerta .=preg_replace('/\s+?(\S+)?$/', '', substr($allerta, 0, 400))."....\n";
$filecsv = fopen('db/falair.csv', 'w+');
fwrite($filecsv,$text); // Write information to the file
   fclose($filecsv);

$csv = array_map('str_getcsv', file('db/falair.csv'));
//var_dump($csv);
$countcsv = 0;
foreach($csv as $data=>$csv1){
  $countcsv = $countcsv+1;
}
//echo $count;
for ($i=0;$i<$countcsv;$i++){
  $homepage .="\n</br>";
  $homepage .=$csv[$i][0]."\n</br>";
  $homepage .=$csv[$i][1]."\n</br>";
  $homepage .=$csv[$i][2]."\n</br>";
  $homepage .=$csv[$i][3]."\n</br>";

//  $link="http://www.piersoft.it/falbot/dettaglio.php?".$csv[$i][7]."\n";
//  $homepage .=str_replace(" ","",$link);


}

//$partenza="Bari-Matera";
//$ore="5";
//$minuti="11";
//$datad="11/01/2016";

/*
extract($_POST);
$url = 'http://ferrovieappulolucane.it/wp-admin/admin-ajax.php';
$ch = curl_init();
$dettaglio=str_replace(" ","",$dettaglio);
$options=$dettaglio;
//$options="action=get_corsa&id_corsa=31090&id_partenza=16287&id_arrivo=16414";
//$options='tipo_mezzo='.$mezzo.'&'.$partenza.'&data='.$datad.'&ore='.$ore.'&minuti='.$minuti;
$file = fopen('db/faldettaglio.txt', 'w+'); //da decommentare se si vuole il file locale
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','Accept: Application/json','X-Requested-With: XMLHttpRequest','Content-Type: application/octet-stream','Content-Type: application/download','Content-Type: application/force-download','Content-Transfer-Encoding: binary '));
curl_setopt($ch,CURLOPT_POSTFIELDS,$options );
curl_setopt($ch, CURLOPT_FILE, $file);
curl_exec($ch);
curl_close($ch);

$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A&key=12EaiyVJBbpoFWK9V8i1lYBVNhulu9ikJuaVwKWLuNxo&gid=0";

$csv = array_map('str_getcsv', file($urlgd));
//var_dump($csv);
$countcsv = 0;
foreach($csv as $data=>$csv1){
  $countcsv = $countcsv+1;
}
//echo $countcsv;
for ($i=1;$i<$countcsv;$i++){
  $homepage .="\n";
  $homepage .="Fermata: ".$csv[$i][0]."\n";
  $homepage .="Arrivo: ".$csv[$i][1]."\n";
  $homepage .="Partenza: ".$csv[$i][2]."\n";
  $homepage .="F. a richiesta: ".$csv[$i][3]."\n";
  $homepage .="Note: ".$csv[$i][4]."\n";
  $homepage .="____________\n";

}
*/
echo $homepage;

?>
