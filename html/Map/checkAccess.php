<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$noArea = $_GET['geom'];
$poiArea = $_GET['geomPOI'];
$id = $_SESSION['bikeID'];
$date = $_GET['date'];

echo $id;
echo $noArea;


if(isset($noArea)){
    $insertNoArea = pg_query("INSERT INTO access VALUES ('$noArea', '$id', '$date');");
}

if(isset($poiArea)){
    $insertPOIArea = pg_query("INSERT INTO access VALUES ('$poiArea', '$id', '$date');");
}

?>
