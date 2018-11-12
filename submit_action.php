<?php

session_start();

include("connection.php");

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}


$remove_action=$bdd->prepare('DELETE FROM actions WHERE user=:user AND turn=:turn');
$remove_action->execute(array(
	'user' => $_SESSION['user'],
	'turn' => $_SESSION['current_turn']
	));
$remove_action->closeCursor();


if ($_POST['action']!="remove_action"){
	  $insert_action=$bdd->prepare('INSERT INTO actions(user,team_id,action,target_team_id,game_id,turn,blocked) VALUES (:user,:team_id,:action,:target_team_id,:game_id,:turn,0)');
	  $insert_action->execute(array(
		'user' => $_SESSION['user'],
		'team_id' => $_SESSION['team_id'],
		'action' => $_POST['action'],
		'target_team_id' => $_POST['team'],
		'game_id' => $_SESSION['game_id'],
		'turn' => $_SESSION['current_turn']
		));
	  $insert_action->closeCursor();

}

$_SESSION['message']="Action updated";
header ("Location: actions.php");

?>
