<?php

    session_start();


    $host = 'localhost';
    $db = 'NowBike';
    $user = 'postgres';
    $password = 'postgres';
    $connect = pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

    $_SESSION["error"] = 0;



    $x = $_GET['positionX'];
    $y = $_GET['positionY'];
    $offset = $_GET['offset'];
    $parkID = $_GET['parkID'];
    $bikeID = $_SESSION['bikeID'];

    $latitudeANDLong = pg_query("SELECT ST_X (ST_Transform (geom, 4326)) AS long,
                                          ST_Y (ST_Transform (geom, 4326)) AS lat
                                   FROM park WHERE id = '$parkID';");
    $positionParkX = "";
    $positionParkY = "";

    while ($row = pg_fetch_row($latitudeANDLong)) {
      $positionParkY = $positionParkY.$row[0] + $offset;
      $positionParkX = $positionParkX.$row[1] + $offset;
    }


    $checkDistance = pg_query("SELECT ST_DistanceSphere(ST_MakePoint('$x', '$y'),ST_MakePoint('$positionParkX', '$positionParkY'));");

    $distance = "";
    while ($row = pg_fetch_row($checkDistance)) {
       $distance = $distance.$row[0];
    }


    $checkMaxDistance = pg_query("SELECT * FROM maxdistance;");

    $maxDistance = "";
    while ($row = pg_fetch_row($checkMaxDistance)) {
       $maxDistance = $maxDistance.$row[0];
    }


    if($distance <= $maxDistance){
        $deleteReservation = pg_query("DELETE FROM reserved WHERE id = '$bikeID';");


        $name = "";
        $parkName = pg_query("SELECT name FROM park WHERE id = '$parkID'");
        while ($row = pg_fetch_row($parkName)) {
           $name = $name.$row[0];
        }

        echo ($name);
        echo ($bikeID);

        $finishPath = pg_query("UPDATE paths SET topos = '$name' WHERE id = '$bikeID';");

        //$addAgain = pg_query("INSERT INTO bici VALUES ('$bikeID','$name');");

        $_SESSION['reserved'] = 0;
        header('location:index.php');

    }else{
        $_SESSION['error'] = 5;
        header('location:indexTrip.php');

    }


?>