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

	$dataPoints = array();
	$req=$bdd->query('SELECT * FROM reports WHERE game_id="'.$_SESSION['game_id'].'"');
	while ($reports_req=$req->fetch()){
		$dataPoints[$reports_req['team_id']][]=(array("x"=>$reports_req['turn']-1,"y"=>$reports_req['prod_before']));
	}

  $req=$bdd->query('SELECT * FROM teams WHERE game_id="'.$_SESSION['game_id'].'"');
  while ($teams_req=$req->fetch()){
	  $teams['color'][$teams_req['id']]=$teams_req['color'];
	  $teams['font_color'][$teams_req['id']]=$teams_req['font_color'];
		$teams['team'][$teams_req['id']]=$teams_req['team'];
		if ($current_game['current_turn']<>-1){
			$dataPoints[$teams_req['id']][]=(array("x"=>$current_game['current_turn'],"y"=>$teams_req['production_progress']));
		}
  }


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
  <link rel="stylesheet" href="./css/style-companies.css">
</head>

<body>
  <header class="header-banner">
	<?php include("banner.php"); ?>
	<?php include("nav.php"); ?>
  </header>
	<section class="bdg-sect-header">
	<h1>Companies</h1>
	</section>
  <section class="bdg-sect">
    <?php
	$req_teams=$bdd->query('SELECT * FROM teams WHERE game_id="'.$_SESSION['game_id'].'"');
	while ($list_teams=$req_teams->fetch()){ ?>
	<section class="bdg-action-block-team" style="border-color:#<?php echo $list_teams['color']; ?>;color:<?php echo $list_teams['font_color'];?>;">
      <form action="update_team.php" method="post">
        <section id="i7u8fr" class="bdg-action-team" style="background-color:#<?php echo $list_teams['color']; ?>;">
          <section id="c7594">
            <h1 id="iirudi" class="action-team-title"><?php echo $list_teams['team']; ?></h1>
			<?php if(($list_teams['id']==$current_team['id']) AND ($current_team['updated']==0)){ ?>
			<input placeholder="Type your new company name" class="textarea new_name_input" name="team"/>
            <div class="c8062"><input type="color" value="#<?php echo $list_teams['color']; ?>" name="color"/></div>
			<button class="button">Update</button>
			<?php } ?>
			</section>
        </section>
        <?php $req_users=$bdd->query('SELECT * FROM users WHERE team_id="'.$list_teams['id'].'"');
		while ($list_users=$req_users->fetch()){ ?>
			<section class="bdg-action-block-user" style="background-color:#<?php if ($current_game['current_turn']==-1){echo $teams['color'][$list_users['spy_team_id']];}else{echo $list_teams['color'];} ?>;color:<?php if ($current_game['current_turn']==-1){echo $teams['font_color'][$list_users['spy_team_id']];}else{echo $list_teams['font_color'];} ?>;">
			<h1 class="action-team-title action-user-title"><?php echo ucfirst($list_users['user']); ?></h1></section>
		<?php } ?>
      </form>
    </section>
	<?php } ?>
	<div id="chartContainer" style="height: 370px; width: 100%;"></div>
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



	window.onload = function () {

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
