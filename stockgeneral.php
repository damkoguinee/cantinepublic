<?php require 'header3.php';

require 'headerstock.php';?>

<div class="container-fluid"> <?php 

  if (isset($_GET['deletes'])) {

    $numero=$_GET['deletes'];
    $DB->delete('DELETE FROM stock WHERE id = ?', array($numero));?>

    <div class="alert alert-success">Le Produit a bien été supprimer</div><?php
  }

  if (isset($_POST['design'])) {
    $_SESSION['design']=$_POST['design'];
    unset($_SESSION['recherchgen']);
  }else{
    if (!empty($_SESSION['design'])) {
      $_SESSION['design']=$_SESSION['design'];
    }else{

      $_SESSION['design']='';

    }
    
  }

  if (isset($_GET['recherchgen'])) {
    $_SESSION['recherchgen']=$_GET['recherchgen'];

    unset($_SESSION['design']);
  }else{
    if (!empty($_SESSION['recherchgen'])) {
      $_SESSION['recherchgen']=$_SESSION['recherchgen'];
    }else{

      $_SESSION['recherchgen']='';

    }
    
  }

  if (isset($_GET['stock'])) {
    unset($_SESSION['design']);
    unset($_SESSION['recherchgen']);
  }

  $prods = $DB->query('SELECT * FROM stock ORDER BY (nom)');

  //$prodcat = $DB->query('SELECT * FROM logo ORDER BY (name)');

    if (isset($_POST['id'])) {
      $pa=$panier->espace($_POST['pa']);
      $pr=$panier->espace($_POST['pr']);
      $pv=$panier->espace($_POST['pv']);


      $DB->insert('UPDATE stock SET nom= ?, prix_achat=?, prix_revient=?, prix_vente= ?, quantity= ? WHERE id = ?', array($_POST['desig'], $pa,  $pr, $pv, $_POST['qtite'], $_POST['id']));
    }?>

    <div class="row" style="overflow: auto;">         

      <table class="table table-hover table-bordered table-striped table-responsive text-center">

        <thead>

          <tr><th class="legende" colspan="6" height="30">Liste des Produits</th></tr>

          <tr>

            <form action="stockgeneral.php" method="POST">
              <th colspan="2"><select class="form-select" aria-label="Default select example" type="text" name="design" onchange="this.form.submit()" ><?php 
                if (!empty($_SESSION['design'])) {?>
                  <option value="<?=$_SESSION['design'];?>"><?=$_SESSION['design'];?></option><?php
                }else{?>
                  <option></option><?php
                }

                foreach($prods as $product){?>
                    <option value="<?=$product->nom;?>"><?=$product->nom;?></option><?php
                }?></select>
              </th>
            </form>

            <th colspan="2">

              <input class="form-control" id="search-user" type="text" name="recherchgen" placeholder="rechercher un produit" />

                <div style="color:white; background-color: black; font-size: 11px;" id="result-search"></div>
            </th>          
          </tr>

          <tr>
            <th>Désignation</th>
            <th>Prix d'Achat</th>
            <th>Prix de Revient</th>
            <th>Prix de Vente</th>
            <th></th>
            <th></th>
          </tr>

        </thead>

        <tbody>

          <?php
          $tot_achat=0;
          $tot_revient=0;
          $tot_vente=0;
          $quantite=0;
          $genre='boissons';
          if (!empty($_SESSION['design'])) {

           $prod = $DB->query("SELECT * FROM stock WHERE  nom='{$_SESSION['design']}' and genre!='{$genre}' ORDER BY (nom)  ");

          }elseif (!empty($_SESSION['recherchgen'])) {

           $prod = $DB->query("SELECT * FROM stock WHERE  id='{$_SESSION['recherchgen']}' and genre!='{$genre}' ORDER BY (nom) ");
          }else{
            $prod = $DB->query("SELECT * FROM stock where genre!='{$genre}' ORDER BY (nom) ");
          }

          foreach ($prod as $product):

            $tot_achat+=$product->prix_achat*$product->quantity;
            $tot_revient+=$product->prix_revient*$product->quantity;
            $tot_vente+=$product->prix_vente*$product->quantity;
            $quantite+=$product->quantity;?>

          <tr>

            <form action="stockgeneral.php" method="POST">

              <td style="font-size: 14px; width: 25%; text-align: left;"><input class="form-control" type="text" name="desig" value="<?= ucwords(strtolower($product->nom)); ?>"> <input type="hidden" name="id" value="<?= $product->id; ?>"><input type="hidden" name="qtite" value="<?= $product->quantity; ?>" style="width:90%;"><input type="hidden" name="qtite" value="<?= $product->quantity; ?>" style="width:90%;"></td>

              

              <td><input class="form-control text-center" type="text" name="pa" value="<?= number_format($product->prix_achat,0,',',' '); ?>" ></td>

              <td><input class="form-control text-center" type="text" name="pr" value="<?= number_format($product->prix_revient,0,',',' '); ?>"  ></td>

              <td><input class="form-control text-center" type="text" name="pv" value="<?= number_format($product->prix_vente,0,',',' '); ?>" ></td>

              <td><input class="btn btn-success" type="submit" name="valid" value="Valider"></td>

              </form>

              <td><?php if ($_SESSION['level']>7) {?><a class="btn btn-danger" onclick="return alerteS();" href="stockgeneral.php?deletes=<?=$product->id;?>">Suprrimer</a><?php }?></td>

            
          </tr>
            
        <?php endforeach ?>

      </tbody>

    </table>
  </div>
</div>
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
                    url: 'rechercheproduit.php?stockgeneral',
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

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script> 