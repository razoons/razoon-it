<?php

session_start();

include("connection.php");

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}
$_SESSION['error']="";

$req=$bdd->query('SELECT * FROM games WHERE id="'.$_SESSION['game_id'].'"');
$current_game=$req->fetch();
$req=$bdd->query('SELECT * FROM teams WHERE id="'.$_SESSION['team_id'].'"');
$current_team=$req->fetch();

$insert_admission=$bdd->prepare('INSERT INTO admissions(game_id,turn,team_id,target_user,voter_user,vote_result) VALUES (:game_id,:turn,:team_id,:target_user,:voter_user,:vote_result)');
$array = explode(',', $_POST['team_admission']); //create an array by separating string

foreach ($array as $value) {
	$req=$bdd->query('SELECT * FROM users WHERE team_id="'.$value.'"');
	while ($list_users=$req->fetch()){
		$insert_admission->execute(array(
		'game_id' => $_SESSION['game_id'],
		'turn' => $current_game['current_turn'],
		'team_id' => $value,
		'target_user' => $_SESSION['user'],
		'voter_user' => $list_users['user'],
		'vote_result' => NULL
		));
	}	
}

header ("Location: actions.php");
?>
