<?php

session_start();

include("connection.php");

$remove_actions=$bdd->exec('DELETE FROM actions WHERE game_id='.$_POST['game_id'].'');

$remove_admissions=$bdd->exec('DELETE FROM admissions WHERE game_id='.$_POST['game_id'].'');

$remove_exclusions=$bdd->exec('DELETE FROM exclusions WHERE game_id='.$_POST['game_id'].'');

$remove_notif=$bdd->exec('DELETE FROM notifications WHERE game_id='.$_POST['game_id'].'');

$remove_teams=$bdd->exec('DELETE FROM teams WHERE game_id='.$_POST['game_id'].'');

$remove_users=$bdd->exec('DELETE FROM users WHERE game_id='.$_POST['game_id'].'');

$remove_games=$bdd->exec('DELETE FROM games WHERE id='.$_POST['game_id'].'');

$remove_reports=$bdd->exec('DELETE FROM reports WHERE game_id='.$_POST['game_id'].'');

header ("Location: admin_games.php");
?>
