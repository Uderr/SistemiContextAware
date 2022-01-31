<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

$_SESSION["reserved"] = 0;
$bikesReservedJSON = "";

echo ($_SESSION['isAdmin']);


$min = 1000;
$max = 9999;
$reservation = rand($min,$max);

$bikeID1 = $_GET['bikeid'];
$bikeID2 = $_POST['username'];
$bikeID = "";


if(isset($bikeID2)){
    $bikeID = $bikeID2;
}else{
    $bikeID = $bikeID1;
}

$_SESSION['bikeID'] = $bikeID;


//INSERISCI NEI PRENOTATI LA BICI, RESTITUISCI LA POSIZIONE DELLA BICI PRENOTATA
$insert = pg_query("INSERT INTO reserved (id, geom)
SELECT id,geom FROM bici WHERE bici.id = '$bikeID';
UPDATE reserved SET resnumb = '$reservation' WHERE (id = '$bikeID');");




//RITORNA UN GEOJSON DEL DATABASE
$bikesReserved = pg_query($connect, "SELECT jsonb_build_object(
                                 'type',     'FeatureCollection',
                                 'features', jsonb_agg(feature)
                             )
                             FROM (
                               SELECT jsonb_build_object(
                                 'type',       'Feature',
                                 'id',         id,
                                 'geometry',   ST_AsGeoJSON(geom)::jsonb,
                                 'properties', to_jsonb(row) - 'gid' - 'geom'
                               ) AS feature
                               FROM (SELECT * FROM reserved WHERE id = '$bikeID') row) features;");




//$deleteFromAvaiable = "DELETE FROM bici WHERE id = '$bikeID'";

while ($row = pg_fetch_row($bikesReserved)) {
  $bikesReservedJSON = $bikesReservedJSON.$row[0].$row[1].$row[2];
}

$_SESSION['bikePosition'] = $bikesReservedJSON;


//SALVA I RISULTATI NEL FILE JSON
$fileHandle = fopen("Results/bikesReserved.json", "w");
fwrite($fileHandle, $bikesReservedJSON);
fclose($fileHandle);


$_SESSION["reserved"] = 1;
$_SESSION["resNumb"] = $reservation;

header('location:indexReserve.php');




?>