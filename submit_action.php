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

//if the action is a leak
if($_POST['leak'])
{
	//we remove the leak (but not the default action part)
	if($_POST['action']=="remove_action")
	{
		$remove_action = $bdd->prepare('UPDATE actions SET leak_risk=0, leak_team_id=-1 WHERE user=:user AND turn=:turn');
		$remove_action->execute(array(
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn']
		));
		$remove_action->closeCursor();
	}
	//we update the leak
	else
	{
		$update_action = $bdd->prepare('UPDATE actions SET leak_risk=:leak_risk, leak_team_id=:leak_team WHERE user=:user AND turn=:turn');
		if($_POST['action']=='leak_low')
		{
			$risk = "low";
		}
		else{
			$risk = "high";
		}
		$update_action->execute(array(
		'leak_risk' => $risk,
		'leak_team' => $_POST['team'],
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn']
		));
		$update_action->closeCursor();
	}
}
//action is a default one (not leak)
else
{
	//remove action (but not the leak action part)
	if($_POST['action']=="remove_action")
	{
		$remove_action = $bdd->prepare('UPDATE actions SET action="", target_team_id=0 WHERE user=:user AND turn=:turn');
		$remove_action->execute(array(
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn']
		));
		$remove_action->closeCursor();
	}
	//update the default action (not the leak part)
	else
	{
		$insert_action=$bdd->prepare('UPDATE actions SET action=:action, target_team_id=:target_team_id WHERE user=:user AND turn=:turn');
		$insert_action->execute(array(
		'action' => $_POST['action'],
		'target_team_id' => $_POST['team'],
		'user' => $_SESSION['user'],
		'turn' => $_SESSION['current_turn']
		));
	  $insert_action->closeCursor();
	}
}

$_SESSION['message']="Action updated";
header ("Location: actions.php");

?>
