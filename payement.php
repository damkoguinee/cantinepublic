<?php
require '_header.php';

$_SESSION['date']=date("Ymd");  
$dates = $_SESSION['date'];
$dates = new DateTime( $dates );
$dates = $dates->format('Ymd'); 
$_SESSION['date']=$dates;
$_SESSION['date1']=$dates;
$_SESSION['date2']=$dates;
$_SESSION['dates1']=$dates;

$date = date('y') . '0000';

$maximum = $DB->querys('SELECT max(id) AS max_id FROM payement ');


$numero_commande = $date + $maximum['max_id'] + 1;

$numerot= $DB->querys("SELECT (COUNT(id)+1) AS nbre_cmd  FROM payement where DATE_FORMAT(date_cmd, \"%Y%m%d\")='{$_SESSION['date1']}'");

$numticket=$numerot['nbre_cmd'];

$init='cod';
//$init='elm';
//$init='dao';
//$init='kmco';

$_SESSION['num_cmdp']=$init.$numero_commande;

$pseudo=$_SESSION['idpseudo'];
$coment=$_POST['coment'];
$prodvente = $DB->querys("SELECT *FROM validvente where pseudop='{$pseudo}' ");

if (empty($prodvente['fraisup'])) {
	$fraisup=0;
}else{
	$fraisup=$prodvente['fraisup'];
}

$total=$panier->totalcom()+$fraisup;

$totalcomverif=number_format($panier->totalcom(),0,',','');
$totalcomverif=$panier->espace($totalcomverif);
$totachatverif=number_format($_POST['totachat'],0,',','');
$totachatverif=$panier->espace($totachatverif);

if ($totalcomverif==$totachatverif) {//Vérification du totalcmd
	
	$position=strtolower($_SESSION['mange']);

	if ($position=='livraison') {
		$etatliv='nonlivre';
	}else{
		$etatliv='livre';
	}

	unset($_SESSION['$quantite_rest']); //pour vider en cas de commande > au stock

	$client=$_POST['clientvip'];

    $numclient= $client;

    $heure=date("H:i:s");

    
    	
    $datev=$_SESSION['datev'].' '.$heure;
    	
    

	if ($panier->espace($_POST['reste'])<=0) {

		//************************ GESTION TABLE PAYEMENT PAYEMENT TOTALITE***************

		$prodvente = $DB->querys("SELECT *FROM validvente where pseudop='{$pseudo}' ");
		$etat="totalite";
		$montantpaye=$prodvente['montantpgnf']+$_POST['reste'];
		$remise=$prodvente['remise'];
		$reste=0;

		if (!empty($_POST['clientvip'])) {

			if (empty($datev)) {

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($init.$numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment));
			}else{

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($init.$numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment, $datev));
			}

			if (empty($datev)) {
				$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$init.$numero_commande));

			}else{

				$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$init.$numero_commande, $datev));

			}

			//ici pour l'email

			if (!empty($panier->nomClientad($numclient)[3])) {

				ini_set( 'display_errors', 1);
				error_reporting( E_ALL );
				$from = "logescom@damkoguinee.com";
				$to =strtolower($panier->nomClientad($numclient)[3]);
				$subject = "votre facture numéro ".$init.$numero_commande;
				$message = "Cher Client, Merci de trouver votre facture de ".$total." GNF en cliquant sur ce lien http://koulamatco.com/logescom/accueilclient.php?lienclient=".$numclient;
				$headers = "From:" . $from;
				mail($to,$subject,$message, $headers);
			}

	        require 'fraisup.php';
		          		
		}

	}else{


		//************************ GESTION TABLE PAYEMENT PAYEMENT CREDIT***********************************

		$prodvente = $DB->querys("SELECT *FROM validvente where pseudop='{$pseudo}' ");	

		if (empty($prodvente['montantpgnf'])) {
			$montantpaye=0;
		}else{	
			$montantpaye=$prodvente['montantpgnf'];
			
		}

		$remise=$prodvente['remise'];
		$reste=$_POST['reste']+$fraisup;
		$etat="credit";	

		if (empty($client)) {

			header("Location: accueil.php");

		}else{

			if (empty($datev)) {

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($init.$numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment));
			}else{

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($init.$numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment, $datev));
			}

			if (empty($datev)) {

		    	$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$init.$numero_commande));
			}else{

		    	$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$init.$numero_commande, $datev));

			}

        	if (empty($datev)) {
        		$DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($numclient, -$reste, "Reste a payer facture", $init.$numero_commande));
        	}else{

        		$DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($numclient, -$reste, "Reste a payer facture", $init.$numero_commande, $datev));

        	}
				

				

	        if (!empty($panier->nomClientad($numclient)[3])) {

				ini_set( 'display_errors', 1);
				error_reporting( E_ALL );
				$from = "logescom@damkoguinee.com";
				$to =strtolower($panier->nomClientad($numclient)[3]);
				$subject = "votre facture numéro ".$init.$numero_commande;
				$message = "Cher Client, votre commande est en cours de preparation";
				$headers = "From:" . $from;
				mail($to,$subject,$message, $headers);
			}

	        require 'fraisup.php';

		    
	    }	

	}

	//******************** GESTION TABLE COMMANDE PAYEMENT TOTALITE*********************

	$products= $DB->query("SELECT id_produit as id, stock.nom as designation, stock.quantity as qtites, validpaie.quantite as qtite, pvente, prix_achat, prix_revient, prix_vente, stock.type as type, idingredient, qtiteingredient FROM validpaie inner join stock on stock.id=id_produit where pseudov='{$_SESSION['idpseudo']}' order by(validpaie.id)");

	$cumbenef=0;

	foreach($products as $product){
		$designation=$product->designation;
		$id=$product->id;			
		$price_achat=$product->prix_achat;
		$price_revient=$product->prix_revient;
		$price_vente=$product->pvente;
		$quantity=$product->qtite;
		//$qtites=$product->qtites;			

		$benefice=$product->pvente-$product->prix_revient;
		$cumbenef+=$benefice;

		$etat="totalite";
				
		if (empty($_POST['clientvip'])) {

			header("Location: accueil.php");
			
        
		}else{

			$prodrecette= $DB->query("SELECT idprod, iding, qtite as qtitep FROM prodingredient where idprod='{$id}'");

			foreach ($prodrecette as $value) {

				$stockgen= $DB->querys("SELECT quantity as qtite, qtiteint FROM stock where id='{$value->iding}'");

				$stockdet= $DB->querys("SELECT id, quantity as qtite FROM stockdetail where nom='{$value->iding}'");

				$restedet=$stockdet['qtite'];

				$qtitevendu=$value->qtitep;

				$qtiteengros=$stockgen['qtite'];

				$qtiteint=$stockgen['qtiteint'];

				$qtitedim=$qtiteengros-1;

				$qtiteaug=$restedet+$qtiteint;

				if ($qtitevendu>=$restedet) {
					
					$DB->insert('UPDATE stock SET quantity = ? WHERE id = ?', array($qtitedim, $value->iding));

					if (empty($_POST['datev'])) {
						$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($value->iding, $init.$numero_commande, 'sortiedet', -1));
					}else{

						$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, ?)', array($value->iding, $init.$numero_commande, 'sortiedet', -1, $datev));
					}

					$reste=$qtiteaug-$quantity*$qtitevendu;

					if (empty($stockdet['id'])) {

						$DB->insert('INSERT INTO stockdetail (nom, quantity) VALUES(?, ?)', array($value->iding, $reste));
					}else{

						$DB->insert('UPDATE stockdetail SET quantity = ? WHERE nom = ?', array($reste, $value->iding));
					}

				}else{

					$reste=$restedet-$quantity*$qtitevendu;

					$DB->insert('UPDATE stockdetail SET quantity = ? WHERE nom = ?', array($reste, $value->iding));

				}

				
			}


			
			$DB->insert('INSERT INTO commande (id_produit, prix_vente, prix_achat, prix_revient, quantity, num_cmd, num_ticket, id_client) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($id, $price_vente, $price_achat, $price_revient, $quantity, $init.$numero_commande, $numticket, $numclient));

			if (empty($_POST['datev'])) {
				$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, $init.$numero_commande, 'sortie', -$quantity));
			}else{

				$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, ?)', array($id, $init.$numero_commande, 'sortie', -$quantity, $datev));
			}

			//************************************gestion detail**********************

			$prodstock= $DB->querys("SELECT nom, quantity as qtites, type, genre, taille FROM stock where id='{$id}'");

			if ($prodstock['genre']=='boissons' and $prodstock['genre']=='aromes' and $prodstock['taille']=='bouteille') {

				$qtites=$prodstock['qtites'];

				$quantite=($qtites)-$quantity;
				$DB->insert('UPDATE stock SET quantity = ? WHERE id = ?', array($quantite, $id));
			}

            
		}
	}

	unset($_SESSION['clientvip']);
    unset($_SESSION['mange']);
    unset($_POST);


    $DB->delete("DELETE FROM validpaie where pseudov='{$pseudo}' ");
	$DB->delete("DELETE FROM validvente where pseudop='{$pseudo}' ");

	//header("Location: recherche.php");

    header("Location: ticket_pdf.php");
}else{

	header("Location: accueil.php");
}?>

</body>
</html>

			

	


		
