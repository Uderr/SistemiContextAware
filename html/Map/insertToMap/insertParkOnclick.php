<?php


session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$x = $_GET['x'];
$y = $_GET['y'];


$addToPark = pg_query("INSERT INTO park (geom)
                       SELECT ST_MakePoint($x,$y);");

header('location:../indexAdmin.php');


?>