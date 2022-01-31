<?php

    session_start();


    $host = 'localhost';
    $db = 'NowBike';
    $user = 'postgres';
    $password = 'postgres';
    $connect = pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

    $bikeID = $_SESSION['bikeID'];
    $geom = $_SESSION['geom'];
    echo ($bikeID);

    $delete = pg_query("DELETE FROM reserved WHERE id = '$bikeID'");
    $insertAgain = pg_query("INSERT INTO bici VALUES ('$bikeID', '$geom')");
    $_SESSION['reserved'] = 0;
    header('location:index.php');


?>