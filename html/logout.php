<?php

session_start();
unset($_SESSION);
session_destroy();
session_write_close();
if(!isset($_SESSION)){
	echo("success");
	die;
}else{
	echo("failure");
} 
die;
?>
