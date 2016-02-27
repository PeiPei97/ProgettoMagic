<?php
  $conn = null;

  /*Connessione al db carte.db*/
  try{
    $conn = new PDO(
        'sqlite:carte.db',
        null,
        null,
        array(PDO::ATTR_PERSISTENT => true)
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }catch(PDOException $ex){
      echo("Connessinoe fallita.".$ex->getMessage());
  }

  /*Ricerca gli elenchi degli attributi delle carte, nasce per riempire i campi di ricerca*/
  /*Non utilizzo la bind perchè il parametro viene passato dal programmatore, non dall'utonto*/
  /*Restituisce i dati in formato json*/
  function ricercaAttirbuti($tabella){
    try{
      global $conn;
      $stmSql = $conn->prepare("SELECT * FROM $tabella");
      $stmSql->execute();
      $json = codificaRisultati($stmSql);
    }catch(PDOException $ex){
      echo("Errore nella query!".$ex->getMessage());
    }

    return $json;
  }

  /*Restituisce i risultati di una query in formato json*/
  function codificaRisultati($stmExecuted){
      $results = $stmExecuted->fetchAll(PDO::FETCH_ASSOC);
      $json=json_encode($results);

      return $json;
  }



  /*Ricerca le carte, riceve un json contente i campi su cui si vuole effettuare la ricerca.*/
  /*Formato dati [nomeCarta, tipo, colore, espansione, rarità]*/
  /*Se il campo non è stato valorizzato dall'utente */




  $jsonResults = ricercaAttirbuti("espansioni");

  /*Leggo ricorsivamente l'array json, utile per gli array annidati*/
  $jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($jsonResults, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

  foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        echo "$key:\n";
    } else {
        echo "$key => $val</br>";
    }
  }


/*
  while($row = $x->fetch()){
    $colore = $row["espansione"];
    echo("$colore");
  }
/*
  try{
    $stmSql = $conn->prepare("SELECT * FROM colori");
    $stmSql->execute();
  }catch(PDOException $exc){
    echo("Albertooooooooooo!".$ex->getMessage());
  }

  while($row = $stmSql->fetch()){
      $colore = $row["colori_id"];
      echo("$colore");
  }*/

  echo("Fine!");


 ?>
