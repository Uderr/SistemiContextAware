<?php

    session_start();


    $host = 'localhost';
    $db = 'NowBike';
    $user = 'postgres';
    $password = 'postgres';
    $connect = pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

    $maxDistance = $_GET['maxdistance'];

    echo ($maxDistance);

    $modify = pg_query("UPDATE maxdistance SET maxdistance = '$maxDistance';");

    header('location: indexAdmin.php');


?>