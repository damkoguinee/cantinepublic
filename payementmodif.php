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


$numero_commande=$_SESSION['numcmdmodif'];

$numticket=$_SESSION['numticketmodif'];

$_SESSION['numcmd']=$numero_commande;

$init='';
//$init='elm';
//$init='dao';
//$init='kmco';

$_SESSION['num_cmdp']=$numero_commande;

$pseudo=$_SESSION['idpseudo'];
$coment=$_POST['coment'];
$prodvente = $DB->querys("SELECT *FROM validventemodif where pseudop='{$pseudo}' ");

if (empty($prodvente['fraisup'])) {
	$fraisup=0;
}else{
	$fraisup=$prodvente['fraisup'];
}

$total=$panier->totalcommodif()+$fraisup;

$totalcomverif=number_format($panier->totalcommodif(),0,',','');
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
    
    if (empty($_POST['datev'])) {
    	
    	$datev=$_SESSION['datev'].' '.$heure;
    	
    }else{

    	$datev=$_POST['datev'].' '.$heure;
    }

	if ($panier->espace($_POST['reste'])<=0) {

		//************************ GESTION TABLE PAYEMENT PAYEMENT TOTALITE***************

		$prodvente = $DB->querys("SELECT *FROM validventemodif where pseudop='{$pseudo}' ");
		$etat="totalite";
		$montantpaye=$prodvente['montantpgnf']+$_POST['reste'];
		$remise=$prodvente['remise'];
		$reste=0;

		$DB->delete('DELETE from payement WHERE num_cmd=?', array($numero_commande));
		$DB->delete('DELETE from banque WHERE numero=?', array('vente'.$numero_commande));
		$DB->delete('DELETE from banque WHERE numero=?', array('fsup'.$numero_commande));
		$DB->delete('DELETE from fraisup WHERE numcmd=?', array($numero_commande));
		$DB->delete('DELETE from bulletin WHERE numero=?', array($numero_commande));

		if (!empty($_POST['clientvip'])) {

			if (empty($datev)) {

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment));
			}else{

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment, $datev));
			}

			if (empty($datev)) {
				$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$numero_commande));

			}else{

				$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$numero_commande, $datev));

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

		$DB->delete('DELETE from payement WHERE num_cmd=?', array($numero_commande));
		$DB->delete('DELETE from banque WHERE numero=?', array('vente'.$numero_commande));
		$DB->delete('DELETE from banque WHERE numero=?', array('fsup'.$numero_commande));
		$DB->delete('DELETE from fraisup WHERE numcmd=?', array($numero_commande));
		$DB->delete('DELETE from bulletin WHERE numero=?', array($numero_commande));

		$prodvente = $DB->querys("SELECT *FROM validventemodif where pseudop='{$pseudo}' ");	

		if (empty($prodvente['montantpgnf'])) {
			$montantpaye=0;
		}else{	
			$montantpaye=$prodvente['montantpgnf'];
			
		}

		$remise=$prodvente['remise'];
		$reste=$_POST['reste']+$fraisup;
		$etat="credit";	

		if (empty($client)) {

			header("Location: modifventeprod.php");

		}else{

			if (empty($datev)) {

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment));
			}else{

				$DB->insert('INSERT INTO payement (num_cmd, num_ticket, Total, fraisup, montantpaye, remise, reste, etat, mode_payement, vendeur, position, etatliv, num_client, coment, date_cmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($numero_commande, $numticket, $total, $fraisup, $montantpaye, $remise, $reste, $etat, $_POST['mode_payement'], $pseudo, $position, $etatliv, $numclient, $coment, $datev));
			}

			if (empty($datev)) {

		    	$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], $montantpaye, 'vente n°'.$init.$numero_commande, 'vente'.$numero_commande));
			}else{

		    	$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], $montantpaye, 'vente n°'.$numero_commande, 'vente'.$init.$numero_commande, $datev));

			}

        	if (empty($datev)) {
        		$DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($numclient, -$reste, "Reste a payer facture", $numero_commande));
        	}else{

        		$DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($numclient, -$reste, "Reste a payer facture", $numero_commande, $datev));

        	}
				

				

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

	}

	//******************** GESTION TABLE COMMANDE PAYEMENT TOTALITE*********************

	//*********************************************************Remettre les quantités avant modifcation*****************************



	$prodcmd= $DB->query("SELECT id_produit as id, stock.nom as designation, stock.quantity as qtites, commande.quantity as qtite, stock.type as type, idingredient, qtiteingredient FROM commande inner join stock on stock.id=id_produit where num_cmd='{$numero_commande}'");

	foreach($prodcmd as $product){
		$id=$product->id;
		$quantityi=$product->qtite;
				
		if (empty($_POST['clientvip'])) {

			header("Location: modifventeprod.php");
			
        
		}else{

			$prodrecettemodif= $DB->query("SELECT idprod, iding, qtite as qtitep FROM prodingredient where idprod='{$id}'");

			foreach ($prodrecettemodif as $value) {

				$prodingredientmodif= $DB->querys("SELECT qtite FROM ingredient where id='{$value->iding}'");

				$toting=$quantityi*$value->qtitep;

				$reste=($prodingredientmodif['qtite']+($toting));


				$DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $value->iding));

				$DB->delete('DELETE from ingredientmouv WHERE numeromouv=? and idstock=?', array($numero_commande, $value->iding));
			}


			if (!empty($product->idingredient)) {
				

				$prodingredient= $DB->querys("SELECT qtite FROM ingredient where id='{$product->idingredient}'");

				$toting=$quantityi*$product->qtiteingredient;

				$reste=($prodingredient['qtite']+($toting));


				$DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $product->idingredient));

				$DB->delete('DELETE from ingredientmouv WHERE numeromouv=? and idstock=?', array($numero_commande, $product->idingredient));
			}


			$DB->delete('DELETE from commande WHERE num_cmd=?', array($numero_commande));

			$DB->delete('DELETE from stockmouv WHERE numeromouv=? and idstock=?', array($numero_commande, $id));

			//************************************gestion detail**********************

			$prodstock= $DB->querys("SELECT nom, quantity as qtites, type FROM stock where id='{$id}'");

			if ($prodstock['genre']=='boissons' and $prodstock['genre']=='aromes' and $prodstock['taille']=='bouteille') {

				$qtites=$prodstock['qtites'];
				$quantite=($qtites)+$quantityi;
				$DB->insert('UPDATE stock SET quantity = ? WHERE id = ?', array($quantite, $id));
			}

            
		}
	}


	//******************************************fin***************************************************************************

	$products= $DB->query("SELECT id_produit as id, stock.nom as designation, stock.quantity as qtites, validpaiemodif.quantite as qtite, pvente, prix_achat, prix_revient, prix_vente, stock.type as type, idingredient, qtiteingredient FROM validpaiemodif inner join stock on stock.id=id_produit where pseudov='{$_SESSION['idpseudo']}' order by(validpaiemodif.id)");



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

			header("Location: modifventeprod.php");
			
        
		}else{

			$prodrecette= $DB->query("SELECT idprod, iding, qtite as qtitep FROM prodingredient where idprod='{$id}'");

			foreach ($prodrecette as $value) {

				$prodingredient= $DB->querys("SELECT qtite FROM ingredient where id='{$value->iding}'");

				$toting=$quantity*$value->qtitep;

				$reste=($prodingredient['qtite']-($toting));


				$DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $value->iding));

				if (empty($_POST['datev'])) {
					$DB->insert('INSERT INTO ingredientmouv (idstock, numeromouv, libelle, quantitemouv, qtiterecette, dateop) VALUES(?, ?, ?, ?, ?, now())', array($value->iding, $numero_commande, 'sortie', -$quantity , -$toting));
				}else{

					$DB->insert('INSERT INTO ingredientmouv (idstock, numeromouv, libelle, quantitemouv, qtiterecette, dateop) VALUES(?, ?, ?, ?, ?, ?)', array($value->iding, $numero_commande, 'sortie', -$quantity, -$toting, $datev));
				}
			}


			if (!empty($product->idingredient)) {
				

				$prodingredient= $DB->querys("SELECT qtite FROM ingredient where id='{$product->idingredient}'");

				$toting=$quantity*$product->qtiteingredient;

				$reste=($prodingredient['qtite']-($toting));


				$DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $product->idingredient));

				if (empty($_POST['datev'])) {
					$DB->insert('INSERT INTO ingredientmouv (idstock, numeromouv, libelle, quantitemouv, qtiterecette, dateop) VALUES(?, ?, ?, ?, ?, now())', array($product->idingredient, $numero_commande, 'sortie', -$quantity , -$toting));
				}else{

					$DB->insert('INSERT INTO ingredientmouv (idstock, numeromouv, libelle, quantitemouv, qtiterecette, dateop) VALUES(?, ?, ?, ?, ?, ?)', array($product->idingredient, $numero_commande, 'sortie', -$quantity, -$toting, $datev));
				}
			}


			$DB->insert('INSERT INTO commande (id_produit, prix_vente, prix_achat, prix_revient, quantity, num_cmd, num_ticket, id_client) VALUES(?, ?, ?, ?, ?, ?, ?, ?)', array($id, $price_vente, $price_achat, $price_revient, $quantity, $numero_commande, $numticket, $numclient));

			if (empty($_POST['datev'])) {
				$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, $numero_commande, 'sortie', -$quantity));
			}else{

				$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, ?)', array($id, $init.$numero_commande, 'sortie', -$quantity, $datev));
			}

			//************************************gestion detail**********************

			$prodstock= $DB->querys("SELECT nom, quantity as qtites, type, genre, taille FROM stock where id='{$id}'");

			if ($prodstock['genre']=='boissons' and $prodstock['taille']=='bouteille') {

				$qtites=$prodstock['qtites'];

				$quantite=($qtites)-$quantity;
				$DB->insert('UPDATE stock SET quantity = ? WHERE id = ?', array($quantite, $id));
			}

            
		}
	}

	unset($_SESSION['clientvip']);
    unset($_SESSION['mange']);
    unset($_SESSION['numcmdmodif']);
    unset($_SESSION['numticketmodif']);
    unset($_POST);


    $DB->delete("DELETE FROM validpaiemodif where pseudov='{$pseudo}' ");
	$DB->delete("DELETE FROM validventemodif where pseudop='{$pseudo}' ");
	$idc=0;
	//header("Location: recherche.php?modif");

    header("Location: ticket_pdf.php?numcmd=".$numero_commande);
}else{

	header("Location: accueil.php");
}?>

</body>
</html>

			

	


		
