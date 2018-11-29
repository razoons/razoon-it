<?php
$req_list_notifications = $bdd->query('SELECT * FROM notifications WHERE game_id="'.$_SESSION['game_id'].'"');
while ($list_notifications=$req_list_notifications->fetch()){
	$req_list_teams = $bdd->query('SELECT * FROM teams WHERE id="'.$list_notifications['team_id'].'"');
	$list_teams=$req_list_teams->fetch();
	if ($list_notifications['user']==$_SESSION['user']){
		if ($list_notifications['type']=="exclusion"){
			echo '<section class="bdg-sect-notif-bad">You have been excluded from '.$list_teams['team'].'</section>';
		}elseif ($list_notifications['type']=="admission"){
			echo '<section class="bdg-sect-notif-good">You have been admitted in '.$list_teams['team'].'</section>';
		}
	}else if ($list_notifications['team_id']==$_SESSION['team_id']){
		if ($list_notifications['type']=="exclusion"){
			echo '<section class="bdg-sect-notif-bad">'.ucfirst($list_notifications['user']).' has been excluded from your company</section>';
		}elseif ($list_notifications['type']=="admission"){
			echo '<section class="bdg-sect-notif-good">Your company admitted '.ucfirst($list_notifications['user']).' as a new member</section>';
		}
	}
}
?>
