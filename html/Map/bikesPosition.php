<?php

    session_start();


    $host = 'localhost';
    $db = 'NowBike';
    $user = 'postgres';
    $password = 'postgres';
    $connect = pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


    $avaiablesBikes = pg_query($connect, "SELECT jsonb_build_object(
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

    $bikes = "";
    while ($row = pg_fetch_row($avaiablesBikes)) {
         $bikes = $bikes.$row[0].$row[1].$row[2];
    }



    $_SESSION['bikes'] = $bikes;
    echo ($bikes);


?>