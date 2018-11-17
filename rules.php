<?php

session_start();

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
	<link rel="stylesheet" href="./css/style-common.css">
	<link rel="stylesheet" href="./css/style-home.css">
</head>

<body>
  <header class="header-banner">
		<?php include("banner_index.php"); ?>
		<?php include("nav_index.php"); ?>
  </header>
<section class="bdg-sect-header">
<h1>Objective</h1>
</section>
<section class="bdg-sect">
	The objective of this game is to be the first company to develop a new revolutionary software or to be the most advanced at the end of the deadline.
	<br/>The progress of a company corresponds to the number of lines that have been coded.
</section>
<section class="bdg-sect-header">
<h1>Companies</h1>
</section>
<section class="bdg-sect">
	During a game, several companies are fighting to be the most advanced one and you are one of those companies employee.
	<br/>However, there might be spies in each company. (the number of spies per company is determined during the game initialization).
	<br/>The only person knowing the real identity of the spy, is the spy him(her)self. Each user can check its real company in the <b>configuration</b> menu.
	<br/><br/>During the game, a user can be fired from a company and hired by another one. Nevertheless, he/she will always belong to his/he real company.
	<br/><u>A user wins the game if his/her real company wins.</u>
	<br/><br/><i>In the following: if the term used is <u>company</u>, it will refer to the company that employs the user; if the term is <u>real company</u>, it will refer to the company to which the user belongs for real.</i>
	<br/><br/>At the beginning of the game, it is possible to choose a new name for the company and a color. Once updated, it will not be possible to change again.
</section>
<section class="bdg-sect-header">
<h1>Actions</h1>
</section>
<section class="bdg-sect">
	The game is split in x turns. Each turn starts at midnight and ends 24 hours later. The game is in pause during the weekend (the turn starting on Friday morning ends on Sunday night).
	<br/>During a turn, each user can take one of the 4 different actions:
	<br/><br/><div><div class="result_team"><img src="./resources/code.png"></div><div class="explain_1"><b>Code:</b> This action allows the user to code an amount of lines for his/her company.</div></div>
	<br/><div><div class="result_team"><img src="./resources/firewall.png"></div><div class="explain_1"><b>Firewall:</b> This action allows the user to protect his company from attacks (hacks or deals). If his/her firewall blocks an attack, his/her company will gain some lines of code.
	<br/>A firewall is only effective during one turn, even if it didn't block any attack. It can't block more than 1 attack.</div></div>
	<br/><div><div class="result_team"><img src="./resources/hack.png"></div><div class="explain_1"><b>Hack:</b> This action allows the user to hack another company. If the hack is blocked by a firewall, it has no effect but if it's not, his/her company will gain some lines of code.</div></div>
	<br/><div><div class="result_team"><img src="./resources/deal.png"></div><div class="explain_1"><b>Deal:</b> This action allows the user to deal with another company. If the deal is blocked, it has no effect but if it's not blocked, the user steals some lines of code from his/her company and give it to another company.</div></div>
	<br/> The firewalls blocks attacks in this order:
	<ol>
	<li> Minor hacks (small number of hacks coming from the same company)</li>
	<li> Massive hacks (large number of hacks coming from the same company)</li>
	<li> Deals (hacks coming from the attacked company itself)</li>
	</ol>
	
	<br/><br/><b>Particularities:</b>
	<br/>When hacking or dealing a team, a user can not get more than what the team currently has.
	<br/>During the actions resolution, hacks and deals will always be processed before the coding.
	
	
</section>
<section class="bdg-sect-header">
<h1>Exclusions</h1>
</section>
<section class="bdg-sect">
	At any moment, a user can initialize a vote in order to exclude another user from the company.
	<br/>Once initiated, a vote will be requested to all the other users from the company, except the one to be excluded.
	<br/>If at the end of the turn or the next turn, a majority of "yes" or "no" has been counted, the vote is finalized and the user is excluded or not, depending on the result.
	<br/>If at the end of the next turn, there is no majority, the vote is ended and the user is not excluded.
	<br/><br/><b>For a user to be excluded, more than 50% of the team users must vote in favor.</b>
	<br/><br/>If a vote leads to a user exclusion:
	<li>His/her action will be taken into account</li>
	<li>All his/her current vote decisions will be discarded</li>
	<li>He/she will be excluded starting next turn</li>
</section>
<section class="bdg-sect-header">
<h1>Admissions</h1>
</section>
<section class="bdg-sect">
	Once excluded, a user can decide to join a new company by applying to them.
	<br/>Once requested, all the users from this company will have to vote. A user is admitted in the company if at least 50% of the team approves it.
	<br/>The user can send a request to several companies at the same time. If several teams admit the user, priority will be given to the company with less members. Otherwise, first company that has been requested will have the priority.
	<br/>As for the exclusions, if majority has not been reached of the next turn, the admission is cancelled.
</section>
  <script type="text/javascript">
	document.body.style.background= "#0071b8 url(\"./resources/it_wallpaper_white.png\") repeat";
 document.body.style.backgroundSize = "1%";



  </script>
</body>
<html>
