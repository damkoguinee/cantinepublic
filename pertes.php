<?php
require 'header3.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];
  

  if ($_SESSION['level']>=3) {

    require 'headerstock.php';?>
    
    <div class="container-fluid"><?php

      $products=$DB->query('SELECT id, nom as designation FROM stock order by (nom)');?>

      <div class="row">

        <div class="col-sm-12 col-md-4">

          <form method="POST" action="pertes.php" >
            <fieldset ><legend>Ajouter une Perte</legend>

              <div class="row">

                <div class="col">
                  <div class="mb-1">
                    <label class="form-label">Nom du Produit*</label>
                    <select class="form-select" name="nom" required="">
                      <option></option><?php
                      foreach ($products as $value) {?>

                        <option value="<?=$value->id;?>"><?=ucfirst($value->designation);?></option><?php 
                      }?>
                    </select>
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Prix d'Achat</label>
                    <input class="form-control" type="number" name="prix_achat" value="0" min="0">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Prix de Vente</label>
                    <input class="form-control" type="number" name="prix_vente" value="0" min="0">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Prix de Revient</label>
                    <input class="form-control" type="number" name="prix_revient" value="0" min="0">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Quantite</label>
                    <input class="form-control" type="number" name="quantity" min="0" required="">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Motif Perte</label>
                    <select class="form-select" name="motif" required="">
                      <option></option>
                      <option value="pertes">Pertes</option>
                      <option value="surplus">Surplus</option>
                    </select>
                  </div>
                </div>
              </div>

              <?php if ($_SESSION['level']>6) {?><input class="btn btn-light" type="reset" value="Annuler" name="valid" id="form"/><input class="btn btn-primary" type="submit" value="Ajouter"  id="form" onclick="return alerteV();"/><?php }?>
            </fieldset>
          </form>

        </div><?php

        if (empty($_POST['quantity']) and empty($_POST['nom'])) {
              
        }else{

          $maximum = $DB->querys('SELECT count(id) AS max_id FROM pertes ');

          $numpertes =$maximum['max_id'] + 1;

          $motif=$_POST['motif'];

          $init='per';
          
          
          $nom=$_POST['nom'];

          $DB->insert('INSERT INTO pertes (idpertes, prix_achat, prix_vente, prix_revient, quantite, motifperte, datepertes) VALUES(?, ?, ?, ?, ?, ?, now())', array($_POST['nom'], $_POST['prix_achat'], $_POST['prix_vente'], $_POST['prix_revient'], $_POST['quantity'], $_POST['motif']));

          if ($_POST['motif']=='pertes') {

            $quantite=-$_POST['quantity'];

            $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($_POST['nom'], 'per'.$numpertes, $motif, -$_POST['quantity']));

          }else{
            $quantite=$_POST['quantity'];

            $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($_POST['nom'], 'surp'.$numpertes, $motif, $_POST['quantity']));
          }

          

          $prodstock = $DB->querys("SELECT quantity as quantite FROM stock where id='{$_POST['nom']}' ");

          $moins=$prodstock['quantite']+$quantite;

          $DB->insert('UPDATE stock SET quantity= ? WHERE id = ?', array($moins, $_POST['nom']));?>

            <div class="alert alert-success">Le produit à bien été retirer dans votre stock</div><?php

        }?>
      <div class="col-sm-12 col-md-8">

        <table class="table table-hover table-bordered table-striped table-responsive text-center">

          <thead>

            <tr>
              <th class="text-center bg-info" colspan="6" height="30">Liste des Pertes</th>
            </tr>

            <tr>
              <th>N°</th>
              <th>Motif</th>
              <th>Nom du Produit</th>
              <th>Qtite Perdue</th>
              <th>P-Revient</th>
              <th>Date</th>
            </tr>

          </thead>

          <tbody><?php 
            $cumulmontant=0;
            $cumulqtite=0;
            $products= $DB->query('SELECT nom as designation, motifperte as motif, pertes.prix_revient as prix_revient, pertes.quantite as quantite, datepertes FROM pertes inner join stock on stock.id=idpertes  WHERE YEAR(datepertes) = :annee order by(idpertes)', array(
              'annee' => date('Y')
            ));

            foreach ($products as $key=> $product ){

              $totrevient=$product->prix_revient*$product->quantite;

              $cumulmontant+=$totrevient;
              $cumulqtite+=$product->quantite; ?>

              <tr>
                <td style="text-align: center;"><?= $key+1; ?></td>

                <td><?=ucwords($product->motif);?></td>

                <td><?= $product->designation; ?></td>

                <td style="text-align: center;"><?= $product->quantite; ?></td> 

                <td style="text-align: right; padding-right: 5px;"><?= number_format($totrevient,0,',',' '); ?></td>

                <td><?= (new dateTime($product->datepertes))->format('d/m/Y'); ?></td>
              </tr><?php 
            }?>

          </tbody>

          <tfoot>
              <tr>
                <th colspan="3">Totaux</th>
                <th style="text-align: center; padding-right: 5px;"><?= $cumulqtite;?></th>
                <th style="text-align: right; padding-right: 5px;"><?= number_format($cumulmontant,0,',',' ');?></th>
              </tr>
          </tfoot>

        </table>
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

<script>
function suivant(enCours, suivant, limite){
  if (enCours.value.length >= limite)
  document.term[suivant].focus();
}

function focus(){
document.getElementById('reccode').focus();
}

function alerteS(){
  return(confirm('Confirmer la suppression?'));
}

function alerteV(){
    return(confirm('Confirmer la validation'));
}

function alerteM(){
  return(confirm('Confirmer la modification'));
}
</script>
