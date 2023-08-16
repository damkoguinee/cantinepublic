<?php
	require 'db.class.php';
	require 'panier.class.php';
	require 'rapportClass.php';
	$DB = new DB();
	$panier = new panier($DB);
	$rapport= new Rapport($DB);
?>