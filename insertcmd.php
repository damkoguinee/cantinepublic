<?php 

$date ='0000';

$prodnum = $DB->querys('SELECT max(id) AS max_id FROM achat ');

$numero_commande=$date + $prodnum['max_id'] + 1; //automatique

$numfact=$_POST['numfact'];//manuel

if (isset($_POST['numfact']) And $_POST['numfact']=='') {
   $numfact='sans num';
}

$etat='livre';
$pseudo=$_SESSION['pseudo'];


 $prod= $DB->query('SELECT id_produit, validcomande.designation as designation, stock.quantity as qtites, validcomande.quantite as qtite, pachat, pvente, previent, frais, prix_revient FROM validcomande inner join stock on stock.id=id_produit order by(validcomande.id)');

 $prodverif= $DB->querys('SELECT id FROM facture where numfact=:num',array('num'=>$numfact));


    
if (isset($_POST['montantc'])){

    if ($_POST['montantc']<0){?>

        <div class="alertes">FORMAT INCORRECT</div><?php
        

    }else{                          

        if ($_SESSION['motif']!="" AND $_POST['client']!="") {
            

            if ($_POST['montantc'] < $_POST['prix_reel']){

                $etat="credit";

            }else{

              $etat="clos";

            }  

            if (isset($_POST['prix_reel']) AND $_POST['prix_reel']!="") {

                if (($_POST['prix_reel'])<$_POST['montantc']) {?>

                    <div class="alertes">Echec montant decaissé est sup au prix réel</div><?php  

                }elseif(!empty($prodverif)){?>

                    <div class="alertes">Cette facture a déjà été saisie</div><?php  

                }elseif ($_POST['montantc']>$panier->montantCompte($_POST['compte'])) {?>

                    <div class="alertes">Echec montant decaissé est > au montant disponible</div><?php

                }else{
                    $datef=$_POST['datefact'];                           
                    $fournisseur=$_POST['client'];
                    $etatc=$etat;

                    $_SESSION['taux']=1;

                    foreach($prod as $product){

                        $designation=$product->designation;
                        $qtitestock=$product->qtites;
                        $quantite= $product->qtite;
                        $price_achat=$product->pachat*$_SESSION['taux'];
                        $price_vente=$product->pvente;
                        $price_revient=($product->pachat*$_SESSION['taux'])+$product->frais;
                        $previentstock=$product->prix_revient;
                        $id_produitfac=$product->id_produit;
                        

                        $DB->insert('INSERT INTO achat (id_produitfac, numcmd, numfact, fournisseur, designation, quantite, pachat, previent, pvente, etat, datefact, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array($id_produitfac, 'cmd'.$numero_commande, $numfact, $fournisseur, $designation, $quantite, $price_achat, $price_revient, $price_vente, $etatc, $datef));

                        $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id_produitfac, 'cmd'.$numero_commande, 'entree', $quantite));

                        $qtitetot=($qtitestock)+$quantite;

                        if ($qtitestock<0) {

                            $qtitestock=0;
                        }

                        if (empty($previentstock)) {

                            $qtitestock=0;
                        }

                        $previenmoyen=(($price_revient*$quantite+$previentstock*$qtitestock)/($quantite+$qtitestock));

                        $DB->insert('UPDATE stock SET quantity = ? , prix_achat= ?, prix_revient=?, prix_vente= ?  WHERE id = ?', array($qtitetot, $price_achat, $previenmoyen, $price_vente, $id_produitfac));
                    }

                    $DB->insert('INSERT INTO facture (numcmd, numfact, datefact, fournisseur, montantht, montantva, montantpaye, frais, payement, datecmd) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, now())', array('cmd'.$numero_commande, $numfact, $datef, $fournisseur, $_POST['prix_reel'], 0, $_POST['montantc'], $_POST['frais'], $_POST['mode_payement']));

                    $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], -$_POST['montantc'], "paiement (".'cmd'.$numero_commande.')', 'cmd'.$numero_commande));

                    $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array(1, -$_POST['frais'], "paiement frais (".'cmd'.$numero_commande.')', 'cmd'.$numero_commande));

                    $differnce=(($_POST['prix_reel'])-$_POST['montantc']);

                    $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['client'], $differnce, $_SESSION['motif'], 'cmd'.$numero_commande));

                    $DB->delete('DELETE FROM validcomande'); //pour supprimer les produits validés 

                    unset($_SESSION['panierc']);
                    unset($_SESSION['panierca']);
                    unset($_SESSION['paniercp']);
                    unset($_SESSION['etat']);

                    unset($_SESSION['taux']);
                    unset($_SESSION['devise'])?>

                    <div class="alerteV">Commande validée et stock mis à jour avec succèe!!</div><?php
                }

            }else{?>

              <div class="alertes">Saisissez tous les champs vides</div><?php 

            } 

        } else{?>

          <div class="alertes">Saisissez tous les champs vides</div><?php

        }

    }

}else{

}        

    