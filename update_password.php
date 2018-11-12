<?php

session_start();

include("connection.php");
$_SESSION['error']= "";

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}

if ($_POST['password']==$_POST['confirm_password']){
	$update_user=$bdd->prepare('UPDATE users SET password=:password WHERE user=:user');
     $update_user->execute(array(
     'user' => $_SESSION['user'],
     'password' => sha1($_POST['password'])
     ));
header ("Location: actions.php");

}else{
	$_SESSION['error']= "Passwords must match.";
	header ("Location: configuration.php");
}





	?>
