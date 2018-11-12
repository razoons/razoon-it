<?php

session_start();

include("connection.php");

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}


 $update_team=$bdd->prepare('UPDATE teams SET team=:team,color=:color,font_color=:font_color,updated=1 WHERE id=:id');
    $color=substr($_POST['color'],1);
	list($r, $g, $b) = sscanf($color,"%02x%02x%02x");
	$font_color_index = 1 - ( 0.299 * $r + 0.587 * $g + 0.114 * $b)/255;

	if ($font_color_index < 0.5){
		$font_color = "black"; // bright colors - black font
	}else{
		$font_color = "white"; // dark colors - white font
	}
	$update_team->execute(array(
    'id' => $_SESSION['team_id'],
    'team' => $_POST['team'],
	'color' => $color,
	'font_color' => $font_color
    ));
	
header ("Location: configuration.php");

	?>
