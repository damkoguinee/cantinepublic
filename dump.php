<?php require 'header1.php';

    $difference=$panier->montantCompte(1)-$_SESSION['fcaisse'];

    $datev=(new dateTime($_SESSION['datev']))->format("Y-m-d");

    if ($_SESSION['date1']==$_SESSION['date2']) {

      $DB->delete('DELETE FROM banque WHERE DATE_FORMAT(date_versement, \'%Y-%m-%d\')=? and  libelles=?', array($datev,'cloture'));

      $DB->insert('INSERT INTO banque (id_banque, libelles, numero, montant, date_versement) VALUES (?, ?, ?, ?, ?)', array(1, 'cloture', 1, -$difference, $datev));

     
    }    
    

    $DB->delete('DELETE FROM debutjournee');

    $dateformat=(new dateTime($_SESSION['datev']))->format("Ymd");

    $etat='credit';

    $restep=$DB->querys("SELECT SUM(Total) AS totc, SUM(montantpaye) AS montc, SUM(reste) as reste, mode_payement, SUM(remise) AS remc, sum(fraisup) as frais FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\")= '{$dateformat}' AND etat= '{$etat}'");

    $credclient_gnf= $restep['reste'];

    $_SESSION['datevc']=(new dateTime($_SESSION['datev']))->format("Ymd");
    $_SESSION['datedujour']=(new dateTime($_SESSION['datev']))->format("d/m/Y à H:i");

    foreach ($panier->email as $valuem) {

	    $destinataire=$valuem;
	      $message='bonjour,
	      Compte Rendu du '.$_SESSION['datedujour'].' 
	      Nombre totale des ventes: '.$panier->nbreVente($dateformat, $dateformat).',
	      Montant total des ventes: '.number_format($panier->venteTot($_SESSION['datevc'], $_SESSION['datevc']),0,',',' ').',
	      Crédit Client: '.number_format($credclient_gnf,0,',',' ').',
	      Cloturé par '.$panier->nomPersonnel($_SESSION["idpseudo"])[1];
	      ini_set( 'display_errors', 1);
	      error_reporting( E_ALL );
	      $from = "codebar@damkoguinee.com";
	      $to =$destinataire;
	      $subject = "situation du jour";
	      $message = $message;
	      $headers = "From:" . $from;
	      mail($to,$subject,$message, $headers);
	}

  //$panier->dumpMySQL("localhost", "root", "", "europe", 3);

  	header("Location: deconnexion.php");
?>