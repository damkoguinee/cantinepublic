<?php
require '_header.php';
$_SESSION['pseudo'] = $_POST['pseudo'];
$_etat = 'connecté';

$bdd='debutjournee';   

$DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `datev` date NOT NULL,
    `etat` int(1) DEFAULT '0',
    PRIMARY KEY (`id`)
)");
	
$connexion = $DB->querys('SELECT * FROM personnel WHERE pseudo =:Pseudo', 
	array('Pseudo'=>$_POST['pseudo']));

$password=password_verify($_POST['mdp'], $connexion['mdp']);

$_SESSION['idpseudo']=$connexion['id'];

$_SESSION['level']=$connexion['level'];

$_SESSION['statut']=$connexion['statut'];

if (empty($connexion)){
	header('Location:index.php');
}else{

	if (!$password){

		header('Location:index.php');

	}else{
	
		$etat='1';
		$prodjournee= $DB->querys("SELECT * FROM debutjournee WHERE etat='{$etat}'");
		if (empty($prodjournee['etat'])) {

			header('Location: journee.php');

		}else{
			$_SESSION['datev']=$prodjournee['datev'];
		
			header('Location: choix1.php');
		}
	}


	
}?>