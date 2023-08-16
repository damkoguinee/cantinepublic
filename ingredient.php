<?php
require 'header3.php';

if (!empty($_SESSION['pseudo'])) {  

  if ($_SESSION['level']>=3) {

    if (isset($_GET['deleteret'])) {

      $DB->delete("DELETE from prodingredient where idprod='{$_GET['deleteret']}' and iding='{$_GET['iding']}'");?>

      <div class="alert alert-success">Suppression reussi!!</div><?php 
    }

    if (isset($_GET['clientvip'])) {

      $_SESSION['clientvip']=$_GET['clientvip'];

    }

    if (isset($_GET['ingredient'])) {

      $_SESSION['ingredient']=$_GET['ingredient'];

    }?>

    
    
    <div class="container-fluid"><?php

      require 'headerstock.php';

      $products=$DB->query('SELECT id, nom as designation FROM stock order by (nom)');?>

      <div class="row">

        <div class="col-sm-12 col-md-4"><?php 
          if (isset($_GET['ajout']) or isset($_GET['clientvip']) or isset($_GET['ingredient'])) {?>

            <form  method="POST" action="ingredient.php">
              <fieldset><legend>Association Produits et ingrédients</legend>

                <div class="col">

                  <div class="mb-1">
                    <label class="form-label">Selectionnez le produit*</label><?php

                    if (!empty($_SESSION['clientvip'])) {?>

                      <input class="form-control" id="search-user" type="text" placeholder="rechercher un produit" value="<?=$panier->nomProduitIngredient($_SESSION['clientvip'])[0];?>" />

                      <input style="width:35%;" type="hidden" name="nom" value="<?=$_SESSION['clientvip'];?>" /> <?php 

                    }else{?>

                      <input class="form-control" id="search-user" type="text" placeholder="rechercher un produit" /> <?php

                    }?>

                    <div id="result-search" class="bg-info"></div>
                  </div>



                  <div class="mb-1">
                    <label class="form-label">Liste des Ingrédients*</label><?php

                    if (!empty($_SESSION['ingredient'])) {?>

                      <input class="form-control" id="search-user1" type="text" placeholder="rechercher un produit" value="<?=$panier->nomProduitIngredient($_SESSION['ingredient'])[0];?>" />

                      <input style="width:35%;" type="hidden" name="ingredient" value="<?=$_SESSION['ingredient'];?>" /> <?php 

                    }else{?>

                      <input class="form-control" id="search-user1" type="text" placeholder="rechercher un produit" /> <?php

                    }?>

                    <div id="result-search1" class="bg-info"></div>
                  </div>

                  
                  <div class="mb-1">
                    <label class="form-label">Quantité*</label>
                    <input class="form-control" type="text" name="quantity" min="0" required="">
                  </div>
                </div>

                <?php if ($_SESSION['level']>6) {?><input class="btn btn-light" type="reset" value="Annuler" id="form"/><input class="btn btn-primary" type="submit" name="valid" value="Ajouter"  id="form" onclick="return alerteV();" /><?php }?>
              </fieldset>
            </form><?php 
          }?>

        </div>
      </div><?php

      if (isset($_POST['valid'])) {

        if (empty($_POST['quantity']) and empty($_POST['nom']) and empty($_POST['ingredient'])) {
              
        }else{
          $nom=$_POST['nom'];
          $ingredient=$_POST['ingredient'];
          $qtite=$_POST['quantity'];

          $verif = $DB->querys("SELECT id FROM prodingredient where idprod='{$nom}' and iding='{$ingredient}' ");

          if (empty($verif['id'])) {

            $DB->insert('INSERT INTO prodingredient (idprod, iding, qtite) VALUES(?, ?, ?)', array($nom, $ingredient, $qtite));?>

            <div class="alert alert-success">Opération enregistré avec succèe!!</div><?php

            unset($_SESSION['ingredient']);
            unset($_SESSION['clientvip']);

          }else{?>

            <div class="alert alert-warning">Cet ingrédient est déja associé à ce produit</div><?php

          }

        }
      }?>      

      <div class="row" style="overflow: auto"><?php 
        if (!isset($_GET['ajout'])) {?>

          <table class="table table-hover table-bordered table-striped table-responsive text-center">

            <thead>

              <tr>
                <th class="text-center bg-info" colspan="5" height="30">Tableau des Récettes  <a style="margin-right: 30px; font-size:20px; color:red;" href="ingredient.php?ajout">Ajouter une recette</a></th>
              </tr>

              <tr>
                <th>N°</th>
                <th>Nom des Ingédients</th>
                <th>dosage</th>
                <th>Dispo</th>
                <th>Opérations</th>
              </tr>

            </thead>

            <tbody><?php 
              $cumulmontant=0;
              $cumulqtite=0;
              $products= $DB->query("SELECT * FROM stock ");

              foreach ($products as $key=>$product ){

                $liaison= $DB->query("SELECT * FROM prodingredient where idprod='{$product->id}' ");

                if (!empty($liaison)) {?>

                  <tr><th colspan="5"><?=ucwords(strtolower($product->nom));?></th></tr><?php

                  foreach ($liaison as $keyl => $valuel) {

                    $dispo= $DB->querys("SELECT * FROM ingredient where id='{$valuel->iding}'");?>

                    <tr>
                      <td style="text-align:center;"><?=$keyl+1;?></td>

                      <td style="text-align: left"><?=$panier->nomProduitIngredient($valuel->iding)[0];?></td>

                      <td style="text-align:center;"><?=$valuel->qtite;?></td>

                      <td style="text-align:center;"><?=$dispo['qtite'];?></td>

                      <td><?php if ($_SESSION['level']>6) {?><a class="btn btn-danger" onclick="return alerteS();" href="ingredient.php?deleteret=<?=$valuel->idprod;?>&iding=<?=$valuel->iding;?>">Supprimer</a><?php }?></td>
                    </tr><?php
                  }
                }
                 
              }?>

            </tbody>
          </table><?php 
        }?>
      </div>

    </div><?php

  }else{

      echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";
  }

}else{

  header('Location: deconnexion.php');

}?>
</body>
</html><?php

require 'footer.php';?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        $('#search-user').keyup(function(){
            $('#result-search').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'recherchefiltre.php',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#result-search').append(data);
                        }else{
                          document.getElementById('result-search').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                        }
                    }
                })
            }
      
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#search-user1').keyup(function(){
            $('#result-search1').html("");

            var utilisateur = $(this).val();

            if (utilisateur!='') {
                $.ajax({
                    type: 'GET',
                    url: 'recherchefiltre.php?ingredient',
                    data: 'user=' + encodeURIComponent(utilisateur),
                    success: function(data){
                        if(data != ""){
                          $('#result-search1').append(data);
                        }else{
                          document.getElementById('result-search1').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                        }
                    }
                })
            }
      
        });
    });
</script>

<script type="text/javascript">
  function alerteS(){
    return(confirm('Valider la suppression'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>
