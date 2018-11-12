<link rel="stylesheet" href="./css/style-sub_header.css">
<section class="c4589">
    <section class="c5221">
      <b>Welcome <?php echo $_SESSION['user']?></b>
	  <br/>
		from <?php if (isset($current_team['team'])){echo $current_team['team'];}else{echo 'Pole Emploi';}?>
    </section>
    <section class="c6689">
      <b>Turn</b>
		<br/>
		<span><?php echo $current_game['current_turn']."/".$current_game['turns'];?></span>
    </section>
  </section>