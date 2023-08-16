<?php 

$bdd='ventedelete';  

$DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_produit` int(11) DEFAULT NULL,
  `prix_vente` double NOT NULL,
  `prix_revient` double DEFAULT '0',
  `quantity` int(11) NOT NULL,
  `num_cmd` varchar(50) NOT NULL,
  `idtable` varchar(50) NULL,
  `id_client` int(10) DEFAULT NULL,
  `idpersonnel` int(11) NOT NULL,
  `datedelete` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ");

if (isset($_GET['deletemodif'])) {
  $DB->delete('DELETE FROM validpaiemodif');

  $DB->delete('DELETE FROM validventemodif');
}

if (isset($_POST['num_cmd']) or isset($_GET['num_cmd'])) {


  if (isset($_POST['num_cmd'])) {
    
    $numero=$_POST['num_cmd'];
  }

  if (isset($_GET['num_cmd'])) {
    
    $numero=$_GET['num_cmd'];
  }

  //$prodtop=$DB->querys('SELECT id_client, montant, benefice FROM topclient WHERE id_client=?', array($_GET['client']));

  $products=$DB->query('SELECT id_produit, nom as designation, commande.quantity as quantity, stock.type as type, idingredient, qtiteingredient FROM commande inner join stock on stock.id=commande.id_produit WHERE num_cmd= :NUM', array('NUM'=>$numero));

  foreach ($products as $prodcmd) {
    $idproduit=$prodcmd->id_produit;
    $designation=$prodcmd->designation;
    $qtite1=$prodcmd->quantity;

    $id=$idproduit;
    $quantityi=$qtite1;

    $prodrecettemodif= $DB->query("SELECT idprod, iding, qtite as qtitep FROM prodingredient where idprod='{$id}'");

    foreach ($prodrecettemodif as $value) {

      $proddet=$DB->querys('SELECT *FROM stockdetail WHERE nom= ?', array($value->iding));

      $qtitedet=$proddet['quantity'];

      $prodingredientmodif= $DB->querys("SELECT qtite FROM ingredient where id='{$value->iding}'");

      $toting=$quantityi*$value->qtitep;

      $reste=($prodingredientmodif['qtite']+($toting));


      $DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $value->iding));

      $qtiterecette=$value->qtitep;
      $qtiteaj=$qtite1*$qtiterecette+$qtitedet;
      
      $DB->insert('UPDATE stockdetail SET quantity = ? WHERE nom = ?', array($qtiteaj, $value->iding));

      //$DB->delete('DELETE from ingredientmouv WHERE numeromouv=? and idstock=?', array($numero, $value->iding));
    }


    if (!empty($prodcmd->idingredient)) {
      

      $prodingredient= $DB->querys("SELECT qtite FROM ingredient where id='{$prodcmd->idingredient}'");

      $toting=$quantityi*$prodcmd->qtiteingredient;

      $reste=($prodingredient['qtite']+($toting));


      $DB->insert('UPDATE ingredient SET qtite = ? WHERE id = ?', array($reste, $prodcmd->idingredient));

      //$DB->delete('DELETE from ingredientmouv WHERE numeromouv=? and idstock=?', array($numero, $prodcmd->idingredient));
    }
    
  
    $prodstock=$DB->querys('SELECT nom as designation, quantity as quantite, type, genre, taille FROM stock WHERE id= ?', array($idproduit));
    if ($prodstock['genre']=='boissons' and $prodstock['taille']=='bouteille') {

      $quantite=$prodstock['quantite']+$prodcmd->quantity;
      
      $DB->insert('UPDATE stock SET quantity = ? WHERE id = ?', array($quantite, $idproduit));
    }
    
  }

  foreach ($products as $prodcmd) {

    $designation=$prodcmd->id_produit;

    $prodmouv=$DB->query('SELECT idstock, quantitemouv FROM stockmouv WHERE idstock= :DESIG and numeromouv=:numero' , array('DESIG'=>$designation, 'numero'=>$numero));

    foreach ($prodmouv as $prodstock) {                    

      $quantite=$prodstock->quantitemouv+$prodcmd->quantity;
  
      $DB->insert('UPDATE stockmouv SET quantitemouv = ? WHERE idstock = ? and numeromouv=?' , array($quantite, $designation, $numero));
    }
  }


  $prodcmd=$DB->query("SELECT *FROM commande WHERE num_cmd='{$numero}'");

  $prodpaie=$DB->querys("SELECT *FROM payement WHERE num_cmd='{$numero}'");

  $table=$prodpaie['idtable'];

  $totalsup=$prodpaie['Total'];

  $montantsup=$prodpaie['montantpaye'];

  $remise=$prodpaie['remise'];

  foreach ($prodcmd as $valuec) {

    $DB->insert('INSERT INTO ventedelete (id_produit, prix_vente, prix_revient, quantity, num_cmd, id_client, idtable, idpersonnel, datedelete) VALUES(?, ?, ?, ?, ?, ?, ?, ?, now())', array($valuec->id_produit, $valuec->prix_vente, $valuec->prix_revient, $valuec->quantity, $valuec->num_cmd, $valuec->id_client, $table, $_SESSION['idpseudo']));
  }
  
  foreach ($panier->email as $valuem) {

    $destinataire=$valuem;
    $message='bonjour, la vente N°'.$numero.' de '.number_format($totalsup,0,',',' ').' a été supprimée par '.$panier->nomPersonnel($_SESSION["idpseudo"])[1];
    ini_set( 'display_errors', 1);
    error_reporting( E_ALL );
    $from = "codebar@damkoguinee.com";
    $to =$destinataire;
    $subject = "ventes supprimees";
    $message = $message;
    $headers = "From:" . $from;
    mail($to,$subject,$message, $headers);
  }
    

    $DB->delete('DELETE FROM payement WHERE num_cmd = ?', array($numero));

    $DB->delete('DELETE FROM bulletin WHERE numero = ?', array($numero));

    $DB->delete('DELETE FROM commande WHERE num_cmd = ?', array($numero));

    $DB->delete('DELETE FROM historique WHERE num_cmd = ?', array($numero));

    $DB->delete('DELETE FROM versement WHERE numcmd = ?', array($numero));

    $DB->delete('DELETE FROM fraisup WHERE numcmd = ?', array($numero));

    $DB->delete('DELETE FROM banque WHERE numero = ?', array('vente'.$numero));

    $products=$DB->querys('SELECT num_cmd FROM payement WHERE num_cmd= ?', array($numero));

    if (empty($products)) {?>

      <div class="alert alert-success">Commande supprimée avec sucèe!!</div><?php

    }else{?>

    <div class="alert alert-warning">Commande non supprimée!!</div><?php

  }

}else{}?>