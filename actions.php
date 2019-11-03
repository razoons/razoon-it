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

	$total_users=0;
	$total_teams=0;
	$req=$bdd->query('SELECT team_id, COUNT(*) as number FROM `users` WHERE game_id="'.$_SESSION['game_id'].'" GROUP BY team_id');
	while ($number_users=$req->fetch()){
		$total_users+=$number_users['number'];
		if ($number_users['team_id']!=NULL){
			$total_teams+=1;
		}
	}
	echo $total_teams;
	echo $total_users;

	$req_configuration = $bdd->query('SELECT * FROM configuration');
	$configuration=$req_configuration->fetch();

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
			<div class="helper">Your team produces +<?php echo $configuration['code_gain'];?> lines of code</div>

			<div class="img_sprite" data-type="firewall" data-id=<?php echo $list_teams['id']; ?>>
				<img class="sprite3 sprite" src="resources/firewall.png" <?php if ($action['action']=="firewall"){ echo 'data-selected="true"';}?>/>
			</div>

			<div class="helper">Blocks hacks directed to your team. <?php if($configuration['firewall_gain']<>0){echo "+".$configuration['firewall_gain']." lines of code if blocking. ";} if($configuration['hack_loss']<>0){echo "-".$configuration['hack_loss']." lines of code for the hacking company.";}?></div>
			<div class="img_sprite" data-type="snitch" data-id=<?php echo $list_teams['id']; ?>>
				<img class="sprite3 sprite" src="resources/snitch.png" <?php if ($action['action']=="snitch"){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">Tries to detect a leak from your team. <?php echo $configuration['snitch_low_chance']*100 . "% chance of detecting a low risk leak, " . $configuration['snitch_high_chance']*100 . "% chance for a high risk one." ?></div>

			<?php }else{ ?>
			<div class="img_sprite" data-type="hack" data-id=<?php echo $list_teams['id']; ?>>
				<img class="sprite3 sprite" src="resources/hack.png" <?php if (($action['action']=="hack") AND ($action['target_team_id']==$list_teams['id'])){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">If not blocked, your team gains +<?php echo $configuration['hack_gain'];?> lines of code. <?php if($configuration['code_loss']<>0){echo "-".$configuration['code_loss']." lines of codes for ".$list_teams['team'].".";}?></div>

			<div class="img_sprite" data-type="leak_low" data-id=<?php echo $list_teams['id']; ?>>
				<img class="sprite3 sprite" src="resources/leak_low.png" <?php if (($action['leak_risk']=="low") AND ($action['leak_team_id']==$list_teams['id'])){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">You give +<?php echo $configuration['leak_low'];?> lines of code from your team and send it to <?php echo $list_teams['team'];?>.</div>

			<div class="img_sprite" data-type="leak_high" data-id=<?php echo $list_teams['id']; ?>>
				<img class="sprite3 sprite" src="resources/leak_high.png" <?php if (($action['leak_risk']=="high") AND ($action['leak_team_id']==$list_teams['id'])){ echo 'data-selected="true"';}?>/>
			</div>
			<div class="helper">You give +<?php echo $configuration['leak_high'];?> lines of code from your team and send it to <?php echo $list_teams['team'];?>.</div>

		<?php }}else{ if($total_users/$total_teams+$configuration['max_users']>$nbr_users_team['user_nbr']){?>
			<div class="img_sprite_admission" data-type="admission" data-id=<?php echo $list_teams['id']; ?>>
				<?php if (in_array($list_teams['id'],$admissions)){?>
					<img class="sprite" src="resources/hire_ok.png" data-selected="true">
				<?php }else{?>
					<img class="sprite3 sprite" src="resources/hire.png" data-selected="false">
				<?php } ?>
			</div>
		<?php }}?>
		</section>
	<?php }
	if(isset($current_team['id']))
	{ ?>
		<center>
		<form class="form" id="submit_action" action="submit_action.php" method="post">
			<input type="hidden" id="action" name="action"/>
			<input type="hidden" id="team" name="team"/>
			<input type="hidden" id="leak" name="leak"/>
			<input type="hidden" id="leak_team" name="leak_team"/>
			<div class="img_sprite" data-type="button"><img class="sprite2 sprite" id="button_submit_action" src="resources/submit.png"/></div>
		</form>
	</center>
	<?php }else{ ?>
		<center>
		<form class="form" id="initialize_admission" action="initialize_admission.php" method="post">
			<input type="hidden" id="team_admission" name="team_admission"/>
			<div class="img_sprite_admission" data-type="button"><img class="sprite2 sprite" id="button_submit_admission" src="resources/submit.png"/></div>
		</form>
		</center>
	<?php } ?>
	</section>

	<script type="text/javascript">
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

		var sprites3=document.querySelectorAll('.sprite3');
		var sprites2=document.querySelectorAll('.sprite2');
		var img_sprite=document.querySelectorAll('.img_sprite');
		var helper=document.querySelectorAll('.helper');
		var img_sprite_admission=document.querySelectorAll('.img_sprite_admission');
		var input_action=document.getElementById('action');
		var input_team=document.getElementById('team');
		var input_leak = document.getElementById('leak');
		var input_leak_team = document.getElementById('leak_team');
		var input_admission = document.getElementById('team_admission');
		var button_submit_action = document.getElementById('button_submit_action');
		var submit_action = document.getElementById('submit_action');
		var button_submit_admission = document.getElementById('button_submit_admission');
		var submit_admission = document.getElementById('initialize_admission');

		for (i=0;i<img_sprite.length;i++){
			if(img_sprite[i].getAttribute("data-type")!="button")
				img_sprite[i].addEventListener('click', action_click.bind(null,img_sprite[i]));
		}

		for (i=0;i<helper.length;i++){
			helper[i].style.top=(img_sprite[i].offsetTop+130)+"px";
			helper[i].style.left=img_sprite[i].offsetLeft+"px";
		}

		for (i=0;i<img_sprite_admission.length;i++){
			if (img_sprite_admission[i].getAttribute("data-type")!="button" && img_sprite_admission[i].firstElementChild.getAttribute("data-selected")!="true"){
				img_sprite_admission[i].addEventListener('click', admission_click.bind(null,img_sprite_admission[i]));
			}
		}
		if(submit_action)
			button_submit_action.addEventListener('click', to_submit.bind(null));
		else if(submit_admission)
			button_submit_admission.addEventListener('click', to_submit_admission.bind(null));

		function to_submit(){
			submit_action.submit();
		}

		function to_submit_admission(){
			submit_admission.submit();
		}

		function sprite_over3(obj){
			if (obj.getAttribute("data-selected")=="true"){
				delta=(obj.height)*2/3;
			}else{
				delta=(obj.height)/3;
			}
			obj.style.top="-"+delta+"px";

			if(obj.parentElement.getAttribute("class") == "img_sprite"){
				obj.parentElement.nextElementSibling.style.display="block";
			}

		}

		function sprite_out3(obj){
			if (obj.getAttribute("data-selected")=="true"){
				delta=(obj.height)/3;
			}else{
				delta=0;
			}
			obj.style.top="-"+delta+"px";

			if(obj.parentElement.getAttribute("class") == "img_sprite"){
				obj.parentElement.nextElementSibling.style.display="none";
			}

		}

		function sprite_over2(obj){
			delta=(obj.height)/2;
			obj.style.top="-"+delta+"px";
		}

		function sprite_out2(obj){
			delta=0;
			obj.style.top="-"+delta+"px";
		}

		function unselectOther(obj){
			for(i=0; i<img_sprite.length; i++){
				if(img_sprite[i] != obj && img_sprite[i].getAttribute("data-type")!="button"){
					var dataType = obj.getAttribute("data-type");
					if(dataType == "leak_low" || dataType == "leak_high"){
						dataType = img_sprite[i].getAttribute("data-type");
						if(dataType == "leak_low" || dataType == "leak_high"){
							img_sprite[i].firstElementChild.setAttribute("data-selected", false);
						}
					}
					else{
						dataType = img_sprite[i].getAttribute("data-type");
						if(dataType != "leak_low" && dataType != "leak_high"){
							img_sprite[i].firstElementChild.setAttribute("data-selected", false);
						}
					}
					spriteUpdate(img_sprite[i].firstElementChild);
				}
			}
		}

		function action_click(obj){
			unselectOther(obj);

			if (obj.firstElementChild.getAttribute("data-selected")=="true"){
				obj.firstElementChild.setAttribute("data-selected", false);
			}else{
				obj.firstElementChild.setAttribute("data-selected", true);
			}

			check_selected();
		}

		function check_selected(){
			var selected = document.querySelectorAll('[data-selected=true]');
			input_leak.value = "";
			input_leak_team.value = -1;
			input_team.value = -1;
			input_action.value = "";

			for(i=0; i<selected.length; i++)
			{
				var element = selected[i].parentElement;

				if(element.getAttribute("data-type")=="leak_low")
				{
					input_leak.value = "low";
					input_leak_team.value = element.getAttribute("data-id");
				}
				else if (element.getAttribute("data-type")=="leak_high")
				{
					input_leak.value = "high";
					input_leak_team.value = element.getAttribute("data-id");
				}
				else
				{
					input_team.value = element.getAttribute("data-id");
					input_action.value = element.getAttribute("data-type");
				}
			}
		}

		function admission_click(obj){
			if (obj.firstElementChild.getAttribute("data-selected")=="true"){
				obj.firstElementChild.setAttribute("data-selected", false);
			}else{
				obj.firstElementChild.setAttribute("data-selected", true);
			}

			var selected = document.querySelectorAll('[data-selected=true]');

			input_admission.value = [];
			var admin = [];

			for(i=0; i<selected.length; i++)
			{
				if(selected[i].getAttribute("class") == "sprite3 sprite")
				{
					admin.push(selected[i].parentElement.getAttribute("data-id"));
				}
			}
			input_admission.value = admin.toString();
		}

		function spriteUpdate(obj){
			var new_position = 0;
			if(obj.getAttribute("class") =="sprite3 sprite")
			{
				if (obj.getAttribute("data-selected")=="true"){
					new_position=(obj.height)/3;
				}
				else{
					new_position=0;
				}
			}
			else if(obj.getAttribute("class") =="sprite2 sprite"){
				if (obj.getAttribute("data-selected")=="true"){
					new_position=(obj.height)/2;
				}
				else{
					new_position=0;
				}
			}
			obj.style.top="-"+new_position+"px";
		}

		window.onload = function () {
			var new_position = 0;
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
			check_selected();
		}

	</script>
</body>
<html>
