<?php
$req_list_users= $bdd->query('SELECT * FROM users WHERE user="'.$_SESSION['user'].'"');
$list_users=$req_list_users->fetch();
$req_list_teams=$bdd->query('SELECT * FROM teams WHERE game_id='.$_SESSION['game_id'].' ORDER BY production_progress DESC');
$i=1;
while($list_teams=$req_list_teams->fetch()){
	if ($list_teams['id']==$list_users['spy_team_id']){
		$rank=$i;
	}else{
		$i++;
	}
}

if ($rank==1){
	echo '<section class="bdg-sect-final-good">You won!! Your company came first!</section>';
}else{
	echo '<section class="bdg-sect-final-bad">Unfortunately, your company rank is '.$rank.'</section>';
}

?>