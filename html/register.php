<?php

session_start();
$db2 = mysqli_connect('localhost','root','Bellico97!','register_from_android');
require_once "validate.php";



if(isset($_POST['email']) && isset($_POST['password'])){
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $query = "INSERT INTO users VALUES (NULL,'$email','$email','$password')";
    $result = mysqli_query($db2,$query);
    if($result){
	    echo ("success");
    }else{
	    echo("failure");
    }
}
?>
