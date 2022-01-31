<?php


session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

//$file = $_FILES["file"];
//$target_dir = "Inserted/";
//$file["name"] = "insert.txt";
//move_uploaded_file($file["tmp_name"], "Inserted/" . $file["name"]);

//$json = (file_get_contents("Inserted/insert.txt"));

//echo ($json);

$string = 'ogr2ogr -f "PostgreSQL" PG:"dbname=NowBike user=postgres password=Bellico97" "/var/www/html/Map/insertToMap/Inserted/insert.txt"';

exec($string);


$addToPark = pg_query("INSERT INTO text
                       SELECT ogc_fid, wkb_geometry, nome FROM insert;
                       INSERT INTO text
                       SELECT * FROM park;
                       DELETE FROM park;

                       CREATE TABLE tmp (id int, geom geometry, name varchar);
                       INSERT INTO tmp (geom, name) SELECT DISTINCT geom, name FROM text;
                       DROP TABLE text;
                       ALTER TABLE tmp RENAME TO text;
                       DROP TABLE insert;

                       INSERT INTO park (geom, name)
                       SELECT geom, name FROM text;

                       DELETE FROM text;
                       ");

header('location:../indexAdmin.php');


?>