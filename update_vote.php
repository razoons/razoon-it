<?php

session_start();

include("connection.php");

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}
$_SESSION['error']="";


$update_vote=$bdd->prepare('UPDATE exclusions SET vote_result=:vote_result WHERE id=:id');
$update_vote->execute(array(
'id' => $_POST['id'],
'vote_result' => $_POST['vote_result']
));
header ("Location: exclusions.php");
?>
