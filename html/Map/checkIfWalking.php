<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

$positionX1 = $_GET['PpositionX'];
$positionY1 = $_GET['PpositionY'];
$offset1 = $_GET['Ooffset'];

$positionX2 = $_GET['positionX1Arr'];
$positionY2 = $_GET['positionY1Arr'];
$offset2 = $_GET['offsetArr'];

$time = $_GET['time'];

$isBike = 0;


$trueX = $positionX1 - $offset1;
$trueY = $positionY1 - $offset1;

$trueXArr = $positionX2 - $offset2;
$trueYArr = $positionY2 - $offset2;


$checkDistance = pg_query("SELECT ST_Distance(ST_MakePoint('$trueX', '$trueY'),ST_MakePoint('$trueXArr', '$trueYArr'));");

$distance = "";
while ($row = pg_fetch_row($checkDistance)) {
   $distance = $distance.$row[0];
}

$distance = $distance * 111195;
$distance = $distance/1000;

$velox = ($distance/$time);

if($velox <= 7){
    $isBike = 0;
    echo $isBike;
}else{
    $isBike = 1;
    echo $isBike;
}



?>
