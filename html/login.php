<?php


session_start();
$db2 = mysqli_connect('localhost','root','Bellico97!','register_from_android');
require_once "validate.php";

if(isset($_POST['email']) && isset($_POST['password'])){
    require_once "validate.php";
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $query = "select * from users where email='$email' and password='$password'";
    $result = mysqli_query($db2,$query);
    if($result){
        $query2 = "select id from users where name='$email'";
        $result = mysqli_query($db2,$query2);
        $row = mysqli_fetch_array($result);
        $id = $row[0];
        if(isset($id)){
            $_SESSION['isAdmin'] = 1;
        }
        echo "success";
    } else{
        echo "failure";
    }
}
?>
