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

              <form method="POST" action="produitvendus.php">

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

            <div class="row text-center"><?php 
              

              $prodcat =$DB->query('SELECT * FROM categorie order by(id)');

              foreach ($prodcat as$valuecat) {

                $total=0;

                if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

                  $products =$DB->querys("SELECT commande.id as id FROM commande inner join stock on stock.id=id_produit inner join payement on payement.num_cmd=commande.num_cmd WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' and idcat= '{$valuecat->id}' ");

                }else{

                  $products =$DB->querys("SELECT commande.id as id FROM commande inner join stock on stock.id=id_produit inner join payement on payement.num_cmd=commande.num_cmd WHERE vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' and idcat= '{$valuecat->id}' ");    
                }  ?>

                <div class="col-sm-12 col-md-4"><?php

                  if (!empty($products['id'])) {?>


                    <table class="table table-hover table-bordered table-striped table-responsive">

                      <thead>

                        <tr>
                          <th class="text-center bg-info" colspan="2"><?=ucwords($valuecat->nomcat)." Vendus " .$datenormale ?></th>
                        </tr>

                        <tr>
                          <th>Désignation</th>
                          <th>Qtité</th>
                        </tr>

                      </thead>

                      <tbody><?php



                        $products =$DB->query("SELECT id, nom as designation FROM stock where idcat='{$valuecat->id}' ");

                        foreach ($products as $produc ){

                          if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

                            $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'"); 

                          }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

                            $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'");

                          }else{

                            $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'");    
                          }    

                          foreach ($products as $product ){

                            $total+= $product->qtite;

                            if (!empty($product->qtite)) {?>

                              <tr>
                                <td style="text-align: left;"><?= ucwords(strtolower($produc->designation)); ?></td>
                                <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
                              </tr><?php

                            }else{

                            }
                          }
                        }?>

                      <tr>          
                        <th colspan="1" height="40">TOTAL</th>
                        <th style="text-align: center;"><?=$total; ?></th>          
                      </tr>

                    </tbody>

                  </table><?php 
                }?>
              </div><?php 
            }?>              
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

