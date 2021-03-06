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
	$req=$bdd->query('SELECT * FROM admissions WHERE team_id="'.$_SESSION['team_id'].'"');
  while ($list_votes_req=$req->fetch()){
		$list_votes[]=$list_votes_req['target_user'];
	}
  $req=$bdd->query('SELECT * FROM teams');
  while ($teams_req=$req->fetch()){
	  $teams['color'][$teams_req['id']]=$teams_req['color'];
	  $teams['font_color'][$teams_req['id']]=$teams_req['font_color'];
  }
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
  <link rel="stylesheet" href="./css/style-admissions.css">
</head>

<body>
  <header class="header-banner">
	<?php include("banner.php"); ?>
	<?php include("nav.php"); ?>
  </header>
	<section class="bdg-sect-header">
	<h1>Admissions</h1>
	</section>
  <section class="bdg-sect">
	<?php

    $req=$bdd->query('SELECT * FROM admissions WHERE team_id="'.$current_team['id'].'" AND voter_user="'.$_SESSION['user'].'"');
	if ($req->rowCount() == 0) {
		echo '<span class="message"> There is no pending admission </span>';
	}else{
	while ($list_admissions=$req->fetch()){
		$req_counts=$bdd->query('SELECT COUNT(*) as total_votes FROM admissions WHERE team_id="'.$list_admissions['team_id'].'" AND target_user="'.$list_admissions['target_user'].'"');
		$total_counts=$req_counts->fetch();
		$req_counts=$bdd->query('SELECT COUNT(*) as total_votes FROM admissions WHERE team_id="'.$list_admissions['team_id'].'" AND target_user="'.$list_admissions['target_user'].'" AND vote_result IS NOT NULL');
		$votes_counts=$req_counts->fetch();
		 ?>
  <section class="bdg-action-block-team">
    <form id="i6tj4i" method="post" action="update_admission.php">
	<input type=hidden name="id" value="<?php echo $list_admissions['id']; ?>">
	<input type=hidden name="vote_result" value="">
        <div class="c7571">Admission of <?php echo ucfirst($list_admissions['target_user']);?>
        </div>
      <section id="irt3xr">
        <div class="c7693"><?php echo $votes_counts['total_votes'].'/'.$total_counts['total_votes'];?>
        </div>
      </section>
			<?php if ($list_admissions['vote_result']==NULL){?>
      <div class="img_sprite"><img class="sprite vote_ok" src="resources/vote_ok.png"/></div>
      <div class="img_sprite"><img class="sprite vote_ko" src="resources/vote_ko.png"/></div>
		<?php } ?>
    </form>
  </section>
<?php }} ?>
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
