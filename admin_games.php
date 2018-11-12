<?php

	include("connection.php");
	session_start();

?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
	<link rel="stylesheet" href="./css/style-admin-games.css">
</head>

<body>
	<header class="header-banner">
		<?php include("banner_index.php"); ?>
		<?php include("nav_index.php"); ?>
	</header>
	<section class="bdg-sect-header">
	<h1>Administrate games</h1>
	</section>
	<section class="bdg-sect">
		<section id="i1if6r"><br class="Apple-interchange-newline" />
			<form id="myForm" action="remove_game.php" method="post">
				<input type="hidden" id="game_id" name="game_id" value=""/>
				<div class="c6293">Games</div>
				<?php
				$req_list_games = $bdd->query('SELECT * FROM games');
				while ($list_games=$req_list_games->fetch()){?>
				<div class="separator"></div>
				<div class="form-group">
					<div class="game_header">
						<div class="game_id"><?php echo $list_games['id'];?></div>
						<h1>Turn <?php echo $list_games['current_turn'].'/'.$list_games['turns'];?></h1>
						<div class="img_sprite delete" data-id="<?php echo $list_games['id'];?>">
							<img class="sprite c7097" src="resources/delete.png"/>
						</div>
					</div>
					<?php
					$req_list_teams = $bdd->query('SELECT * FROM teams WHERE game_id='.$list_games['id'].'');
					while ($list_teams=$req_list_teams->fetch()){?>
					<div class="game_body">
						<div><div class="team_name" style="background-color:#<?php echo $list_teams['color'];?>;color:<?php echo $list_teams['font_color'];?>;"><?php echo $list_teams['team'];?></div>
						<?php $req_list_users = $bdd->query('SELECT * FROM users WHERE game_id='.$list_games['id'].' and team_id='.$list_teams['id'].'');
						while ($list_users=$req_list_users->fetch()){?>
						<div class="team_member" style="background-color:#<?php echo $list_teams['color'];?>;color:<?php echo $list_teams['font_color'];?>;"><?php echo $list_users['user'];?></div>
					<?php }?>
					</div></div>
				<?php }?>
				</div>
			<?php }?>
			</form>
		</section>
	</section>
	<script type="text/javascript">
	var sprites=document.querySelectorAll('.sprite');
	var img_sprite=document.querySelectorAll('.img_sprite');
	var myForm=document.getElementById('myForm');
	var game_id=document.getElementById('game_id');
	var deletes=document.querySelectorAll('.delete');

	for (i=0;i<deletes.length;i++){
			deletes[i].addEventListener('click', delete_game.bind(null,deletes[i]));
	}




	window.onload = function () {
		for (i=0;i<sprites.length;i++){
	  		sprites[i].addEventListener('mouseover', sprite_over.bind(null,sprites[i]));
	  		sprites[i].addEventListener('mouseout', sprite_out.bind(null,sprites[i]));
		}
	}

	function sprite_over(obj){
		delta=(obj.height)/2;
		obj.style.top="-"+delta+"px";
	}

	function sprite_out(obj){
		delta=0;
		obj.style.top="-"+delta+"px";
	}

	function delete_game(obj){
		game_id.value=obj.getAttribute("data-id");
		myForm.submit();
	}



	</script>
</body>
<html>
