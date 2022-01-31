<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$name = $_GET['name'];

$return = pg_query("SELECT * FROM access WHERE name = '$name';");

$result = "";
while ($row = pg_fetch_row($return)) {
  $result = $result.$row[0]."   ".$row[1]."    ".$row[2];
}

echo $result;


?>
