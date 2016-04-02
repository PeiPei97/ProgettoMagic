<?php

$file_ver_name = 'versionejson';

function scaricaPagina($ind) {
    /*$ch = curl_init($ind);
    if(false) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_PROXY, '192.168.0.10:8080');
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'inf.lerdaa1204:password97');

        curl_setopt($ch, CURLOPT_VERBOSE, true);
    }

    $out = curl_exec($ch);
    curl_close($ch);*/

    return file_get_contents($ind);
}

function scaricaZip($nomefile, $ind) {
	$TMP = 'tmp.zip';
	/* Scarioco file zip */
    $out = scaricaPagina($ind);
    $f = fopen($TMP, "w") or die("Unable to open file!");
    fwrite($f, $out);
    fclose($f);
	
	/* Estraggo lo zip */
    $zip = new ZipArchive;
    if ($zip->open($TMP) === TRUE) {
        $f = fopen($nomefile, "w");
		
		/* Salvo il contenuto non compresso */
        fwrite($f, $zip->getFromIndex(0));
        $zip->close();

        fclose($f);
    } else {
        echo 'failed';
    }
	
	//delete($TMP); //NOn funziona
}

$cv = scaricaPagina('http://mtgjson.com/'); /* Controllo versione */

preg_match('/\<span id=\"currentversion\"\>[^\d]*([\d])*\.([\d])*\.([\d]*).*\<\/span\>/', $cv, $m);
$letto = array(intval($m[1]), intval($m[2]), intval($m[3]));
$attuale = null;

if(file_exists($file_ver_name)) {
    $str_versione = file_get_contents($file_ver_name);
    $attuale = json_decode($str_versione);
}
else {
    $attuale = array(0,0,0);
}

echo "inizio";
$da_scaricare = false;

/* Controllo i valori della versione per capire se è più nuovo */
if($letto[0]>$attuale[0] ||
   ($letto[0] == $attuale[0] && ($letto[1] > $attuale[1] ||
                                 ($letto[1] == $attuale[1] && $letto[2] > $attuale[2])))) $da_scaricare = true;
/* Nel caso sia da aggiornare procedo */
if($da_scaricare) {
	
	/* Scrivo la nuova versione */
    $f = fopen("versionejson", "w");
    fwrite($f, json_encode($letto));
	
	scaricaZip('cards.json', 'http://mtgjson.com/json/AllCards.json.zip');
	scaricaZip('sets.json', 'http://mtgjson.com/json/AllSets.json.zip');
}

?>