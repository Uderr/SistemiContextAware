<?php


session_start();

$host = 'localhost';
$db = 'NowBike';
$user = 'postgres';
$password = 'postgres';
$connect= pg_connect("host=localhost dbname=NowBike user=postgres password=Bellico97");

$code = $_GET['admincode'];

$check = pg_query("SELECT * FROM admin WHERE admincode = '$code'; ");

$isTrue = "";

while ($row = pg_fetch_row($check)) {
  $isTrue = $isTrue.$row[0].$row[1].$row[2];
}

if($isTrue != ""){
    $_SESSION['isAdmin'] = 1;
    header('location:indexAdmin.php');
}else{
    header('location:index.php');
}

?>