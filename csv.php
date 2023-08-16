<?php
require '_header.php';
header("Content-type: text/csv;");

if (isset($_GET['stock'])) {
	header("Content-disposition: attachment; filename=payement stock.csv");
	

	$newReservations=$DB->query("SELECT id, nom as designation, quantity as quantite, prix_achat, prix_revient, prix_vente from stock order by (nom)");?>
	Ordre;"designation";"quantite";"Prix-Achat";"Prix-Revient";"Prix-Vente";"Tot Achat";"Tot Revient";"Tot-Vente";<?php
	$totachat=0;
	$totrevient=0;
	$totvente=0;
	$quantite=0;
	foreach($newReservations as $key => $row) {

		$totachat+=$row->prix_achat*$row->quantite;
        $totrevient+=$row->prix_revient*$row->quantite;
        $totvente+=$row->prix_vente*$row->quantite;
        $quantite+=$row->quantite;

        echo "\n".'"'.($key+1).'";"'.$row->designation.'";"'.$row->quantite.'";"'.$row->prix_achat.'";"'.$row->prix_revient.'";"'.$row->prix_vente.'";"'.$totachat.'";"'.$totrevient.'";"'.$totvente.'"';

	    
	}
}


if (isset($_GET['client'])) {
	header("Content-disposition: attachment; filename=compteclient.csv");

	$cumulmontant=0;

    $type1='Client';
    $type2='Clientf';

    $nomclient = $DB->query("SELECT *FROM client where type='{$type1}' or type='{$type2}' order by(nom_client)");?>
    Ordre;"Nom du Client";"Solde Compte";<?php

    foreach ($nomclient as $key => $row){

    	$products= $DB->querys("SELECT sum(montant) as montant FROM bulletin where nom_client='{$row->id}' ");

      	$cumulmontant+=$products['montant'];

	    if ($products['montant']>0) {
        	$montant=$products['montant'];
      	}else{
	        $montant=$products['montant'];
	    }

        echo "\n".'"'.($key+1).'";"'.$row->nom_client.'";"'.$montant.'"';

	    
	}
}

if (isset($_GET['personnel'])) {
	header("Content-disposition: attachment; filename=compteclient.csv");

	$cumulmontant=0;

    $type1='Employer';
    $type2='Employer';

    $nomclient = $DB->query("SELECT *FROM client where type='{$type1}' or type='{$type2}' order by(nom_client)");?>
    Ordre;"Nom du Client";"Solde Compte";<?php

    foreach ($nomclient as $key => $row){

    	$products= $DB->querys("SELECT sum(montant) as montant FROM bulletin where nom_client='{$row->id}' ");

      	$cumulmontant+=$products['montant'];

	    if ($products['montant']>0) {
        	$montant=$products['montant'];
      	}else{
	        $montant=$products['montant'];
	    }

        echo "\n".'"'.($key+1).'";"'.$row->nom_client.'";"'.$montant.'"';

	    
	}
}

if (isset($_GET['autres'])) {
	header("Content-disposition: attachment; filename=compteclient.csv");

	$cumulmontant=0;

    $type1='autres';
    $type2='autres';

    $nomclient = $DB->query("SELECT *FROM client where type='{$type1}' or type='{$type2}' order by(nom_client)");?>
    Ordre;"Nom du Client";"Solde Compte";<?php

    foreach ($nomclient as $key => $row){

    	$products= $DB->querys("SELECT sum(montant) as montant FROM bulletin where nom_client='{$row->id}' ");

      	$cumulmontant+=$products['montant'];

	    if ($products['montant']>0) {
        	$montant=$products['montant'];
      	}else{
	        $montant=$products['montant'];
	    }

        echo "\n".'"'.($key+1).'";"'.$row->nom_client.'";"'.$montant.'"';

	    
	}
}


if (isset($_GET['fournisseurs'])) {
	header("Content-disposition: attachment; filename=compteclient.csv");

	$cumulmontant=0;

    $type1='Fournisseur';
    $type2='Fournisseur';

    $nomclient = $DB->query("SELECT *FROM client where type='{$type1}' or type='{$type2}' order by(nom_client)");?>
    Ordre;"Nom du Client";"Solde Compte";<?php

    foreach ($nomclient as $key => $row){

    	$products= $DB->querys("SELECT sum(montant) as montant FROM bulletin where nom_client='{$row->id}' ");

      	$cumulmontant+=$products['montant'];

	    if ($products['montant']>0) {
        	$montant=$products['montant'];
      	}else{
	        $montant=$products['montant'];
	    }

        echo "\n".'"'.($key+1).'";"'.$row->nom_client.'";"'.$montant.'"';

	    
	}
}