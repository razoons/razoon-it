<?php

try
{
	// On se connecte à MySQL
	$bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'tonio', 'password');
	// For 000webhostapp.com //$bdd = new PDO('mysql:host=localhost;dbname=id4139222_razoonit;charset=utf8', 'id4139222_razoon', 'Gecko#06');
	//$bdd = new PDO('mysql:host=localhost;dbname=u663106570_razoo;charset=utf8', 'u663106570_tonio', 'Gecko#06');
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
	echo 'Échec lors de la connexion : ' . $e->getMessage();
}

?>
