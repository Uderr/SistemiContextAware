<?php


session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$vertices = $_GET['polygon'];
$prova = $_GET['prova'];

echo ($prova);
echo ($vertices);

if($prova == 2){

    $insertFence = pg_query("INSERT INTO noarea (geom)
                             SELECT ST_MakePolygon( ST_GeomFromText('LINESTRING($vertices)'));");
    header('location:../indexAdmin.php');

}else{

    $insertFence = pg_query("INSERT INTO interest (geom)
                             SELECT ST_MakePolygon( ST_GeomFromText('LINESTRING($vertices)'));");
    header('location:../indexAdmin.php');
}


?>