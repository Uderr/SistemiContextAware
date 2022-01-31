<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$bikeID = $_GET['bikeid'];
$enumerate = 1;

if(isset($bikeID)){

    $checkPaths = pg_query("SELECT * FROM paths WHERE id = '$bikeID';");


    $results = "";

    while ($row = pg_fetch_row($checkPaths)) {
      $results = $results."Punto di partenza $enumerate:    ".$row[1]."  Destinazione $enumerate:   ".$row[2]."</br>";
      $enumerate = $enumerate + 1;
    }

    if($results == ""){
        echo ("Nessun percorso trovato");
    }

    echo ($results);

    unset($bikeID);

}








?>