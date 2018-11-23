<?php

session_start();

include("connection.php");

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}

$action_exists=$bdd->prepare('SELECT * FROM actions WHERE user=:user AND turn=:turn AND game_id=:game_id');
$action_exists->execute(array(
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn'],
		'game_id' => $_SESSION['game_id']
		));

//if action not been inserted yet, create it
if(!$action_exists->fetch())
{
	$insert_action=$bdd->prepare('INSERT INTO actions(user,team_id,action,target_team_id,game_id,turn,blocked,leak_risk, leak_team_id, snitched) VALUES (:user,:team_id,"",-1,:game_id,:turn,0,0,-1,0)');
	$insert_action->execute(array(
		'user' => $_SESSION['user'],
		'team_id' => $_SESSION['team_id'],
		'game_id' => $_SESSION['game_id'],
		'turn' => $_SESSION['current_turn'],
		));
	$insert_action->closeCursor();
}
$action_exists->closeCursor();

//Check if values have been affected
$action= isset($_POST['action']) ? $_POST['action'] : "";
$team = isset($_POST['team']) ? (int)$_POST['team'] : -1;
$leak= isset($_POST['leak']) ? $_POST['leak'] : "";
$leak_team = isset($_POST['leak_team']) ? (int)$_POST['leak_team'] : -1;

//Update action with new values
$update_action = $bdd->prepare('UPDATE actions SET action=:action, target_team_id=:target_team_id, leak_risk=:leak_risk, leak_team_id=:leak_team WHERE user=:user AND turn=:turn');
$update_action->execute(array(
		'action' => $action,
		'target_team_id' => $team,
		'leak_risk' => $leak,
		'leak_team' => $leak_team,
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn']
		));
$update_action->closeCursor();

$_SESSION['message']="Action updated";
header ("Location: actions.php");

?>
