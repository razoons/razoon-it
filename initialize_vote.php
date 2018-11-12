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

$insert_vote=$bdd->prepare('INSERT INTO exclusions(game_id,turn,team_id,target_user,voter_user,vote_result) VALUES (:game_id,:turn,:team_id,:target_user,:voter_user,:vote_result)');
$req=$bdd->query('SELECT * FROM users WHERE team_id="'.$current_team['id'].'" AND user<>"'.$_POST['target_user'].'"');
while ($list_users=$req->fetch()){
	if ($_SESSION['user']==$list_users['user']){
		$result=true;
	}else{
		$result=NULL;
	}
	$insert_vote->execute(array(
	'game_id' => $_SESSION['game_id'],
	'turn' => $current_game['current_turn'],
	'team_id' => $current_team['id'],
	'target_user' => $_POST['target_user'],
	'voter_user' => $list_users['user'],
	'vote_result' => $result
	));
}
header ("Location: exclusions.php");
?>
