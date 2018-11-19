<?php

session_start();

if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}

	include("connection.php");

  $req=$bdd->query('SELECT * FROM games WHERE id="'.$_SESSION['game_id'].'"');
  $current_game=$req->fetch();
  $req=$bdd->query('SELECT * FROM teams WHERE id="'.$_SESSION['team_id'].'"');
  $current_team=$req->fetch();
	$req=$bdd->query('SELECT * FROM teams WHERE id="'.$_SESSION['spy_team_id'].'"');
	$current_spy_team=$req->fetch();
  $req=$bdd->query('SELECT * FROM teams');
  while ($teams_req=$req->fetch()){
		$teams['color'][$teams_req['id']]=$teams_req['color'];
		$teams['font_color'][$teams_req['id']]=$teams_req['font_color'];
	  $teams['team'][$teams_req['id']]=$teams_req['team'];
  }
  $req_users=$bdd->query('SELECT * FROM users WHERE user="'.$_SESSION['user'].'"');
  $current_user=$req_users->fetch();
  $req->closeCursor();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
  <link rel="stylesheet" href="./css/style-configuration.css">
</head>

<body>
  <header class="header-banner">
		<?php include("banner.php"); ?>
		<?php include("nav.php"); ?>
  </header>
	<?php if($current_team['updated']==0){ ?>
	<section class="bdg-sect-header">
	<h1>Company</h1>
	</section>
  <section class="bdg-sect">
		<form action="update_team.php" method="post">
			<div class="form-group"><label class="label_large">New company name</label><input type="text" placeholder="Type here your new company name" name="team" class="input input_large" id="team"/></div>
      <div class="form-group"><label class="label_large">New company color</label><input type="color" name="color" class="input"/></div>
		<button id="update" class="button">Update</button>
		</form>
  </section>
	<?php } ?>
	<section class="bdg-sect-header">
	<h1>User</h1>
	</section>
  <section class="bdg-sect">
    <form action="update_password.php" method="post" class="form">
      <div class="form-group"><label class="label">New password</label><input type="password" placeholder="Type here your new password" name="password" class="input" /></div>
      <div class="form-group"><label class="label">Confirm password</label><input type="password" placeholder="Confirm here your new password" name="confirm_password" class="input" /></div>
      <div class="form-group"><button type="submit" class="button">Save</button></div>
    </form>
    <section id="i3ur7f" style="background-color:#<?php echo $teams['color'][$current_user['spy_team_id']];?>;color:<?php echo $teams['font_color'][$current_user['spy_team_id']];?>;"><img class="c4674" src="resources/<?php if ($current_user['spy_team_id']==$current_user['team_id']){echo "not_";}?>spy_<?php echo $teams['font_color'][$current_user['spy_team_id']];?>.png"><?php echo $teams['team'][$current_user['spy_team_id']];?></section>
  </section>
  <script type="text/javascript">
	<?php if($current_team['updated']==0){ ?>
	var team=document.getElementById('team');
	var update=document.getElementById('update');
	update.disabled=true;

	team.addEventListener('change', disable_update.bind(null));

	function disable_update(){
		if (team.value!=""){
			update.disabled=false;
		}else{
			update.disabled=true;
		}
	}
	<?php } ?>

	<?php
	if ($_SESSION['current_turn']==-1){?>
		document.body.style.background= "#<?php echo $current_spy_team['color'];?> url(\"./resources/it_wallpaper_<?php echo $current_spy_team['font_color']; ?>.png\") repeat";
	<?php }else{
		 if (isset($current_team['id'])){ ?>
			 document.body.style.background= "#<?php echo $current_team['color'];?> url(\"./resources/it_wallpaper_<?php echo $current_team['font_color']; ?>.png\") repeat";
		 <?php }else{ ?>
			 document.body.style.background= "#ffffff url(\"./resources/it_wallpaper_black.png\") repeat";
		 <?php }}?>
	document.body.style.backgroundSize = "1%";
    </script>
</body>
<html>
