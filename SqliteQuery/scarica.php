<?php

function scaricaPagina($ind) {
    $ch = curl_init("http://mtgjson.com/json/AllCards.json.zip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
    curl_setopt($ch, CURLOPT_PROXY, '192.168.0.10:8080');
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'inf.lerdaa1204:password97');

    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $out = curl_exec($ch);
    curl_close($ch);

    return $out;
}

$cv = scaricaPagina('http://mtgjson.com/'); /* Controllo versione */

preg_match($cv, '/<span id="currentversion">.*([\d]*\.[\d]*\.[\d]*).*</span>/', $m);

var_dump($m);

/*
$f = fopen("cards.zip", "w") or die("Unable to open file!");
fwrite($f, $out);

$zip = new ZipArchive;
if ($zip->open('cards.zip') === TRUE) {
    echo $zip->getFromName('AllCards.json');
    $zip->close();
} else {
    echo 'failed';
}
*/

?>