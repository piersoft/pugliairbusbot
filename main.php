<?php
/**
* Telegram Bot Demo per gli orari di Pugliairbus
* @author Francesco Piero Paolicelli @piersoft
*/

include("Telegram.php");
include("settings_t.php");
class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");


	if ($text == "/start" || $text == "Informazioni") {
		$img = curl_file_create('logo.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
		$reply = "Benvenuto. Questo è un servizio automatico (bot da Robot) per gli orari ".NAME.".
		Puoi ricercare gli orari dei Bus per Matera da Bari Aereoporto e viceversa.
		Per cercare i prossimi Bus clicca nel menù in basso o segui la sezione Istruzioni.
		In qualsiasi momento scrivendo /start ti ripeterò questo messaggio.\nQuesto bot è stato realizzato da @piersoft, senza fini di lucro e a titolo di Demo, non ha collegamenti con il progetto Puglia Air Bus, non è ufficiale e l'autore declina da ogni responsabilità. La fonte dati è realtime quella del sito http://pugliairbus.aeroportidipuglia.it/. Il codice sorgente è liberamente riutilizzabile con licenza MIT.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ",new chat started," .$chat_id. "\n";
			file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;
	}	elseif ($text == "/istruzioni" || $text == "Istruzioni") {
		$reply = "Devi seguire alcune semplici regole. Il formato è bm%11/01/2016 dove bm è per Bari->Matera e quindi mb per Matera->Bari, poi il carattere % e la data nel formato gg/mm/aaaa. Attenzione!";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ",istruzioni," .$chat_id. "\n";
		file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;

	}
			elseif ($text == "Bus da BA oggi" || $text == "Bus da MT oggi") {
				$ore=date("H");
				$minuti=$todaym;
				$datad = date("d/m/Y");
				$minuti = date("i");
			//	$mezzo="1";
				if ($text == "Bus da BA oggi"){
					$partenza="bm";
					$mezzo="1";//lascio la scelta se un domani inserisco i treni
				}else{
					$partenza="mb";
					$mezzo="1";
				}
				if ($mezzo=="1"){
						$bus="Bus";
						if ($partenza=="bm"){
								$partenza="Corse_BARI_101_".$datad."_MATERA_103.aspx";
								$reply = "Sto cercando i ".$bus." che partono oggi, nelle prossime ore, da Bari\n";
						}else{
								$partenza="Corse_MATERA_103_".$datad."_BARI_101.aspx";
								$reply = "Sto cercando i ".$bus." che partono oggi, nelle prossime ore, da Matera\n";

						}
					}


									$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
									$telegram->sendMessage($content);

									$datad=str_replace("/","%2F",$datad);
								//	$filecsv = fopen('db/falair.txt', 'w+');
								//	fwrite($filecsv,file_get_contents("http://pugliairbus.aeroportidipuglia.it/".$partenza)); // Write information to the file
								//	   fclose($filecsv);

								//		$html = file_get_contents("db/falair.txt");
										$html = file_get_contents("http://pugliairbus.aeroportidipuglia.it/".$partenza);
									  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
									  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
									  $html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
									  $html=str_replace("</th>","</th>;",$html);
								    $html=str_replace("</tr>","</tr>;",$html);
									  $html=str_replace("<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"586\"","<prova>",$html);

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
									$text="";
									for ($i=0;$i<$count;$i++){
									  array_push($allerta,$data[$i]);
									$countr++;
									}
								for ($tt=0;$tt<20;$tt++){
									for ($t=1;$t<20;$t++){
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
									  $homepage .="\n";
									  $homepage .=$csv[$i][0]."\n";
									  $homepage .=$csv[$i][1]."\n";
									  $homepage .=$csv[$i][2]."\n";
									  $homepage .=$csv[$i][3]."\n";

									}
									$chunks = str_split($homepage, self::MAX_LENGTH);
									foreach($chunks as $chunk) {
										$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
										$telegram->sendMessage($content);

										}

										$content = array('chat_id' => $chat_id, 'text' => "Approfondimenti, disponibilità e biglietti online su http://pugliairbus.aeroportidipuglia.it/",'disable_web_page_preview'=>true);
										$telegram->sendMessage($content);
										$log=$today. ",bus oggi," .$chat_id. "\n";

				file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);
			exit;

}
elseif($location!=null)
		{

			exit;

		}

		elseif(strpos($text,'%') !== false){
				$mezzo="1"; //lascio la scelta se per un futuro devo inserire anche i treni

				function extractString($string, $start, $end) {
						$string = " ".$string;
						$ini = strpos($string, $start);
						if ($ini == 0) return "";
						$ini += strlen($start);
						$len = strpos($string, $end, $ini) - $ini;
						return substr($string, $ini, $len);
				}
				$datad=substr($text, 3, 10);
				$ore="";
				$minuti="";
				$partenza=substr($text, 0, 2);
				$partenza=strtoupper($partenza);

if ($mezzo=="1"){
	$bus="Bus";
	if ($partenza=="BM"){
			$partenza="Corse_BARI_101_".$datad."_MATERA_103.aspx";
			$reply = "Sto cercando i ".$bus." che partono da Bari\n";
	}else{
			$partenza="Corse_MATERA_103_".$datad."_BARI_101.aspx";
			$reply = "Sto cercando i ".$bus." che partono da Matera\n";

	}
}


				$reply .="il giorno ".$datad;
				$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
				$telegram->sendMessage($content);
				//sleep(2);
				$datad=str_replace("/","%2F",$datad);
		//		$filecsv = fopen('db/falairr.txt', 'w+');
		//		fwrite($filecsv,file_get_contents("http://pugliairbus.aeroportidipuglia.it/".$partenza)); // Write information to the file
		//		fclose($filecsv);

		//			$html = file_get_contents("db/falairr.txt");
					$html = file_get_contents("http://pugliairbus.aeroportidipuglia.it/".$partenza);
					$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
					$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
					$html = sprintf('<html><head><title></title></head><body>%s</body></html>', $html);
					$html=str_replace("</th>","</th>;",$html);
					$html=str_replace("</tr>","</tr>;",$html);
					$html=str_replace("<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"586\"","<prova>",$html);

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
				$text="";
				for ($i=0;$i<$count;$i++){
					array_push($allerta,$data[$i]);
				$countr++;
				}
				for ($tt=0;$tt<20;$tt++){
				for ($t=1;$t<20;$t++){
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

				$filecsv = fopen('db/falairr.csv', 'w+');
				fwrite($filecsv,$text); // Write information to the file
					 fclose($filecsv);

				$csv = array_map('str_getcsv', file('db/falairr.csv'));
				//var_dump($csv);
				$countcsv = 0;
				foreach($csv as $data=>$csv1){
					$countcsv = $countcsv+1;
				}
				//echo $count;
				for ($i=0;$i<$countcsv;$i++){
					$homepage .="\n";
					$homepage .=$csv[$i][0]."\n";
					$homepage .=$csv[$i][1]."\n";
					$homepage .=$csv[$i][2]."\n";
					$homepage .=$csv[$i][3]."\n";

				}
				$chunks = str_split($homepage, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);

					}
					$content = array('chat_id' => $chat_id, 'text' => "Approfondimenti, disponibilità e biglietti online su http://pugliairbus.aeroportidipuglia.it/",'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);

					$log=$today. ",ricerca," .$chat_id. "\n";
					file_put_contents(LOG_FILE, $log, FILE_APPEND | LOCK_EX);

}

$this->create_keyboard_temp($telegram,$chat_id);
exit;

	}

	function create_keyboard_temp($telegram, $chat_id)
	 {
			 $option = array(["Bus da MT oggi","Bus da BA oggi"],["Istruzioni","Informazioni"]);
			 $keyb = $telegram->buildKeyBoard($option, $onetime=false);
			 $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Digita la sequenza di caratteri ad esempio bm%31/01/2016 oppure clicca sulle prossime partenze]");
			 $telegram->sendMessage($content);
	 }



}

?>
