<?php require 'header3.php';
require 'headerstock.php';?>

<div class="container-fluid"> <?php

if (isset($_POST['desig'])) {
  $_SESSION['desig']=$_POST['desig'];
}else{
  $_SESSION['desig']='';
} 

  $products = $DB->query('SELECT * FROM stock ORDER BY (nom)');?> 

  <div class="col-sm-12 col-md-10">        

    <table class="table table-hover table-bordered table-striped table-responsive text-center">

      <form action="stockmouv.php" method="POST">

        <thead>
        

          <tr>

            <th><select class="form-select" type="text" name="desig" onchange="this.form.submit()" ><?php 
              if (isset($_POST['desig'])) {?>
                <option><?=$panier->nomProduit($_SESSION['desig'])?></option><?php
              }else{?>
                <option></option><?php
              }

              foreach($products as $product){?>
                  <option value="<?=$product->id;?>"><?=$product->nom;?></option><?php
              }?></select>  <?="Mouvement de Stock des ".$panier->nomProduit($_SESSION['desig']);?></th>
          </tr>
        

          <tr>
            <th>Date</th>
            <th>Libellé</th>
            <th>Quantité
              <table class="table table-hover table-bordered table-striped table-responsive">
                <thead>
                  <tr>
                    <th style="width: 100px;">Entrée(s)</th>
                    <th style="width: 100px;">Sortie(s)</th>
                    <th style="width: 100px;">Stock</th>
                  </tr>
                </thead>
              </table></th>
          </tr>

        </thead>
      </form>

      <tbody>

        <?php
        $tot_achat=0;
        $tot_revient=0;
        $tot_vente=0;
        $quantite=0;

        if (isset($_POST['desig'])) {

          $prodstockmouv = $DB->query("SELECT * FROM stockmouv where idstock='{$_SESSION['desig']}' order by(id) ");
        }else{

          $prodstockmouv=array();

        }

        $soldestock=0;
        $keyent=0;
        $keysort=0;
        $keyret=0;
        $keyper=0;

        foreach ($prodstockmouv as $keye=> $entree){

          $soldestock+=$entree->quantitemouv;

          if ($entree->libelle=='entree') {

            $keyent+=1;

            $libelled='Approvisionnement N°'.$keyent;

          }elseif ($entree->libelle=='surplus') {

            $keyent+=1;

            $libelled='Surplus N°'.$keyent;

          }elseif ($entree->libelle=='sortie') {

            $keysort+=1;

            $libelled='Vente N°'.$keysort;        

          }elseif ($entree->libelle=='sortiedet') {

            $keysort+=1;

            $libelled='Vente detail N°'.$keysort;        

          }elseif ($entree->libelle=='retrait detail') {

            $keysort+=1;

            $libelled='Retrait pour cocktail/verres etc...';        

          }elseif ($entree->libelle=='retour') {

            $keyret+=1;

            $libelled='Retour N°'.$keyret;        

          }elseif ($entree->libelle=='pertes') {

            $keyper+=1;

            $libelled='Perte N°'.$keyper;        

          }?>

          <tr>
            <td><?=(new DateTime($entree->dateop))->format('d/m/Y');?></td>

            <td><?=ucfirst($libelled); ?></td>

            <td>
              <table class="table table-hover table-bordered table-striped table-responsive text-center">
                <tbody>
                  <tr><?php 
                    if ($entree->libelle=='entree') {?>

                      <td style="width: 100px;"><?= $entree->quantitemouv; ?></td>
                      <td style="width: 100px;"></td><?php

                    }elseif ($entree->libelle=='surplus') {?>

                      <td style="width: 100px;"><?= $entree->quantitemouv; ?></td>
                      <td style="width: 100px;"></td><?php

                    }elseif ($entree->libelle=='sortie') {?>

                      <td style="width: 100px;"></td>                  
                      <td style="width: 100px;"><?= (-1)*$entree->quantitemouv; ?></td><?php 
                    }elseif ($entree->libelle=='retrait detail') {?>

                      <td style="width: 100px;"></td>                  
                      <td style="width: 100px;"><?= (-1)*$entree->quantitemouv; ?></td><?php 
                    }else{?>

                      <td style="width: 100px;"></td>

                      <td style="width: 100px;"><?= $entree->quantitemouv; ?></td>
                      <?php

                    }?>

                    <td style="width: 100px;"><?=$soldestock;?></td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr><?php 
        }?>

      </tbody>

      

    </table>
  </div>

  
</div>  

</body>
</html><?php

require 'footer.php';?> 

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