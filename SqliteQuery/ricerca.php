<?php
  $conn = null;

  try{
    $conn = new PDO(
        'sqlite:carte.db',
        null,
        null,
        array(PDO::ATTR_PERSISTENT => true)
    );
  }catch(PDOException $ex){
      echo("Connessinoe fallita.".$ex->getMessage());
  }

  function ricercaAttirbuti($tabella){
    try{
      $stmSql = $conn->prepare("SELECT * FROM (?)");
      $stmSql->bindParam(1, $tabella);
      $stmSql->execute();
    }catch(PDOException $exc){
      echo("Albertooooooooooo!".$ex->getMessage());
    }

    return $stmSql;
  }

  $x = ricercaAttirbuti("colori");
  while($row = $x->fetch()){
    $colore = $row["colori_id"];
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
