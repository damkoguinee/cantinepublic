<?php require 'header3.php';

if (!empty($_SESSION['pseudo'])) {

  if ($_SESSION['level']>=3) {

    if (!isset($_POST['j1'])) {

      $_SESSION['date']=date("Ymd");  
      $dates = $_SESSION['date'];
      $dates = new DateTime( $dates );
      $dates = $dates->format('Ymd'); 
      $_SESSION['date']=$dates;
      $_SESSION['date1']=$dates;
      $_SESSION['date2']=$dates;
      $_SESSION['dates1']=$dates; 

    }else{

      $_SESSION['date01']=$_POST['j1'];
      $_SESSION['date1'] = new DateTime($_SESSION['date01']);
      $_SESSION['date1'] = $_SESSION['date1']->format('Ymd');
      
      $_SESSION['date02']=$_POST['j2'];
      $_SESSION['date2'] = new DateTime($_SESSION['date02']);
      $_SESSION['date2'] = $_SESSION['date2']->format('Ymd');

      $_SESSION['dates1']=(new DateTime($_SESSION['date01']))->format('d/m/Y');
      $_SESSION['dates2']=(new DateTime($_SESSION['date02']))->format('d/m/Y');  
    }


    if (isset($_POST['j2'])) {

      $datenormale='du '.$_SESSION['dates1'].' au '.$_SESSION['dates2'];

    }else{

      $datenormale=(new DateTime($dates))->format('d/m/Y');
    }
    
    require 'headercompta.php';?>

    <div class="container-fluid">

      <div class="row" style="overflow: auto;">

        <table class="table table-hover table-bordered table-striped table-responsive text-center">

          <thead>

            <tr>
              <th scope="col">Personnels</th>
              <th>Début ----------- Fin</th>                   
              <th>Nbre V</th>
              <th>Ventes</th>
              <th>Charges</th>
              <th>Fond de Caisse</th>
              <th>Différence</th>
            </tr>

          </thead>

          <tbody>

            <tr>

              <form method="POST" action="produitvendusdet.php">

                <td>
                  <select  class="form-select" aria-label="Default select example" name="magasin" onchange="this.form.submit()"><?php

                    if (isset($_POST['magasin']) and $_POST['magasin']=='general') {?>

                      <option value="<?=$_POST['magasin'];?>">Général</option><?php
                      
                    }elseif (isset($_POST['magasin'])) {?>

                      <option value="<?=$_POST['magasin'];?>"><?=$panier->nomPersonnel($_POST['magasin'])[1];?></option><?php
                      
                    }else{?>

                      <option value="<?=$_SESSION['idpseudo'];?>"><?=$panier->nomPersonnel($_SESSION['idpseudo'])[1];?></option><?php

                    }

                    if ($_SESSION['level']>=6) {

                      foreach($panier->listePersonnel() as $product){?>

                        <option value="<?=$product->id;?>"><?=strtoupper($product->nom);?></option><?php

                      }?>

                      <option value="general">Général</option><?php
                    }?>
                  </select>
                </td>

                <td>
                  <div class="container">
                    <div class="row">
                      <div class="col"><?php

                        if (isset($_POST['j1'])) {?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                        }else{?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                        }?>
                      </div>

                      <div class="col"><?php 

                        if (isset($_POST['j2'])) {?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j2" value="<?=$_POST['j2'];?>" onchange="this.form.submit()"><?php

                        }else{?>

                          <input class="form-control" style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                        }?>
                      </div>
                    </div>

                  </td>
                </form>

                <td style="text-align: center;"><?=$panier->nbreVente($_SESSION['date1'], $_SESSION['date2']); ?></td>

                <td style="text-align: center;"><?=number_format($panier->venteTot($_SESSION['date1'], $_SESSION['date2']),0,',',' '); ?></td><?php

                if ($_SESSION['level']>=6) {?>

                  <td style="text-align: center;"><?=number_format($panier->depenseTot($_SESSION['date1'], $_SESSION['date2']),0,',',' '); ?></td><?php 
                }else{?>

                  <td></td>
                  <td></td><?php 
                }?>

                <form method="POST" action="comptasemaine.php"><?php

                  if (isset($_POST['fcaisse'])) {

                    $_SESSION['fcaisse']=$_POST['fcaisse'];

                    if (empty($_POST['fcaisse'])) {
                      $difference=$panier->montantCompte(1);
                    }else{
                      $difference=$panier->montantCompte(1)-$_POST['fcaisse'];
                    }

                    $_SESSION['difference']=$difference;?>

                      <td><?php if ($_SESSION['level']>=6) {?><input  class="form-control" type ="text" name ="fcaisse" value="<?=$_POST['fcaisse'];?>" onchange="this.form.submit()" style="width: 95%; height: 25px; font-size: 20px;"><?php }?></td><?php

                    }else{

                      $difference=$panier->montantCompte(1);
                      $_SESSION['difference']=$difference;?>

                      <td><?php if ($_SESSION['level']>=6) {?><input  class="form-control" type = "text" name ="fcaisse" onchange="this.form.submit()" style="width: 95%; height: 25px; font-size: 20px;"><?php }?></td><?php

                    }?>
                  </form>

                  <td style="text-align:center;"><?php if ($_SESSION['level']>=6) {?><?=number_format($difference,0,',',' ');?><?php }?></td>
                </tr>

              </tbody>

            </table>
          </div>

          <div class="container-fluid">

            <div class="row text-center">

              <div class="col" style="overflow: auto;">

                <table class="table table-hover table-bordered table-striped table-responsive">
                  <thead>
                    <tr>
                      <th class="text-center bg-info" colspan="10" height="30"><?="Détails des Produits Vendus " .$datenormale ?></th>
                    </tr>

                    <tr>

                      <th>Désignation</th>
                      <th>Qtité</th>
                      <th>P.Vente</th>
                      <th>P.Revient</th>
                      <th>Bénéfice</th>
                      <th>Payement</th>
                      <th>Heure</th>
                      <th>Etat</th>
                      <th>Contact du Client</th>
                      <th>vendeur</th>
                    </tr>
                  </thead>

                  <tbody><?php  

                    if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

                      $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

                    }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

                      $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

                    }else{

                      $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'"); 
                    }

                    $cumulmontantotc=0;
                    $cumulrevient=0;
                    foreach ($products as $product ){
                      if (!empty($product->clientvip)) {
                          $client=$product->clientvip;
                        }else{
                          $client="client journalier";
                        }

                      $cumulmontantotc+=$product->prix_vente*$product->quantity;
                      $cumulrevient+=$product->prix_revient*$product->quantity; ?>

                      <tr>
                        <td><?= ucwords(strtolower($product->designation)); ?></td>
                        <td style="text-align:center"><?= $product->quantity; ?></td>
                        <td style="text-align: right"  ><?= number_format($product->prix_vente*$product->quantity,0,',',' '); ?></td>
                        <td style="text-align: right"  ><?= number_format($product->prix_revient*$product->quantity,0,',',' '); ?></td>
                        <td style="text-align: right"  ><?= number_format($product->prix_vente*$product->quantity-$product->prix_revient*$product->quantity,0,',',' '); ?></td>
                        <td><?= $product->mode_payement; ?></td>
                        <td><?= $product->DateTemps; ?></td>
                        <td><?= $product->etat; ?></td>
                        <td><?= $client; ?></td>
                        <td><?=strtolower($panier->nomPersonnel($product->vendeur)[0]); ?></td>
                      </tr><?php 

                    }?>

                  </tbody>

                  <tfoot>
                    <tr>
                      <th></th>
                      <th></th>
                      <th style="text-align: right;"><?= number_format($cumulmontantotc,0,',',' ');?></th>
                      <th style="text-align: right;"><?= number_format($cumulrevient,0,',',' ');?></th>
                      <th style="text-align: right;"><?= number_format($cumulmontantotc-$cumulrevient,0,',',' ');?></th>
                    </tr>
                  </tfoot>

                </table>
            </div>
          </div><?php
        }else{

          echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
        }

      }else{

        header("Location: deconnexion.php");


      }?>
  
    </body>
  </html><?php

require 'footer.php';?>



<script type="text/javascript">
  function alerteS(){
    return(confirm('Valider la suppression'));
  }

  function alerteM(){
    return(confirm('Voulez-vous vraiment modifier cette vente?'));
  }

  function alerteC(){
    return(confirm('Voulez-vous vraiment clôturer cette journée?'));
  }

  function alerteSC(){
    return(confirm('Voulez-vous vraiment supprimer la cloture?'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>

