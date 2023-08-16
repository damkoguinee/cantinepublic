<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];  

  if (isset($_GET['supclot'])) {
    $datev=(new dateTime($_SESSION['datev']))->format("Y-m-d");
    $DB->delete('DELETE FROM banque WHERE DATE_FORMAT(date_versement, \'%Y-%m-%d\')=? and  libelles=?', array($datev,'cloture'));

    $DB->insert('UPDATE debutjournee SET etat = ? WHERE datev = ?', array(1, $datev));
  }


  if ($_SESSION['level']>=3) {
    
    require 'delete.php';

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

      $datenormale='entre le '.$_SESSION['dates1'].' et le '.$_SESSION['dates2'];

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

              <form method="POST" action="comptasemaine.php">

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

                      <td><?php if ($_SESSION['level']>1) {?><input  class="form-control" type ="text" name ="fcaisse" value="<?=$_POST['fcaisse'];?>" onchange="this.form.submit()" style="width: 95%; height: 25px; font-size: 20px;"><?php }?></td><?php

                    }else{

                      $difference=$panier->montantCompte(1);
                      $_SESSION['difference']=$difference;?>

                      <td><?php if ($_SESSION['level']>1) {?><input  class="form-control" type = "text" name ="fcaisse" onchange="this.form.submit()" style="width: 95%; height: 25px; font-size: 20px;"><?php }?></td><?php

                    }?>
                  </form>

                  <td style="text-align:center;"><?php if ($_SESSION['level']>=6) {?><?=number_format($difference,0,',',' ');?><?php }?></td>
                </tr>

              </tbody>

            </table>
          </div><?php

        if (isset($_GET['detailv'])) {?>

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

        </table><?php

      }elseif(isset($_GET['det'])){?>

      <div id="bilaninv" style="display: flex; flex-wrap: wrap;">

        <div>

          

    </div><?php 

    if ($_SESSION['level']>=6) {?>

      <div style="margin-right: 30px"><?php 

        $totaldepenses=0;
        $products=$DB->query('SELECT nom_client as clientvip, client, montant, DATE_FORMAT(date_payement, \'%d/%m/%Y \')AS DateTemps FROM fraisup left join client on fraisup.client=client.id WHERE DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2 ORDER BY(fraisup.id)DESC', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

        if (!empty($products)) {?>

          <table class="payement">

            <thead>

              <tr>
                <th class="legende" colspan="4" height="30"><?="Liste des frais supplementaire " .$datenormale ?></th>
              </tr>

              <tr>
                <th>Nom</th>
                <th>Motif</th>
                <th>Montant</th>
                <th>Date</th>
              </tr>

            </thead>

            <tbody><?php 
              

              foreach ($products as $product ){
                if (!empty($product->clientvip)) {
                  $client=$product->clientvip;
                }else{
                  $client=$product->client;
                }
                $totaldepenses+=$product->montant;?>
                <td><?= ucwords($client); ?></td> 
                <td><?= ucwords('Frais Supplementaire achat'); ?></td>
                <td style="text-align: right; padding-right: 15px"><?= number_format($product->montant,0,',',' '); ?></td>
                <td><?= $product->DateTemps; ?></td>         
                  
                </tr><?php 
              }?>


            </tbody>

            <tfoot>

              <tr>
                <th colspan="2">TOTAL</th>
                <th style="text-align: right;padding-right: 15px"><?= number_format($totaldepenses,0,',',' ') ; ?></th>
              </tr>

            </tfoot>

          </table><?php 
        }?>

      </div>

      <div style="margin-right: 30px"><?php 

        $totaldepenses=0;
        $products=$DB->query('SELECT nom_client as client, frais, DATE_FORMAT(datecmd, \'%d/%m/%Y \')AS DateTemps FROM facture inner join client on fournisseur=client.id WHERE DATE_FORMAT(datecmd, \'%Y%m%d\') >= :date1 and DATE_FORMAT(datecmd, \'%Y%m%d\') <= :date2 ORDER BY(facture.id)DESC', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

        if (!empty($products)) {?>

          <table class="payement">

            <thead>

              <tr>
                <th class="legende" colspan="4" height="30"><?="Liste des frais marchandises " .$datenormale ?></th>
              </tr>

              <tr>
                <th>Fournisseur</th>
                <th>Motif</th>
                <th>Montant</th>
                <th>Date</th>
              </tr>

            </thead>

            <tbody><?php 
              

              foreach ($products as $product ){
                $totaldepenses+=$product->frais;?>
                <td><?= ucwords($product->client); ?></td>                   
                                         
                  <td><?= ucfirst('Frais Marchandises'); ?></td>
                  <td style="text-align: right; padding-right: 15px"><?= number_format($product->frais,0,',',' '); ?></td>
                  <td><?= $product->DateTemps; ?></td>          
                  
                </tr><?php 
              }?>


            </tbody>

            <tfoot>

              <tr>
                <th colspan="2">TOTAL</th>
                <th style="text-align: right;padding-right: 15px"><?= number_format($totaldepenses,0,',',' ') ; ?></th>
              </tr>

            </tfoot>

          </table><?php 
        }?>

      </div>

        <div style="margin-right: 30px"><?php 

          $totaldepenses=0;
          $products=$DB->query('SELECT montant, coment, DATE_FORMAT(date_payement, \'%d/%m/%Y \')AS DateTemps FROM decdepense WHERE DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2 ORDER BY(id)DESC', array(
                'date1' => $_SESSION['date1'],
                'date2' => $_SESSION['date2']
              ));

              if (!empty($products)) {?>

                <table class="payement">

                <thead>

                  <tr>
                    <th class="legende" colspan="3" height="30"><?="Liste des depenses " .$datenormale?></th>
                  </tr>

                  <tr>                      
                    <th>Date</th>
                    <th>Motif</th>
                    <th>Montant</th>
                  </tr>

                </thead>

                <tbody><?php 
                  

                foreach ($products as $product ){?>
                  <tr> 

                    <td><?= $product->DateTemps; ?></td>                       
                    <td><?= strtolower($product->coment); ?></td>
                    <td style="text-align: right; padding-right: 15px"><?= number_format($product->montant,0,',',' '); ?></td>          
                    
                  </tr><?php 
                } ?>


              </tbody>

              <tfoot>

                <tr>
                  <th colspan="2">TOTAL</th>
                  <th style="text-align: right;padding-right: 15px"><?= number_format($panier->depenseTot($_SESSION['date1'], $_SESSION['date2']),0,',',' ') ; ?></th>
                </tr>

              </tfoot>

            </table><?php

          }?>
        
      </div>


      
    </div><?php 
  }?>


  <div class="bilan_dec">

    <div class="dec"><?php

      if ($_SESSION['level']>=6) {

        $products =$DB->query('SELECT decaissement.id as id, montant, nom_client as client, coment, DATE_FORMAT(date_payement, \'%H:%i:%s\')AS DateTemps FROM decaissement inner join client on client=client.id  WHERE DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

        if (!empty($products)) {?>
         
          <table  class="payement">

            <thead>

              <tr>
                <th class="legende" colspan="5" height="30"><?="Liste des Décaissements du " .$datenormale ?></th>
              </tr>

              <tr>
                <th>N°</th>
                <th>Montant</th>
                <th>Motif</th>
                <th>Nom</th>
                <th>Heure</th>
              </tr>

            </thead>

            <tbody><?php
              $cumulmontant=0;
              foreach ($products as $product ): 

                $cumulmontant+=$product->montant;?>

                <tr>
                  <td style="text-align: center;"><?= $product->id; ?></td>
                  <td style="text-align: right; padding-right: 20px;"><?= number_format($product->montant,0,',',' '); ?></td>
                  <td><?= ucwords($product->coment); ?></td>
                  <td><?= $product->client; ?></td>
                  <td><?= $product->DateTemps; ?></td>
                </tr>

              <?php endforeach ?>

            </tbody>

            <tfoot>
              <tr>
                <th></th>
                <th style="text-align: right; padding-right: 20px;"><?= number_format($cumulmontant,0,',',' ');?></th>
              </tr>
            </tfoot>

          </table><?php
        }
      }?>

    </div>

    <div class="cred"><?php

      $Etat="credit";

      if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

        $products =$DB->query("SELECT num_cmd, nom_client as clientvip, Total, remise, montantpaye, reste, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps FROM payement left join client on num_client=client.id WHERE etat='{$Etat}' AND  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'"); 

      }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

        $products =$DB->query("SELECT num_cmd, nom_client as clientvip, Total, remise, montantpaye, reste, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps FROM payement left join client on num_client=client.id WHERE vendeur='{$_POST['magasin']}' and etat='{$Etat}' AND  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

      }else{

        $products =$DB->query("SELECT num_cmd, nom_client as clientvip, Total, remise, montantpaye, reste, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps FROM payement left join client on num_client=client.id WHERE vendeur='{$_SESSION['idpseudo']}' and etat='{$Etat}' AND  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");   
      }

      if (!empty($products)) {?>

        <table class="payement">

          <thead>

            <tr>
              <th class="legende" colspan="7" height="30"><?="Crédits Clients ".$datenormale ?></th>
            </tr>
            <tr>
              <th>N°</th>
              <th>Contact Client</th>
              <th>Heure</th>
              <th>Total</th>
              <th>Remise</th>            
              <th>Montant Payé</th>
              <th>Reste à Payer</th>
            </tr>

          </thead>

          <tbody><?php

            $cumulmontantot=0;
            $cumulmontantrem=0;
            $cumulmontantpaye=0;
            $cumulmontantres=0;
            foreach ($products as $product){
              $cumulmontantot+=$product->Total;
              $cumulmontantrem+=$product->remise;
              $cumulmontantpaye+=$product->montantpaye;
              $cumulmontantres+=$product->reste;?>

              <tr>
                <td><?= $product->num_cmd; ?></td>
                <td><?= $product->clientvip; ?></td>
                <td style="text-align: center"><?= $product->DateTemps; ?></td>

                <td style="text-align:right"><?= number_format($product->Total,0,',',' ') ; ?></td>
                <td style="text-align:right"><?= number_format($product->remise,0,',',' ') ; ?></td>
                <td style="text-align:right"><?= number_format($product->montantpaye,0,',',' '); ?></td>
                
                <td style="color: red;text-align:right"><?= number_format(($product->reste),0,',',' '); ?></td>
              </tr><?php 
            }?>

          </tbody>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($cumulmontantot,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($cumulmontantrem,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($cumulmontantpaye,0,',',' ');?></th>
              <th style="text-align: right; padding-right: 10px;"><?= number_format($cumulmontantres,0,',',' ');?></th>
            </tr>
          </tfoot>

        </table><?php
      }?>

      <table style="margin-top: 30px;" class="payement">

        <thead>
          <tr>
            <th class="legende" colspan="10" height="30"><?="Détails des Produits Vendus " .$datenormale ?></th>
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

      <table style="margin-top: 30px;" class="payement">
        <thead>
          <tr>
            <th class="legende" colspan="14" height="30"><?="Détail des Commandes " .$datenormale ?></th>
          </tr>

          <tr>
            <th>N°</th>
            <th>Heure</th>
            <th>Etat</th>
            <th>Payement</th>
            <th>Remise</th>
            <th>Total</th>
            <th>Montant</th>
            <th>Position</th>
            <th>Contact du Client</th>
            <th>vendeur</th>
            <th colspan="3"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $products =$DB->query('SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \'%H:%i:%s\')AS DateTemps,vendeur, position FROM payement left join client on client.id=num_client WHERE DATE_FORMAT(date_cmd, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_cmd, \'%Y%m%d\') <= :date2', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

          if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

            $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur, position FROM payement left join client on client.id=num_client WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

          }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

            $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur, position FROM payement left join client on client.id=num_client WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

          }else{

            $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur, position FROM payement left join client on client.id=num_client WHERE vendeur='{$_SESSION['idpseudo']}' and  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");
          }

          $cumulmontanremp=0;
          $cumulmontantotp=0;
          $cumulmontanrestp=0;

          foreach ($products as $product ){
            
            $cumulmontanremp+=$product->remise;
            $cumulmontantotp+=$product->Total-$product->remise;
            $cumulmontanrestp+=$product->montantpaye; ?>

            <tr>
              <td><a style="color: red;" href="ticket_pdf.php?ticket=<?=$product->num_cmd;?>" ><?= $product->num_cmd; ?></a></td>
              <td><?= $product->DateTemps; ?></td>
              <td><?= $product->etat; ?></td>
              <td><?= $product->mode_payement; ?></td>
              <td style="text-align:right"><?= number_format($product->remise,0,',',' '); ?></td>
              <td style="text-align: right"><?= number_format(($product->Total-$product->remise),0,',',' '); ?></td>
              <td style="text-align:right"><?= number_format($product->montantpaye,0,',',' '); ?> </td>
              <td style="text-align:left;"><?= $product->position; ?></td>
              <td><?= $product->clientvip; ?></td>
              <td><?=strtolower($panier->nomPersonnel($product->vendeur)[0]) ; ?></td>

              <td></td>

              <td><a href="comptasemaine.php?num_cmd=<?=$product->num_cmd;?>&total=<?=$product->Total-$product->remise;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a></td>
            </tr><?php 
          } ?>   
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4"></th>
            <th style="text-align: right;"><?= number_format($cumulmontanremp,0,',',' ');?></th>
            <th style="text-align: right;"><?= number_format($cumulmontantotp,0,',',' ');?></th>
            <th style="text-align: right;"><?= number_format($cumulmontanrestp,0,',',' ');?></th>
          </tr>
        </tfoot>
      </table><?php
    }elseif(isset($_GET['produit'])){?>

      <div class="container-fluid">

        <div class="row">

          <div class="col-sm-12 col-md-4">

            <table class="table table-hover table-bordered table-striped table-responsive">

              <thead>

                <tr>
                  <th class="text-center bg-info" colspan="2"><?="Produits Vendus " .$datenormale ?></th>
                </tr>

                <tr>
                  <th>Désignation</th>
                  <th>Qtité</th>
                </tr>

              </thead>

              <tbody>
                <?php 
                $total=0;
                $products =$DB->query('SELECT id, nom as designation FROM stock where type!="supplements" and type!="accompagnements" order by(quantity) desc');

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
                  <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
                </tr>

              </tbody>

            </table>

          </div>

          <div class="col-sm-12 col-md-4">

            <table class="table table-hover table-bordered table-striped table-responsive">

              <thead>

                <tr>
                  <th class="text-center bg-info" colspan="2"><?="Accompagnements Vendus " .$datenormale ?></th>
                </tr>

                <tr>
                  <th>Désignation</th>
                  <th>Qtité</th>
                </tr>

              </thead>

            <tbody>
              <?php 
              $total=0;
              $products =$DB->query('SELECT id, nom as designation FROM stock where type="accompagnements" order by(quantity) desc');

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
                      <td style="text-align: left;"><?= $produc->designation; ?></td>
                      <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
                    </tr><?php

                  }else{

                  }
                }
              }?>

              <tr>          
                <th colspan="1" height="40">TOTAL</th>
                <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
              </tr>

            </tbody>

          </table>

        </div>

        <div class="col-sm-12 col-md-4">

          <table class="table table-hover table-bordered table-striped table-responsive">
            <thead>

              <tr>
                <th class="text-center bg-info" colspan="2"><?="Supplements Vendus " .$datenormale ?></th>
              </tr>

              <tr>
                <th>Désignation</th>
                <th>Qtité</th>
              </tr>

            </thead>

            <tbody>
              <?php 
              $total=0;
              $products =$DB->query('SELECT id, nom as designation FROM stock where type="supplements" order by(quantity) desc');

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
                      <td style="text-align: left;"><?= $produc->designation; ?></td>
                      <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
                    </tr><?php

                  }else{

                  }
                }
              }?>

              <tr>          
                <th colspan="1" height="40">TOTAL</th>
                <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
              </tr>

            </tbody>

          </table>
        </div>
      </div><?php

    }else{

      require 'bilansemaine.php';
    }
  }else{

      echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
    }

  }else{

    header('Location: deconnexion.php');


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

