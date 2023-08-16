<?php require 'header3.php';
require 'headerstock.php';?>

<div class="container-fluid"> <?php 

if (isset($_GET['idret'])) {

  $id=$panier->h($_GET['idret']);

  $qtite=1;

  $depart = $DB->querys("SELECT quantity as qtite FROM stock WHERE id=?", array($id));

  $qtited=$depart['qtite']+$qtite;

  $DB->insert("UPDATE stock SET quantity= ? WHERE id = ?", array($qtited, $id));

  $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, 'annuldet', 'retrait detail' , $qtite));

}?>         

  <table class="table table-hover table-bordered table-striped table-responsive text-center">

      <thead>
        <tr> 
          <th class="text-center bg-info" colspan="6" height="30"><?="Boissons Retirées " ; ?></th>
        </tr>

        <tr>
          <th>Désignation</th>
          <th>Action</th>
          <th>Qtité</th>
          <th>Total Achat</th>        
          <th>Total Revient</th>        
          <th>Total Vente</th>
        </tr>

      </thead>

    <tbody>

      <?php
      $tot_achat=0;
      $tot_revient=0;
      $tot_vente=0;
      $quantite=0;
      $genre='retrait detail';

      foreach ($panier->listeProduit() as $product) {

        $prod = $DB->querys("SELECT id, sum(quantitemouv) as quantity FROM stockmouv where idstock='{$product->id}'and libelle='{$genre}' ");

        if (!empty($prod['id'])) {

          $tot_achat+=$product->prix_achat*$product->quantity;
          $tot_revient+=$product->prix_revient*$product->quantity;
          $tot_vente+=$product->prix_vente*$product->quantity;
          $quantite+=$prod['quantity'];
          if (-$prod['quantity']>0) {?>

            <tr>
              <td><?= ucwords(strtolower($product->nom)); ?></td>

              <td style="text-align: center;"><?php if ($_SESSION['level']>0) {?><a class="btn btn-danger" onclick="return alerteR();" href="stockretirer.php?retqtite&idret=<?=$product->id;?>"> Annuler </a><?php }?></td>

              <td style="text-align: center;"><?= -$prod['quantity']; ?><?php if ($_SESSION['level']>7) {?><a class="btn btn-danger" href="stockretirer.php?retqtite&idap=<?=$product->id;?>"> + </a><?php }?><input type="hidden" name="qtite" value="<?= $product->quantity; ?>" style="width:90%;"></td>
              <td><?= number_format($product->prix_achat*$product->quantity,0,',',' ') ; ?> </td>

              <td><?= number_format($product->prix_revient*$product->quantity,0,',',' ') ; ?> </td>            

              <td><?= number_format($product->prix_vente*$product->quantity,0,',',' ') ; ?> </td>
              
            </tr><?php 
          }
        }
      }?>

    </tbody>

  </table>
</div> 

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
                    url: 'rechercheproduit.php?stockboisson',
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

<script type="text/javascript">
    function alerteS(){
      return(confirm('Attention, vous êtes sur le point de supprimer un produit!!! Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteR(){
        return(confirm('Etes vous sûr de vouloir annuler ?'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script> 