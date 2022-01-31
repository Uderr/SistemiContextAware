<?php

session_start();


$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect = pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

$_SESSION["error"] = 0;


//$x = $_GET[x];
//$y = $_GET[y];

$x = 44.50206;
$y = 11.334456;

$_SESSION[bikeX];
$_SESSION[bikeY];

$resNumb = $_GET[numb];
$bikeID = $_SESSION['bikeID'];


$unlockResult = "";
$unlock = pg_query("SELECT * FROM reserved WHERE resNumb = '$resNumb';");

while ($row = pg_fetch_row($unlock)) {
  $unlockResult = $unlockResult.$row[1];
}

if($unlockResult == ""){
    $_SESSION["error"] = 1;
    header("location: indexReserve.php");
}else{
    $latitudeANDLong = pg_query("SELECT ST_X (ST_Transform (geom, 4326)) AS long,
                                      ST_Y (ST_Transform (geom, 4326)) AS lat
                               FROM bici WHERE id = '$bikeID';");
    $positionBikeX = "";
    $positionBikeY = "";

    while ($row = pg_fetch_row($latitudeANDLong)) {
      $positionBikeY = $positionBikeY.$row[0];
      $positionBikeX = $positionBikeX.$row[1];
    }


    $checkDistance = pg_query("SELECT ST_DistanceSphere(ST_MakePoint('$x', '$y'),ST_MakePoint('$positionBikeX', '$positionBikeY'));");

    $distance = "";
    while ($row = pg_fetch_row($checkDistance)) {
       $distance = $distance.$row[0];
    }

    if($distance <= 300){
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

        $tripBike = "";
        while ($row = pg_fetch_row($bikesReserved)) {
          $tripBike = $tripBike.$row[0].$row[1].$row[2];
        }

        $_SESSION['tripBike'] = $tripBike;
        echo ($_SESSION['tripBike']);

        $_SESSION[bikeX] = $positionBikeX;
        $_SESSION[bikeY] = $positionBikeY;

        //---------------------------------------------------------------------------
        //TRACKING OF MOVEMENTS
        $geom = "";

        $takeGeometry = pg_query("SELECT geom FROM bici WHERE id = '$bikeID'");

        while ($row = pg_fetch_row($takeGeometry)) {
          $geom = $geom.$row[0].$row[1].$row[2];
        }

        $_SESSION['geom'] = $geom;

        $nameOfStart = "";
        $startLocation = pg_query("SELECT name FROM park WHERE  geom = '$geom';");

        while ($row = pg_fetch_row($startLocation)) {
          $nameOfStart = $nameOfStart.$row[0].$row[1].$row[2];
        }

        $addToPath = pg_query("INSERT INTO paths VALUES ('$bikeID', '$nameOfStart');");

        //-----------------------------------------------------------------------------

        $_SESSION['reserved'] = 2;
        header("location: indexTrip.php");

    }else{
        $_SESSION["error"] = 2;
        header("location: indexReserve.php");
    }
}



?>