<?php require 'header1.php';
require 'headerstock.php';?>

<div class="box_stockinv" style="margin-top: 30px; width: 99%;"> <?php

if (isset($_POST['desig'])) {
  $_SESSION['desig']=$_POST['desig'];
}else{
  $_SESSION['desig']='';
} 

  $products = $DB->query('SELECT * FROM ingredient ORDER BY (nom)');?>         

  <table class="payement" style="width:80%;">

    <form action="ingredientmouv.php" method="POST">

      <thead>
      

        <tr>

          <th colspan="2"><select style="width:400px; height: 30px;" type="text" name="desig" onchange="this.form.submit()" ><?php 
            if (isset($_POST['desig'])) {?>
              <option><?=$panier->nomIngredient($_SESSION['desig'])?></option><?php
            }else{?>
              <option></option><?php
            }

            foreach($products as $product){?>
                <option value="<?=$product->id;?>"><?=$product->nom;?></option><?php
            }?></select>

            <?="Mouvements des ".$panier->nomIngredient($_SESSION['desig']);?>
          </th>
        </tr>
      

        <tr>
          <th>Date</th>
          <th>Libellé</th>
          <th>Quantité
            <table>
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

        $prodstockmouv = $DB->query("SELECT * FROM ingredientmouv where idstock='{$_SESSION['desig']}' order by(id) ");
      }else{

        $prodstockmouv=array();

      }

      $soldestock=0;
      $keyent=0;
      $keysort=0;
      $keyret=0;
      $keyper=0;

      foreach ($prodstockmouv as $keye=> $entree){

        $soldestock+=$entree->qtiterecette;

        if ($entree->libelle=='entree') {

          $keyent+=1;

          $libelled='Approvisionnement N°'.$keyent;

        }elseif ($entree->libelle=='surplus') {

          $keyent+=1;

          $libelled='Surplus N°'.$keyent;

        }elseif ($entree->libelle=='sortie') {

          $keysort+=1;

          $libelled='Vente N°'.$keysort;        

        }elseif ($entree->libelle=='retour') {

          $keyret+=1;

          $libelled='Retour N°'.$keyret;        

        }elseif ($entree->libelle=='pertes') {

          $keyper+=1;

          $libelled='Perte N°'.$keyper;        

        }?>

        <tr>
          <td><?=(new DateTime($entree->dateop))->format('d/m/Y à H:i');?></td>

          <td><?=ucfirst($libelled); ?></td>

          <td>
            <table>
              <tbody>
                <tr><?php 
                  if ($entree->libelle=='entree') {?>

                    <td style="width: 100px; text-align: center; font-size: 35px;"><?= $entree->qtiterecette; ?></td>
                    <td style="width: 100px; text-align: center; font-size: 35px;"></td><?php

                  }elseif ($entree->libelle=='surplus') {?>

                    <td style="width: 100px; text-align: center; font-size: 35px;"><?= $entree->qtiterecette; ?></td>
                    <td style="width: 100px; text-align: center; font-size: 35px;"></td><?php

                  }elseif ($entree->libelle=='sortie') {?>

                    <td style="width: 100px; text-align: center; font-size: 35px;"></td>                  
                    <td style="width: 100px; text-align:center; font-size: 35px;"><?= (-1)*$entree->qtiterecette; ?></td><?php 
                  }else{?>

                    <td style="width: 100px; text-align: center; font-size: 35px;"></td>

                    <td style="width: 100px; text-align: center; font-size: 35px;"><?= $entree->qtiterecette; ?></td>
                    <?php

                  }?>

                  <td style="width: 100px; text-align: center; font-size: 35px;"><?=$soldestock;?></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr><?php 
      }?>

    </tbody>

    

  </table>

  
</div>   

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