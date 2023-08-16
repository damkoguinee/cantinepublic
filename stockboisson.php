<?php require 'header3.php';
require 'headerstock.php';?>

<div class="container-fluid" style="margin-top: 30px; width: 99%;"> <?php 

  if (isset($_GET['deletes'])) {

    $numero=$_GET['deletes'];
    $DB->delete('DELETE FROM stock WHERE id = ?', array($numero));?>

    <div class="alert alert-success">Le Produit a bien été supprimer</div><?php
  }

  if (isset($_GET['stockboisson'])) {
    unset($_SESSION['design']);
    unset($_SESSION['recherchgen']);
  }

  if (isset($_GET['idret'])) {

    $id=$panier->h($_GET['idret']);

    $qtite=1;

    $depart = $DB->querys("SELECT quantity as qtite FROM stock WHERE id=?", array($id));

    $qtited=$depart['qtite']-$qtite;

    $DB->insert("UPDATE stock SET quantity= ? WHERE id = ?", array($qtited, $id));

    $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, 'retraitdet', 'retrait detail' , -$qtite));

  }

  if (isset($_GET['idap'])) {

      $id=$panier->h($_GET['idap']);

      $qtite=1;

      $depart = $DB->querys("SELECT quantity as qtite FROM stock WHERE id=?", array($id));

      $qtited=$depart['qtite']+$qtite;

      $DB->insert("UPDATE stock SET quantity= ? WHERE id = ?", array($qtited, $id));

      $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, 'approv', 'entree' , $qtite));

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
  $genre='boissons';
  $prods = $DB->query("SELECT * FROM stock where genre='{$genre}'ORDER BY (nom)");

  //$prodcat = $DB->query('SELECT * FROM logo ORDER BY (name)');

    if (isset($_POST['id'])) {
      $qtiteint=$panier->espace($_POST['qtiteint']);
      $pr=$panier->espace($_POST['pr']);
      $pv=$panier->espace($_POST['pv']);


      $DB->insert('UPDATE stock SET nom= ?, qtiteint=?, prix_revient=?, prix_vente= ?, quantity= ? WHERE id = ?', array($_POST['desig'], $qtiteint,  $pr, $pv, $_POST['qtite'], $_POST['id']));
    }?>

    <div class="row" style="overflow: auto;">         

      <table class="table table-hover table-bordered table-striped table-responsive text-center">

      <thead>

        <tr>
          <th class="bg-info" colspan="8" height="30"><?="Stock Boissons à la date du " .date('d/m/Y'); ?>
            <a href="printstock.php?stock" target="_blank" ><img  style=" margin-left: 20px; height: 20px; width: 20px;" src="css/img/pdf.jpg"></a>
            <a style="margin-left: 10px;"href="csv.php?stock" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/excel.jpg"></a>
          </th>
        </tr>

        <tr>

          <form action="stockboisson.php" method="POST">
            <th colspan="4" class="bg-info"><select class="form-select" aria-label="Default select example" type="text" name="design" onchange="this.form.submit()" ><?php 
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

            <th colspan="4" class="bg-info">

              <input class="form-control" id="search-user" type="search" name="recherchgen" placeholder="rechercher un produit" />

                <div style="color:white; background-color: black; font-size: 11px;" id="result-search"></div>
            </th>

          
        </tr>

        <tr>
          <th>Désignation</th>
          <th>Retirer</th>
          <th>Quantité</th>
          <th>Prix de Vente</th>
          <th>Prix de Revient</th>
          <th>Qtite Interne</th>
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

       $prod = $DB->query("SELECT * FROM stock WHERE  nom='{$_SESSION['design']}' and genre='{$genre}' ORDER BY (nom)  ");

      }elseif (!empty($_SESSION['recherchgen'])) {

       $prod = $DB->query("SELECT * FROM stock WHERE  id='{$_SESSION['recherchgen']}' and genre='{$genre}' ORDER BY (nom)  ");
      }else{
        $prod = $DB->query("SELECT * FROM stock where genre='{$genre}' and quantity!=0 ORDER BY (nom) ");
      }

      foreach ($prod as $product):

        $tot_achat+=$product->prix_achat*$product->quantity;
        $tot_revient+=$product->prix_revient*$product->quantity;
        $tot_vente+=$product->prix_vente*$product->quantity;
        $quantite+=$product->quantity;?>

        <tr>

          <form action="stockboisson.php" method="POST">

            <td><input class="form-control" type="text" name="desig" value="<?= ucwords(strtolower($product->nom)); ?>"> <input type="hidden" name="id" value="<?= $product->id; ?>"><input type="hidden" name="qtite" value="<?= $product->quantity; ?>" style="width:90%;"></td>

            <td style="text-align: center;"><?php if ($_SESSION['level']>0) {?><a class="btn btn-danger" onclick="return alerteR();" href="stockboisson.php?retqtite&idret=<?=$product->id;?>"> Retirer </a><?php }?></td>

            <td style="text-align: center;"><?= $product->quantity; ?><?php if ($_SESSION['level']>7) {?><a class="btn btn-danger" href="stockboisson.php?retqtite&idap=<?=$product->id;?>"> + </a><?php }?><input type="hidden" name="qtite" value="<?= $product->quantity; ?>" style="width:90%;"></td>

            <td><input class="form-control text-center" type="text" name="pv" value="<?= number_format($product->prix_vente,0,',',' '); ?>" ></td>

            <td><input class="form-control text-center" type="text" name="pr" value="<?= number_format($product->prix_revient,0,',',' '); ?>"  ></td>

            <td><input class="form-control text-center" type="text" name="qtiteint" value="<?= $product->qtiteint; ?>" ></td>

            <td><input class="btn btn-success" type="submit" name="valid" value="Valider"></td>
            </form>

            <td><?php if ($_SESSION['level']>7) {?><a href="stockgeneral.php?deletes=<?=$product->id;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a><?php }?></td>

          
        </tr>
          
      <?php endforeach ?>

    </tbody>

    <tfoot>

      <tr>
        <th colspan="2">TOTAL</th>
        <th class="text-center"><?= number_format($quantite,0,',',' ') ; ?> </th>

         <th class="text-center"><?= number_format($tot_vente,0,',',' ') ; ?> </th>

         <th class="text-center"><?= number_format($tot_revient,0,',',' ') ; ?> </th>

      </tr>

    </tfoot>

  </table>  

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
        return(confirm('Confirmer le retrait de 1 sur ce produit'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script> 