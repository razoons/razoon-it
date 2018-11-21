<?php

session_start();

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

	$dataPoints = array();
	$req=$bdd->query('SELECT * FROM reports WHERE game_id="'.$_SESSION['game_id'].'" ORDER BY turn ASC');
	while ($reports_req=$req->fetch()){
		$dataPoints[$reports_req['team_id']][]=(array("x"=>$reports_req['turn']-1,"y"=>$reports_req['prod_before']));
	}


	$req=$bdd->query('SELECT * FROM teams WHERE game_id="'.$_SESSION['game_id'].'"');
  while ($teams_req=$req->fetch()){
	  $teams['team'][$teams_req['id']]=$teams_req['team'];
	  $teams['color'][$teams_req['id']]=$teams_req['color'];
	  $teams['font_color'][$teams_req['id']]=$teams_req['font_color'];
		if ($current_game['current_turn']<>-1){
			$dataPoints[$teams_req['id']][]=(array("x"=>$current_game['current_turn']-1,"y"=>$teams_req['production_progress']));
		}else{
			$dataPoints[$teams_req['id']][]=(array("x"=>$current_game['turns'],"y"=>$teams_req['production_progress']));
		}
  }
	if ($_SESSION['current_turn']<>0){
	  $req=$bdd->query('SELECT * FROM actions WHERE user="'.$_SESSION['user'].'" AND turn='.$_SESSION['current_turn'].'');
	  $action=$req->fetch();
	}


  $admissions=[];
	$req=$bdd->query('SELECT * FROM admissions WHERE target_user="'.$_SESSION['user'].'"');
	while ($admissions_req=$req->fetch()){
		$admissions[]=$admissions_req['team_id'];
	}

	if ($_SESSION['current_turn']==-1){
		$previous_turn=$current_game['turns'];
	}else{
		$previous_turn=$_SESSION['current_turn']-1;
	}
  $req->closeCursor();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
	<link rel="stylesheet" href="./css/style-home.css">
</head>

<body>
  <header class="header-banner">
		<?php include("banner.php"); ?>
		<?php include("nav.php"); ?>
  </header>
	<?php
	if ($_SESSION['current_turn']<>-1){
		include("notifs.php");
	}else{
		include("final.php");
	}		?>
<section class="bdg-sect-header">
<h1>Current Situation</h1>
</section>
	<section class="bdg-sect">
    <?php


    $req=$bdd->query('SELECT * FROM teams WHERE game_id="'.$_SESSION['game_id'].'" ORDER BY production_progress DESC ');
		$rank=1;
		$previous_rank=1;
		$previous_team_progress=100000000;
    while ($list_teams=$req->fetch()){

      $req_users=$bdd->query('SELECT COUNT(*) as user_nbr FROM users WHERE team_id="'.$list_teams['id'].'"');
      $nbr_users_team=$req_users->fetch();
      ?>

		<section class="bdg-action-block-team">
			<div class="rank_team"><h1><?php if($list_teams['production_progress']==$previous_team_progress){echo $previous_rank;}else{echo $rank;$previous_rank=$rank;}?></h1></div>
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
			<div class="list_users">
			<?php $req_users=$bdd->query('SELECT * FROM users WHERE team_id="'.$list_teams['id'].'"');
			while ($list_users=$req_users->fetch()){ ?>
				<section class="bdg-action-block-user" style="background-color:#<?php if ($current_game['current_turn']==-1){echo $teams['color'][$list_users['spy_team_id']];}else{echo $list_teams['color'];} ?>;color:<?php if ($current_game['current_turn']==-1){echo $teams['font_color'][$list_users['spy_team_id']];}else{echo $list_teams['font_color'];} ?>;">
				<h1 class="action-team-title action-user-title"><?php echo $list_users['user']; ?></h1></section>
			<?php } ?>
		</div>
    </section>
  <?php $rank++;$previous_team_progress=$list_teams['production_progress']; } ?>
	<section class="bdg-action-block-team">
		<div class="list_users">
			<?php
			$req=$bdd->query('SELECT * FROM users WHERE game_id="'.$_SESSION['game_id'].'" AND team_id is null');
			while ($list_users=$req->fetch()){
				?>
			<?php if ($current_game['current_turn']<>-1){
				$background="white";
				$color="black";
			}else{
				$background="#".$teams['color'][$list_users['spy_team_id']];
				$color="#".$teams['font_color'][$list_users['spy_team_id']];
			}?>
			<section class="bdg-action-block-user" style="background-color:<?php echo $background;?>;color:<?php echo $color;?>;">
			<h1 class="action-team-title action-user-title"><?php echo $list_users['user']; ?></h1></section>
			<?php  } ?>
	</div>
	</section>

  </section>

	<section class="bdg-sect-header">
	<h1>Results of the team</h1>
	</section>
		<section class="bdg-sect">
			<?php if (($_SESSION['current_turn']<>1) AND ($_SESSION['team_id']<>"")){
			$req_reports=$bdd->query('SELECT * FROM reports WHERE team_id='.$_SESSION['team_id'].' AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.'');
			$reports=$req_reports->fetch();
			$total= $reports['code']+$reports['hack']-$reports['hacked']+$reports['blocking']-$reports['blocked']+$reports['leak'];
			?>
	    <div class="result_team"><img src="./resources/graph.png"></div><span class="title_result"><?php if ($total>=0){ echo 'Your company produced '.$total.' lines of code.</span>';}else{echo 'Your company lost '.abs($total).' lines of code.</span>';}?>
			<br/><br/><?php }?><div id="chartContainer" style="height: 370px; width: 100%;"></div>
		</section>
	<?php
	if ($_SESSION['current_turn']<>1){?>
		<section class="bdg-sect-header">
			<h1>Results of your action</h1>
		</section>
		<section class="bdg-sect">
		<?php
			$req_actions=$bdd->query('SELECT * FROM actions WHERE user="'.$_SESSION['user'].'" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.'');
			$actions=$req_actions->fetch();

			$req_reports = $bdd->query('SELECT * FROM reports WHERE game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND team_id='.$actions['team_id']);
			$reports=$req_reports->fetch();

			if(isset($actions['id']) and !is_null($actions['id'])){
			$req_configuration = $bdd->query('SELECT * FROM configuration');
			$configuration=$req_configuration->fetch();

			$req_reports_code=$bdd->query('SELECT COUNT(*) as total_code FROM actions WHERE action="code" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND target_team_id='.$actions['target_team_id'].'');
			$reports_code=$req_reports_code->fetch();
			if (!isset($reports_code['total_code'])){$reports_code['total_code']=0;}

			$req_reports_hack=$bdd->query('SELECT COUNT(*) as total_hack, SUM(blocked) as total_blocked, target_team_id FROM actions WHERE action="hack" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND team_id='.$actions['team_id'].' AND target_team_id='.$actions['target_team_id'].' GROUP BY target_team_id');
			$reports_hack=$req_reports_hack->fetch();
			if (is_null($reports_hack['total_blocked'])){$reports_hack['total_blocked']=0;}

			$req_reports_firewall=$bdd->query('SELECT COUNT(*) as total_firewall FROM actions WHERE action="firewall" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND team_id='.$actions['team_id'].'');
			$reports_firewall=$req_reports_firewall->fetch();

			$req_reports_blocked=$bdd->query('SELECT COUNT(*) as total_hack, SUM(blocked) as total_blocked FROM actions WHERE action="hack" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND target_team_id='.$actions['team_id'].'');
			$reports_blocked=$req_reports_blocked->fetch();
			if (is_null($reports_blocked['total_blocked'])){$reports_blocked['total_blocked']=0;}

			$req_reports_snitch=$bdd->query('SELECT COUNT(*) as total_snitch FROM actions WHERE action="snitch" AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND target_team_id='.$actions['target_team_id'].'');
			$reports_snitch=$req_reports_snitch->fetch();
			if (!isset($reports_snitch['total_snitch'])){$reports_snitch['total_snitch']=0;}

			$req_reports_leak=$bdd->query('SELECT COUNT(*) as total_leak_blocked FROM actions WHERE snitched=1 AND game_id='.$_SESSION['game_id'].' AND turn='.$previous_turn.' AND team_id='.$actions['team_id'].'');
			$reports_leak=$req_reports_leak->fetch();
			if (!isset($reports_leak['total_leak_blocked'])){$reports_leak['total_leak_blocked']=0;}

			if ($actions['action']=="code"){ ?>
				<div class="result_team"><img src="./resources/code.png"></div><span class="title_result">You were <?php $num = $reports_code['total_code']; if($num == 1){echo "alone";}else{echo "{$num} users";}?> coding for your company.</span>
			<?php }elseif ($actions['action']=="hack"){?>
			<div class="result_team"><img src="./resources/hack.png"></div><span class="title_result">You were <?php $num = $reports_hack['total_hack']; if($num == 1){echo "alone";}else{echo "{$num} users";}?> hacking <b><?php echo $teams['team'][$reports_hack['target_team_id']];?></b> (<?php $num = $reports_hack['total_blocked']; echo $num; if($num == 1){echo " was";}else{echo " were";};?> blocked).<br/>
				<?php
				if($actions['blocked']==0){
					if($reports['hack']==$configuration['hack_gain']){
						?><span class="good">Your hack was totally successful (+<?php echo $reports['hack'];?>).</span><?php
					}
					elseif($reports['hack']==0){
						?><span class="bad">Your hack was successful but didn't steal anything (+0).</span><?php
					}
					else{
						?><span class="good">Your hack was successful but didn't steal as much as hoped (+<?php echo $reports['hack'];?>).</span><?php
					}
				}else{?>
					<span class="bad">Your hack was blocked.</span>
				<?php } ?>
				</span>
			<?php }elseif ($actions['action']=="snitch"){ ?>
			<div class="result_team"><img src="./resources/snitch.png"></div><span class="title_result">You were <?php $num = $reports_snitch['total_snitch']; if($num == 1){echo "alone";} else{echo "{$num} users";}?> snitching (<?php $num = $reports_leak['total_leak_blocked']; echo $num; if($num == 1){echo " leak was";}else{echo " leaks were";}?> discovered).<br/>
			<?php }elseif ($actions['action']=="firewall"){ ?>
				<div class="result_team"><img src="./resources/firewall.png"></div><span class="title_result">You were <?php $num = $reports_firewall['total_firewall']; if($num == 1){echo "alone";}else{echo "{$num} users";};?> protecting <b><?php echo $teams['team'][$actions['team_id']];?></b>.<br/><?php $num = $reports_blocked['total_blocked']; echo $num; if($num==1){echo " hack";}else{echo " hacks";}?> prevented (+<?php echo $reports['blocking'];?>) <br/>
			<?php }}else{?>
				<div class="result_team"><img src="./resources/nothing.png"></div><span class="title_result">You didn't take any action last turn.</span> <?php }?>
			<?php if ($actions['leak_team_id']!=-1 and !is_null($actions['leak_team_id'])){ ?>
			<br/><div class="result_team"><img src="./resources/leak.png"></div><span class="title_result">You leaked <?php if($actions['leak_risk']=="low"){echo "small";}else{echo "huge";}?> piece of code to <?php echo $teams['team'][$actions['leak_team_id']]; }?>

	  </section>
	<?php }?>
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
		var selected=document.querySelectorAll('.selected');
    var img_sprite=document.querySelectorAll('.img_sprite');
		var img_sprite_admission=document.querySelectorAll('.img_sprite_admission');
    var input_action=document.getElementById('action');
    var input_team=document.getElementById('team');


    for (i=0;i<img_sprite.length;i++){
  		img_sprite[i].addEventListener('click', action_click.bind(null,img_sprite[i]));
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
  	}

  	function sprite_out3(obj){
		if (obj.getAttribute("data-selected")=="true"){
			delta=(obj.height)/3;
		}else{
			delta=0;
		}
		obj.style.top="-"+delta+"px";
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


		var chart = new CanvasJS.Chart("chartContainer", {
			title: {
				text: "Software Code Progression"
			},
			axisY: {
				title: "Number of Lines coded",
				minimum:0,
				maximum:<?php echo $current_game['target'];?>
			},
			axisX:{
				title: "Turns",
				interval: 1,
				minimum:0,
				maximum:<?php echo $current_game['turns'];?>
			},
			data: [<?php $i=0; $numItems=count($dataPoints);
			foreach($dataPoints as $key => $value){ ?>
				{
				type: "line",
				color: "#<?php echo $teams['color'][$key];?>",
				showInLegend: true,
				legendText: "<?php echo $teams['team'][$key];?>",
				dataPoints: <?php echo json_encode($dataPoints[$key], JSON_NUMERIC_CHECK); ?>
			}<?php
				if(++$i <> $numItems) {
					echo ",";
				}
			}?>
		]});
		chart.render();

	}

  </script>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
<html>
