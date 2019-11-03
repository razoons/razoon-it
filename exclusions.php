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
	$total_users=0;
	$total_teams=0;
	$req=$bdd->query('SELECT team_id, COUNT(*) as number FROM `users` WHERE game_id="'.$_SESSION['game_id'].'" GROUP BY team_id');
	while ($number_users=$req->fetch()){
		if ($number_users['team_id']==$current_team['id']){
			$number_users_current_team=$number_users['number'];
		}
		$total_users+=$number_users['number'];
		if ($number_users['team_id']!=NULL){
			$total_teams+=1;
		}
	}
	$req=$bdd->query('SELECT * FROM exclusions WHERE team_id="'.$_SESSION['team_id'].'"');
  while ($list_votes_req=$req->fetch()){
		$list_votes[]=$list_votes_req['target_user'];
	}
  $req=$bdd->query('SELECT * FROM teams');
  while ($teams_req=$req->fetch()){
	  $teams['color'][$teams_req['id']]=$teams_req['color'];
	  $teams['font_color'][$teams_req['id']]=$teams_req['font_color'];
  }
	$req=$bdd->query('SELECT * FROM configuration');
	$configuration=$req->fetch();
	$min_reached=false;
	if ($total_users/$total_teams-$configuration['min_users']==$number_users_current_team){
		$min_reached=true;
	}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
  <link rel="stylesheet" href="./css/style-exclusions.css">
</head>

<body>
  <header class="header-banner">
	<?php include("banner.php"); ?>
	<?php include("nav.php"); ?>
  </header>
	<?php if ($min_reached==true){ ?>
		<section class="bdg-sect-header">
			<h1>No Exclusion possible</h1>
		</section>
		<section class="bdg-sect">
			<span class="message"> You reached your minimum capacity.</span>
		</section>
	<?php }else{ ?>
		<section class="bdg-sect-header">
			<h1>Exclusions</h1>
		</section>
		<section class="bdg-sect">
		<?php

	    $req=$bdd->query('SELECT * FROM exclusions WHERE team_id="'.$current_team['id'].'" AND voter_user="'.$_SESSION['user'].'"');
		if ($req->rowCount() == 0) {
			echo '<span class="message"> There is no pending exclusion </span>';
		}else{
		while ($list_exclusions=$req->fetch()){
			$req_counts=$bdd->query('SELECT COUNT(*) as total_votes FROM exclusions WHERE team_id="'.$list_exclusions['team_id'].'" AND target_user="'.$list_exclusions['target_user'].'"');
			$total_counts=$req_counts->fetch();
			$req_counts=$bdd->query('SELECT COUNT(*) as total_votes FROM exclusions WHERE team_id="'.$list_exclusions['team_id'].'" AND target_user="'.$list_exclusions['target_user'].'" AND vote_result IS NOT NULL');
			$votes_counts=$req_counts->fetch();
			 ?>
	  <section class="bdg-action-block-team">
	    <form id="i6tj4i" method="post" action="update_vote.php">
		<input type=hidden name="id" value="<?php echo $list_exclusions['id']; ?>">
		<input type=hidden name="vote_result" value="">
	        <div class="c7571">Exclusion of <?php echo $list_exclusions['target_user'];?>
	        </div>
	      <section id="irt3xr">
	        <div class="c7693"><?php echo $votes_counts['total_votes'].'/'.$total_counts['total_votes'];?>
	        </div>
	      </section>
				<?php if ($list_exclusions['vote_result']==NULL){?>
	      <div class="img_sprite"><img class="sprite vote_ok" src="resources/vote_ok.png"/></div>
	      <div class="img_sprite"><img class="sprite vote_ko" src="resources/vote_ko.png"/></div>
			<?php } ?>
	    </form>
	  </section>
		<?php } }?>
	</section>
	<section class="bdg-sect">
	  <form method="post" action="initialize_vote.php">
	    <div class="form-group">
	      <label class="label">Initiate a vote against</label><select class="select" name="target_user">
	      <?php
			$req=$bdd->query('SELECT * FROM users WHERE team_id="'.$current_team['id'].'" AND user<>"'.$_SESSION['user'].'"');
			while ($list_users=$req->fetch()){
				if (in_array(ucfirst($list_users['user']),$list_votes)==FALSE){?>
		  <option value="<?php echo ucfirst($list_users['user']);?>"><?php echo ucfirst($list_users['user']);?></option>
		<?php }} ?>
	      </select>
		  <button type="submit" class="button">Send</button>
	    </div>
	  </form>
	</section>
<?php } ?>
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

	var sprites=document.querySelectorAll('.sprite');
	var vote_ok=document.querySelectorAll('.vote_ok');
	var vote_ko=document.querySelectorAll('.vote_ko');

	function sprite_over(obj){
		delta=(obj.height)/2;
		obj.style.top="-"+delta+"px";
	}

	function sprite_out(obj){
		delta=0;
		obj.style.top="-"+delta+"px";
	}

	function click_vote(obj,vote){
		obj.parentElement.parentElement.children[1].value=vote;
		obj.parentElement.parentElement.submit();

	}

	window.onload = function () {

	for (i=0;i<sprites.length;i++){
			sprites[i].addEventListener('mouseover', sprite_over.bind(null,sprites[i]));
			sprites[i].addEventListener('mouseout', sprite_out.bind(null,sprites[i]));
	}
	for (i=0;i<vote_ok.length;i++){
			vote_ok[i].addEventListener('click', click_vote.bind(null,vote_ok[i],1));
			vote_ko[i].addEventListener('click', click_vote.bind(null,vote_ko[i],0));
	}
}
</script>
</body>
<html>
