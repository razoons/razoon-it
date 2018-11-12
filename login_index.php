<?php

session_start();

include("connection.php");

$_SESSION['error']="";

if (empty($_POST['user']) || empty($_POST['password'])) {
$_SESSION['error']= "Please provide user and password";
}
else
{

$pass_hache = sha1($_POST['password']);

// Vérification des identifiants
$req = $bdd->prepare('SELECT * FROM users WHERE user = :user AND password = :password');
$req->execute(array(
    'user' => $_POST['user'],
    'password' => $pass_hache));

$resultat = $req->fetch();

if (!$resultat)
{
  $_SESSION['error']="User or Password incorrect";
}
else
{
	$_SESSION['user'] = $_POST['user'];
	$_SESSION['game_id'] = $resultat['game_id'];
	$_SESSION['team_id'] = $resultat['team_id'];
  $_SESSION['spy_team_id'] = $resultat['spy_team_id'];

	$req = $bdd->query('SELECT * FROM games WHERE id ='.$_SESSION['game_id'].'');
	$resultat = $req->fetch();

	$_SESSION['current_turn'] = $resultat['current_turn'];

	header ("Location: home.php");
}
}

if ($_SESSION['error']<>""){
  header ("Location: index.php");
}

?>
