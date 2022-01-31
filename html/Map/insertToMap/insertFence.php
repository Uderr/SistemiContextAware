<?php


session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");


$typeOf = $_POST["typeOf"];
$file = $_FILES["file"];
$target_dir = "Inserted/";
$file["name"] = "insertFence.txt";
move_uploaded_file($file["tmp_name"], "Inserted/" . $file["name"]);

$json = (file_get_contents("Inserted/insertFence.txt"));


echo ($typeOf);
echo ($json);

$string = 'ogr2ogr -f "PostgreSQL" PG:"dbname=NowBike user=postgres password=Bellico97" "/var/www/html/Map/insertToMap/Inserted/insertFence.txt"';

exec($string);



if($typeOf == 1){
    $addToNoArea = pg_query("INSERT INTO text2
                           SELECT ogc_fid, wkb_geometry, name FROM insertfence;
                           INSERT INTO text2
                           SELECT * FROM noarea;
                           DELETE FROM noarea;

                           CREATE TABLE tmp2 (id int, geom geometry, name varchar);
                           INSERT INTO tmp2 (name, geom) SELECT DISTINCT name, geom FROM text2;
                           DROP TABLE text2;
                           ALTER TABLE tmp2 RENAME TO text2;
                           DROP TABLE insertfence;

                           INSERT INTO noarea (geom, name)
                           SELECT geom, name FROM text2;

                           DELETE FROM text2;
                           ");

    header('location:../indexAdmin.php');

}else{
    $addToPOI = pg_query("INSERT INTO text2
                           SELECT ogc_fid, wkb_geometry, name FROM insertfence;
                           INSERT INTO text2
                           SELECT * FROM interest;
                           DELETE FROM interest;

                           CREATE TABLE tmp2 (id int, geom geometry, name varchar);
                           INSERT INTO tmp2 (name, geom) SELECT DISTINCT name, geom FROM text2;
                           DROP TABLE text2;
                           ALTER TABLE tmp2 RENAME TO text2;
                           DROP TABLE insertfence;

                           INSERT INTO interest (geom, name)
                           SELECT geom, name FROM text2;

                           DELETE FROM text2;
                           ");

    header('location:../indexAdmin.php');
}





?>