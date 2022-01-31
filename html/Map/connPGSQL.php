<?php

session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


//$_SESSION["reserved"] = 0;

$bikeJSON = "";
$parksJSON = "";
$POIJSON = "";
$NOAREAJSON = "";



//-----------------------------------------------------------
//BIKE QUERY TO GEOJSON

$bikes = pg_query($connect, "SELECT jsonb_build_object(
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
                               FROM (SELECT * FROM bici) row) features;");

while ($row = pg_fetch_row($bikes)) {
  $bikeJSON = $bikeJSON.$row[0].$row[1].$row[2];
}

$fileHandle = fopen("Results/bikes.json", "w");
fwrite($fileHandle, $bikeJSON);
fclose($fileHandle);


//-----------------------------------------------------------
//PARK QUERY TO GEOJSON

$parks = pg_query($connect, "SELECT jsonb_build_object(
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
                               FROM (SELECT * FROM park) row) features;");

while ($row = pg_fetch_row($parks)) {
  $parksJSON = $parksJSON.$row[0].$row[1].$row[2];
}

$fileHandle = fopen("Results/park.json", "w");
fwrite($fileHandle, $parksJSON);
fclose($fileHandle);


//-----------------------------------------------------------
//NOAREA QUERY TO GEOJSON

$NOAREA = pg_query($connect, "SELECT jsonb_build_object(
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
                               FROM (SELECT * FROM noarea) row) features;");

while ($row = pg_fetch_row($NOAREA)) {
  $NOAREAJSON = $NOAREAJSON.$row[0].$row[1].$row[2];
}

$fileHandle = fopen("Results/NOAREA.json", "w");
fwrite($fileHandle, $NOAREAJSON);
fclose($fileHandle);


//-----------------------------------------------------------
//POI QUERY TO GEOJSON

$POI = pg_query($connect, "SELECT jsonb_build_object(
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
                               FROM (SELECT * FROM interest) row) features;");

while ($row = pg_fetch_row($POI)) {
  $POIJSON = $POIJSON.$row[0].$row[1].$row[2];
}

$fileHandle = fopen("Results/POI.json", "w");
fwrite($fileHandle, $POIJSON);
fclose($fileHandle);


?>