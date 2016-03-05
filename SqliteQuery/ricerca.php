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



  /*Ricerca le carte, riceve un vettore contente i campi su cui si vuole effettuare la ricerca.*/
  /*Formato dati [nome, tipo, colore, espansione, rarita]*/
  /*Se il campo non è stato valorizzato dall'utente non viene incluso nella ricerca*/
  function ricerca($request){
    global $conn;
    $binds= [];
    $query = "SELECT * FROM ";
    $tabelle = "carte";
    $condizioni = "1 = 1 ";
    if($request["nome"] != ""){
      $condizioni = $condizioni."AND nome = ? ";
      $binds[] = $request["nome"];
    }
    if($request["tipo"] != ""){
      $condizioni = $condizioni."AND tipo = ? ";
      $binds[] = $request["tipo"];
    }
    if($request["colore"] != ""){
      $tabelle = $tabelle.", costi_carte";
      $codizioni = $condizioni."AND costi_carte.carte_id = carte.id ";
      $condizioni = $condizioni."AND colori_id = ? ";
      $binds[] = $request["colore"];
    }
    if($request["espansione"] != ""){
      $tabelle = $tabelle.", espanioni_carte";
      $condizioni = $condizioni."AND espansioni_carte.carte_id = carte.id ";
      $condizioni = $condizioni."AND espansioni_id = ? ";
      $binds[] = $request["espansione"];
    }
    if($request["rarita"] != ""){
      $condizioni = $condizioni."AND rarita = ? ";
      $binds[] = $request["rarita"];
    }

    $query = $query.$tabelle." WHERE ".$condizioni;
    echo($query);
    $stm = $conn->prepare($query);

    $i = 1;
    foreach ($binds as $campo) {
      $stm->bindParam($i, $campo);
      $i = $i+1;
    }


    try{
        $stm->execute();
    }catch(PDOException $e){
      echo($e->getMessage());
    }
    while($stm->fetch()) echo "Haloa";

    return codificaRisultati($stm);
  }

  $arr["nome"] = "";
  $arr["tipo"] = "";
  $arr["colore"] = "";
  $arr["espansione"] = "";
  $arr["rarita"] = "";

  $jsonResults = ricerca($arr);


  /*$jsonResults = ricercaAttirbuti("espansioni");

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
