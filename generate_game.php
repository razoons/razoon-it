<?php

session_start();

include("connection.php");
$_SESSION['error']="";


if (count($_POST['player'])%$_POST['nbr_teams']<>0){
	$_SESSION['error']= "Teams are not equal. Change the number of teams or players";
	header ("Location: create_game.php");
}

$existing_users[]=array();
$req = $bdd->query('SELECT * FROM users');
while ($existing_users_req = $req->fetch()){
	$existing_users[]=$existing_users_req['user'];
}

$list_players=array();
$dispatched_players=array();

foreach($_POST['player'] as $player){
	if ($player<>""){
		if (array_search($player, $existing_users)==""){
			$list_players[]=$player;
		}else{
			$_SESSION['error']= "Player \"".$player."\" already exist.";
			header ("Location: create_game.php");
		}
	}
}

for ($i=0;$i<count($_POST['player'])/$_POST['nbr_teams'];$i++){
	for ($j=0;$j<$_POST['nbr_teams'];$j++){
		$x=rand(0,count($list_players)-1);
		$dispatched_players['user'][$j][$i]=$list_players[$x];
		array_splice($list_players,$x,1);
	}
}

$nbr_spies=rand($_POST['min_nbr_spies'],$_POST['max_nbr_spies']);

$k=1;
for ($i=0;$i<$nbr_spies;$i++){
	for ($j=0;$j<$_POST['nbr_teams'];$j++){
		if ($j+$i+$k-floor(($j+$i+$k)/$_POST['nbr_teams'])*$_POST['nbr_teams']==$j){
			$k++;
		}
		$dispatched_players['spy_team'][$j][$i]=$j+$i+$k-floor(($j+$i+$k)/$_POST['nbr_teams'])*$_POST['nbr_teams'];

	}
}

//Insert game
$insert_game=$bdd->prepare('INSERT INTO games(current_turn,turns,target) VALUES (1,:turns,:target)');
$insert_game->execute(array(
'turns' => $_POST['nbr_turns'],
'target' => $_POST['code_objective']
));
$insert_game->closeCursor();

$req = $bdd->query('SELECT * FROM games ORDER BY id DESC');
$new_game = $req->fetch();


//Insert teams
$mapping_teams=array();



$insert_teams=$bdd->prepare('INSERT INTO teams(team,game_id,color,font_color,production_progress,updated) VALUES (:team,:game_id,:color,:font_color,0,0)');
for ($j=0;$j<$_POST['nbr_teams'];$j++){
	$color=rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
	list($r, $g, $b) = sscanf($color,"%02x%02x%02x");
	$font_color_index = 1 - ( 0.299 * $r + 0.587 * $g + 0.114 * $b)/255;

	if ($font_color_index < 0.5){
		$font_color = "black"; // bright colors - black font
	}else{
		$font_color = "white"; // dark colors - white font
	}
	$insert_teams->execute(array(
	'team' => "Company ".$j,
	'game_id' => $new_game["id"],
	'color' => $color,
	'font_color' => $font_color
	));

	$req = $bdd->query('SELECT * FROM teams ORDER BY id DESC');
	$new_team = $req->fetch();

	$mapping_teams[$j]= $new_team["id"];
}
$insert_teams->closeCursor();

// Insert users
$insert_users=$bdd->prepare('INSERT INTO users(game_id,team_id,spy_team_id,user,password,spy_acknowledged) VALUES (:game_id,:team_id,:spy_team_id,:user,:password,0)');

for ($i=0;$i<count($dispatched_players['user']);$i++){
	for ($j=0;$j<count($dispatched_players['user'][$i]);$j++){
		if (!isset($dispatched_players['spy_team'][$i][$j])){
			$dispatched_players['spy_team'][$i][$j]=$i;
		}
		$insert_users->execute(array(
		'game_id' => $new_game["id"],
		'team_id' => $mapping_teams[$i],
		'spy_team_id' => $mapping_teams[$dispatched_players['spy_team'][$i][$j]],
		'user' => $dispatched_players['user'][$i][$j],
		'password' => sha1($dispatched_players['user'][$i][$j])
		));
		//echo "pour ".$dispatched_players['user'][$i][$j]." :".$mapping_teams[$i].". spy";
	}
}
$insert_users->closeCursor();

  header ("Location: index.php");

?>
