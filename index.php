<?php

session_start();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
  <link rel="stylesheet" href="./css/style-index.css">
</head>

<body>
  <header class="header-banner">
    <?php include("banner_index.php"); ?>
  </header>
  <section class="bdg-sect">
    <div class="c16080">Welcome to House of Devs !</div>
    <?php if (isset($_SESSION['error'])){if ($_SESSION['error']<>""){ echo '<div class="c16560">'.$_SESSION['error'].'</div>';}} ?>
    <form class="form" action="login_index.php" method="post">
      <div class="form-group"><label class="label">User</label><input placeholder="Type here your user" class="input" name="user"/></div>
      <div class="form-group"><label class="label">Password</label><input type="password" placeholder="Type here your password" class="input" name="password"/></div>
      <div class="form-group"><button type="submit" class="button"/>Login</button></div>
    </form>

   <div class="form-group">
     <button onclick="window.location.href='create_game.php'" class="button new">New Game</button>
   </div>
   <div class="form-group">
     <button onclick="window.location.href='rules.php'" class="button rule">Rules</button>
   </div>
  </section>
  <script type="text/javascript">
  document.body.style.background= "#0071b8 url(\"../resources/it_wallpaper_white.png\") repeat";
 document.body.style.backgroundSize = "1%";
  </script>
</body>
<html>
