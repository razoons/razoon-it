<?php
$req_list_exclusions = $bdd->query('SELECT COUNT(*) as total FROM exclusions WHERE voter_user="'.$_SESSION['user'].'" AND vote_result is NULL');
$list_exclusions=$req_list_exclusions->fetch();
$req_list_admissions = $bdd->query('SELECT COUNT(*) as total FROM admissions WHERE voter_user="'.$_SESSION['user'].'" AND vote_result is NULL');
$list_admissions=$req_list_admissions->fetch();
?>

<div data-gjs="navbar" class="navbar">
  <div class="navbar-container">
    <div id="c5474" class="navbar-burger">
      <div class="navbar-burger-line">
      </div>
      <div class="navbar-burger-line"></div>
      <div class="navbar-burger-line"></div>
    </div>
    <div data-gjs="navbar-items" class="navbar-items-c">
      <nav data-gjs="navbar-menu" class="navbar-menu">
          <a href="home.php" class="navbar-menu-link">Home</a>
        <a href="actions.php" class="navbar-menu-link">Actions</a>
        <a href="exclusions.php" class="navbar-menu-link"><?php if ($list_exclusions['total']>0){?>
          <div class="notif"></div>
        <?php }?>Exclusions</a>
        <a href="admissions.php" class="navbar-menu-link"><?php if ($list_admissions['total']>0){?>
          <div class="notif"></div>
        <?php }?>Admissions</a>
        <a href="configuration.php" class="navbar-menu-link">Configuration</a>
        <a href="logout.php" class="navbar-menu-link">Log out</a>
      </nav>
    </div>
  </div>
</div>
