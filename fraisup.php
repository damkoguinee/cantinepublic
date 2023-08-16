<?php
//Pour les frais supplemenetaire

$_SESSION['fraisup']=array();

if (!empty($fraisup)) {

	if ($_POST['clientvip']=='BoutiqueB3' or $_POST['clientvip']=='BoutiqueB10') {

		
	}else{
		

		$_SESSION['fraisup']=$fraisup;
		if (empty($datev)) {

			$DB->insert('INSERT INTO fraisup (numcmd, montant, payement, motif, client, typeclient, etat, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, now())',array($init.$numero_commande, $fraisup, $_POST['mode_payement'], 'frais supplementaire', $client, "VIP", 'en-cours'));

			$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], -$fraisup, 'fsup'.$init.$numero_commande, 'fsup'.$init.$numero_commande));


		}else{
			$DB->insert('INSERT INTO fraisup (numcmd, montant, payement, motif, client, typeclient, etat, date_payement) VALUES(?, ?, ?, ?, ?, ?, ?, ?)',array($init.$numero_commande, $fraisup, $_POST['mode_payement'], 'frais supplementaire', $client, "VIP", 'en-cours', $datev));

			$DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero,  date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], -$fraisup, 'fsup'.$init.$numero_commande, 'fsup'.$init.$numero_commande,$datev));

		}

		
	}
}