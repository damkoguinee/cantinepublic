<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {

    $pseudo=$_SESSION['pseudo'];

    $products = $DB->querys('SELECT statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

    if ($_SESSION['statut']!="vendeur") {

        require 'headercmd.php';?>

        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-12 col-md-4"><?php 

                    if (isset($_POST['payer'])) {
                        $_SESSION['motif']="Commande Fournisseur";
                        $type="FOURNISSEUR";
                        require 'insertcmd.php';
                    }

                    if (isset($_GET['delcmd'])) {

                      $DB->delete('DELETE FROM achat WHERE id = ?', array($_GET['delcmd']));
                    }

                        
                            
                    if (isset($_POST['etat']) AND $_POST['etat']=="livre" ) {
                        
                    }else{?>

                

                        <fieldset style=" border: 3px solid black;"><h7>RECHERCHEZ LES PRODUITS</h7><?php

                            if (isset($_GET['ventec'])) {
                                unset($_SESSION['scannerc']); // Pour pouvoir à la vente normale
                            }?>
                            <form method="GET" action="commande.php" id="suite" name="term">

                                <input  class="form-control" name = "terme" placeholder="rechercher un produit" onKeyUp="suivant(this,'s', 9)" onchange="document.getElementById('suite').submit()">
                                <input name = "s" style="width: 0px; height: 0px;" >
                            </form><?php

                            if (isset($_GET['terme'])) {

                                if (isset($_GET["terme"])){

                                    $_GET["terme"] = htmlspecialchars($_GET["terme"]); //pour sécuriser le formulaire contre les failles html
                                    $terme = $_GET['terme'];
                                    $terme = trim($terme); //pour supprimer les espaces dans la requête de l'internaute
                                    $terme = strip_tags($terme); //pour supprimer les balises html dans la requête
                                }

                                if (isset($terme)){

                                    $terme = strtolower($terme);
                                    $products=$DB->query("SELECT * FROM stock WHERE nom LIKE ? and genre LIKE ?", array("%".$terme."%", "boissons"));

                                }else{

                                 $message = "Vous devez entrer votre requete dans la barre de recherche";

                                }?>

                                <div class="container-fluid">

                                    <div class="row"><?php 

                                        foreach ( $products as $product ){?>

                                            <div class="col m-0 mt-1 ">
                                                <a style="text-decoration: none;  " href="commande.php?desig=<?= $product->nom; ?>&idc=<?=$product->id;?>&pa=<?=$product->prix_achat;?>&pv=<?=$product->prix_vente;?>">

                                                    <div class="card bg-light" style="width: 9rem;">
                                                        <div class="card-bod m-auto">
                                                            <h6 class="card-title"><?= $product->nom; ?></h6>
                                                        </div>

                                                      <img src="img/<?= $product->id ; ?>.jpg" class="card-img-top m-auto" alt="" style="width: 3rem; height: 3rem">

                                                      <div class="card-bod m-auto">
                                                        <h6 class="card-title">Stock: <?= $product->quantity; ?></h6>
                                                      </div>

                                                      <div class="card-bod m-auto">
                                                        <h6 class="card-title"><?= number_format($product->prix_vente,0,',',' '); ?></h6>
                                                      </div>

                                                    </div>
                                                </a>
                                            </div><?php
                                        }?>
                                    </div>
                                </div><?php
                       
                            }?>

                        </fieldset><?php
                
                    }?>
                </div><?php

                require 'panierc.php';?>
            </div>
        </div><?php

    }else{

        echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
    }

}else{

    header('Location: deconnexion.php');


}

require 'footer.php';?>

<script>
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }
    
    function suivant(enCours, suivant, limite){
        if (enCours.value.length >= limite)
        document.term[suivant].focus();
    }

    function focus(){
    document.getElementById('reccode').focus();
  }
</script>



