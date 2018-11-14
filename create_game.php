<?php

	include("connection.php");
	session_start();

?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
	<link rel="stylesheet" href="./css/style-create-game.css">
</head>

<body>
	<header class="header-banner">
		<?php include("banner_index.php"); ?>
		<?php include("nav_index.php"); ?>
	</header>
	<section class="bdg-sect-header">
	<h1>Create a new game</h1>
	</section>
	<section class="bdg-sect">
		<section class="bdg-line">
		</section>
		<?php if (isset($_SESSION['error'])){if ($_SESSION['error']<>""){ echo '<div class="c7542">'.$_SESSION['error'].'</div>';}} ?>
		<section id="i1if6r"><br class="Apple-interchange-newline" />
			<form id="myForm" action="generate_game.php" method="post">
				<div class="form-group"><label class="label">Number of turns</label><input type="text" class="input" name="nbr_turns" /></div>
				<div class="form-group"><label class="label">Number of teams</label><input type="text" class="input" name="nbr_teams" /></div>
				<div class="form-group"><label class="label">Number of spies per team</label><input type="text" class="input" name="min_nbr_spies" placeholder="minimum"/><input type="text" class="input" name="max_nbr_spies" placeholder="maximum"/></div>
				<div class="form-group"><label class="label">Gain for coding</label><input type="text" class="input" name="gain_coding"/></div>
				<div class="form-group"><label class="label">Gain for hacking</label><input type="text" placeholder="coding*1.5" class="input" name="gain_hacking"/></div>
				<div class="form-group"><label class="label">Gain for firewall</label><input type="text" placeholder="coding" class="input" name="gain_firewalling"/></div>
				<!--ces champs vont dégager-->
				<div class="form-group"><label class="label">Gain for bugging</label><input type="text" class="input" name="gain_bugging"/></div>
				<div class="form-group"><label class="label">Gain for dealing</label><input type="text" placeholder="coding" class="input" name="gain_dealing"/></div>
				<!--ces champs vont dégager-->
				<div class="form-group"><label class="label">Coding objective</label><input type="text" placeholder="codingGain*nbr_players" class="input" name="code_objective"/></div>
				<div class="c6293">Players</div>
				<div class="form-group">
					<input type="text" class="input" name="player[]" />
					<div class="img_sprite delete">
						<img class="sprite c7097" src="resources/delete.png"/>
					</div>
				</div>
				<div class="form-group">
					<input type="text" class="input" name="player[]" />
					<div class="img_sprite delete">
						<img class="sprite c7097" src="resources/delete.png"/>
					</div>
				</div>
				<div class="form-group">
					<input type="text" class="input" name="player[]" />
					<div class="img_sprite delete">
						<img class="sprite c7097" src="resources/delete.png"/>
					</div>
				</div>
				<div class="form-group">
					<input type="text" class="input" name="player[]" />
					<div class="img_sprite delete">
						<img class="sprite c7097" src="resources/delete.png"/>
					</div>
				</div>
				<div class="img_sprite add" id="create">
					<img class="sprite c6374" src="resources/add.png" />
				</div>
				<div class="form-group"><button type="submit" class="button">Create</button></div>
			</form>
		</section>
	</section>
	<script type="text/javascript">
	 document.body.style.background= "#0071b8 url(\"../resources/it_wallpaper_white.png\") repeat";
	document.body.style.backgroundSize = "1%";


	var sprites=document.querySelectorAll('.sprite');
	var img_sprite=document.querySelectorAll('.img_sprite');
	var myForm=document.getElementById('myForm');
	var create=document.getElementById('create');
	var deletes=document.querySelectorAll('.delete');

	for (i=0;i<deletes.length;i++){
			deletes[i].addEventListener('click', delete_player.bind(null,deletes[i]));
	}

	function refresh_var(){
		var sprites=document.querySelectorAll('.sprite');
		var img_sprite=document.querySelectorAll('.img_sprite');
		var myForm=document.getElementById('myForm');
		var create=document.getElementById('create');
		var deletes=document.querySelectorAll('.delete');


		for (i=0;i<deletes.length;i++){
				deletes[i].addEventListener('click', delete_player.bind(null,deletes[i]));
		}
		for (i=0;i<sprites.length;i++){
	  		sprites[i].addEventListener('mouseover', sprite_over.bind(null,sprites[i]));
	  		sprites[i].addEventListener('mouseout', sprite_out.bind(null,sprites[i]));
		}

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

	function delete_player(obj){
		myForm.removeChild(obj.parentElement);
	}

	create.addEventListener('click', add_player.bind(null));



	function add_player(){
		//<img class="sprite c7097" src="resources/delete.png" />
		var new_img = document.createElement("img");
		new_img.setAttribute("class","sprite c7097");
		new_img.setAttribute("src","resources/delete.png");
		//<div class="img_sprite delete">
		var new_div1 = document.createElement("div");
		new_div1.setAttribute("class","img_sprite delete");
		new_div1.appendChild(new_img);
		//<input type="text" class="input" />
		var new_input = document.createElement("input");
		new_input.setAttribute("type","text");
		new_input.setAttribute("class","input");
		new_input.setAttribute("name","player[]");
		//<div class="form-group">
		var new_div2 = document.createElement("div");
		new_div2.setAttribute("class","form-group");
		new_div2.appendChild(new_input);
		new_div2.appendChild(new_div1);

		myForm.insertBefore(new_div2,create);
		refresh_var();
	}

	</script>
</body>
<html>
