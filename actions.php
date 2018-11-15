<?php

session_start();
//test
if (isset($_SESSION['user'])){
}else{
	header ("Location: index.php");
}

	include("connection.php");

  $req=$bdd->query('SELECT * FROM games WHERE id="'.$_SESSION['game_id'].'"');
  $current_game=$req->fetch();
  if ($_SESSION['team_id']<>NULL){
	  $req=$bdd->query('SELECT * FROM teams WHERE id="'.$_SESSION['team_id'].'"');
	$current_team=$req->fetch();
  }
	$req=$bdd->query('SELECT * FROM teams WHERE id="'.$_SESSION['spy_team_id'].'"');
	$current_spy_team=$req->fetch();
  $req=$bdd->query('SELECT * FROM actions WHERE user="'.$_SESSION['user'].'" AND turn='.$_SESSION['current_turn'].'');
  $action=$req->fetch();

  $admissions=[];
	$req=$bdd->query('SELECT * FROM admissions WHERE target_user="'.$_SESSION['user'].'"');
	while ($admissions_req=$req->fetch()){
		$admissions[]=$admissions_req['team_id'];
	}

  $req->closeCursor();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
	<link rel="stylesheet" href="./css/style-actions.css">
</head>

<body>
  <header class="header-banner">
		<?php include("banner.php"); ?>
		<?php include("nav.php"); ?>
  </header>
<section class="bdg-sect-header">
<h1>Actions</h1>
</section>
	<section class="bdg-sect">
    <?php


    $req=$bdd->query('SELECT * FROM teams WHERE game_id="'.$_SESSION['game_id'].'" ORDER BY production_progress DESC ');
    while ($list_teams=$req->fetch()){
      $req_users=$bdd->query('SELECT COUNT(*) as user_nbr FROM users WHERE team_id="'.$list_teams['id'].'"');
      $nbr_users_team=$req_users->fetch();
      ?>
    <section class="bdg-action-block-team">
      <section class="bdg-action-team" style="background-color:#<?php echo $list_teams['color']; ?>; color:<?php echo $list_teams['font_color']; ?>;">
        <section id="c7594">
          <h1 class="action-team-title"><?php echo $list_teams['team']?></h1>
        </section>
        <section class="action-team-users">
          <img class="c8935" src="resources/user_<?php echo $list_teams['font_color']; ?>.png" />
          <div class="c9104"><?php echo $nbr_users_team['user_nbr']; ?></div>
        </section>
        <section id="" class="action-team-progress">
          <div class="c12100"><?php echo $list_teams['production_progress']." / ".$current_game['target'];?></div>
          <section class="action-team-progress-empty" style="border-color:<?php echo $list_teams['font_color']; ?>;">
            <section class="action-team-progress-full" style="width:<?php echo floor($list_teams['production_progress']/$current_game['target']*100);?>%;background-color:<?php echo $list_teams['font_color']; ?>;"></section>
          </section>
        </section>
      </section>
	  <?php if (isset($current_team['id'])){
		if ($list_teams['id']==$current_team['id']){ ?>
      <div class="img_sprite" data-type="code" data-id=<?php echo $list_teams['id']; ?>>
        <img class="sprite3 sprite" src="resources/code.png" <?php if ($action['action']=="code"){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">Your team produces +<?php echo $current_game['code_gain'];?> lines of code</div>
      <div class="img_sprite" data-type="firewall" data-id=<?php echo $list_teams['id']; ?>>
        <img class="sprite3 sprite" src="resources/firewall.png" <?php if ($action['action']=="firewall"){ echo 'data-selected="true"';}?>/>
      </div>
			<div class="helper">Blocks hacks & deals directed to your team.<?php if($current_game['firewall_gain']<>0){echo "+".$current_game['firewall_gain']." lines of code if blocking";}?></div>
	<div class="img_sprite" data-type="bug" data-id=<?php echo $list_teams['id']; ?>>
        <img class="sprite3 sprite" src="resources/bug.png" <?php if ($action['action']=="bug"){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">Your team produces -<?php echo $current_game['bug_gain'];?> lines of code</div>
    <?php }else{ ?>
      <div class="img_sprite" data-type="hack" data-id=<?php echo $list_teams['id']; ?>>
        <img class="sprite3 sprite" src="resources/hack.png" <?php if (($action['action']=="hack") AND ($action['target_team_id']==$list_teams['id'])){ echo 'data-selected="true"';}?>/>
      </div>
			<div class="helper">If not blocked, your team produces +<?php echo $current_game['hack_gain'];?> lines of code</div>
      <div class="img_sprite" data-type="deal" data-id=<?php echo $list_teams['id']; ?>>
        <img class="sprite3 sprite" src="resources/deal.png" <?php if (($action['action']=="deal") AND ($action['target_team_id']==$list_teams['id'])){ echo 'data-selected="true"';}?>/>
      </div>
			<div class="helper">If not blocked, you steal +<?php echo $current_game['deal_gain'];?> lines of code from your team</div>
			<form class="form" id="submit_action" action="submit_action.php" method="post">
		    <input type="hidden" id="action" name="action"/>
		    <input type="hidden" id="team" name="team"/>
		  </form>
	  <?php }
	  }else{?>
		<div class="img_sprite_admission" data-type="admission" data-id=<?php echo $list_teams['id']; ?>>
		<?php if (in_array($list_teams['id'],$admissions)){?>
			<img class="sprite" src="resources/hire_ok.png" data-selected="true">
		<?php }else{?>
			<img class="sprite2 sprite" src="resources/hire.png">
		<?php } ?>
		</div>
		<form class="form" id="initialize_admission" action="initialize_admission.php" method="post">
	    <input type="hidden" id="team" name="team"/>
	  </form>
	<?php }?>
    </section>
  <?php } ?>
  </section>
  <script type="text/javascript">
	 <?php
	 if ($_SESSION['current_turn']==-1){?>
		 document.body.style.background= "#<?php echo $current_spy_team['color'];?> url(\"../resources/it_wallpaper_<?php echo $current_spy_team['font_color']; ?>.png\") repeat";
	 <?php }else{
	 		if (isset($current_team['id'])){ ?>
				document.body.style.background= "#<?php echo $current_team['color'];?> url(\"../resources/it_wallpaper_<?php echo $current_team['font_color']; ?>.png\") repeat";
	 		<?php }else{ ?>
				document.body.style.background= "#ffffff url(\"../resources/it_wallpaper_black.png\") repeat";
	 		<?php }}?>

	document.body.style.backgroundSize = "1%";


    var sprites3=document.querySelectorAll('.sprite3');
		var sprites2=document.querySelectorAll('.sprite2');
		var selected=document.querySelectorAll('.selected');
    var img_sprite=document.querySelectorAll('.img_sprite');
		var helper=document.querySelectorAll('.helper');
		var img_sprite_admission=document.querySelectorAll('.img_sprite_admission');
    var input_action=document.getElementById('action');
    var input_team=document.getElementById('team');


    for (i=0;i<img_sprite.length;i++){
  		img_sprite[i].addEventListener('click', action_click.bind(null,img_sprite[i]));
			helper[i].style.top=(img_sprite[i].offsetTop+130)+"px";
			helper[i].style.left=img_sprite[i].offsetLeft+"px";
  	}

		for (i=0;i<img_sprite_admission.length;i++){
			if (img_sprite_admission[i].firstElementChild.getAttribute("data-selected")=="true"){
			}else{
  			img_sprite_admission[i].addEventListener('click', admission_click.bind(null,img_sprite_admission[i]));
			}
  	}

    function sprite_over3(obj){
		if (obj.getAttribute("data-selected")=="true"){
			delta=(obj.height)*2/3;
		}else{
			delta=(obj.height)/3;
		}
		obj.style.top="-"+delta+"px";
		obj.parentElement.nextElementSibling.style.display="block";
  	}

  	function sprite_out3(obj){
		if (obj.getAttribute("data-selected")=="true"){
			delta=(obj.height)/3;
		}else{
			delta=0;
		}
		obj.style.top="-"+delta+"px";
		obj.parentElement.nextElementSibling.style.display="none";
  	}

	function sprite_over2(obj){
		delta=(obj.height)/2;
		obj.style.top="-"+delta+"px";
  	}

  	function sprite_out2(obj){
		delta=0;
		obj.style.top="-"+delta+"px";
  	}

    function action_click(obj){
			if (obj.firstElementChild.getAttribute("data-selected")=="true"){
				input_action.value="remove_action";
			}else{
				input_action.value=obj.getAttribute("data-type");
			}
			input_team.value=obj.getAttribute("data-id");
			document.getElementById("submit_action").submit();
    }

		function admission_click(obj){
			input_team.value=obj.getAttribute("data-id");
			document.getElementById("initialize_admission").submit();
    }

	window.onload = function () {

	for (i=0;i<sprites3.length;i++){
  		sprites3[i].addEventListener('mouseover', sprite_over3.bind(null,sprites3[i]));
  		sprites3[i].addEventListener('mouseout', sprite_out3.bind(null,sprites3[i]));
		if (sprites3[i].getAttribute("data-selected")=="true"){
			new_position=(sprites3[i].height)/3;
			sprites3[i].style.top="-"+new_position+"px";
		}
  	}

	for (i=0;i<sprites2.length;i++){

		if (sprites2[i].getAttribute("data-selected")=="true"){
			new_position=(sprites2[i].height)/2;
			sprites2[i].style.top="-"+new_position+"px";
		}else{
			sprites2[i].addEventListener('mouseover', sprite_over2.bind(null,sprites2[i]));
  		sprites2[i].addEventListener('mouseout', sprite_out2.bind(null,sprites2[i]));
		}
  	}

	}

  </script>
</body>
<html>
