<?php
require_once "lib/html2pdf.php";
?>

<?php require '_header.php';
$pseudo=$_SESSION['pseudo'];

$prodp = $DB->querys('SELECT level, statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));
ob_start(); ?>

<style type="text/css">
  table {
    width: 100%;
    color: black;
    line-height: 3mm;
    border-collapse: collapse;
    margin-bottom: 30px;
    margin-top: 30px;
  }
    
    .border th{
      text-align: center;
      font-size: 14px;
      padding-left: 5px;
      padding-right:5px;
      border: 1px solid black; 
      height: 20px;
    }

    .border td{
      text-align: left;
      font-size: 14px;
      padding-left: 5px;
      padding-right: 5px;
      border: 1px solid black;

    }

    .compta th{
      height: 20px;
      text-align: center;
      font-size: 14px;
      padding-left: 5px;
      padding-right:5px;
      border: 1px solid black;
    }

    .compta td{
      text-align: center;
      font-size: 12px;
      padding-left: 5px;
      padding-right: 5px;
      border: 1px solid black;
    }
</style>

<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="20mm" >

  <div><?php

    $pseudo=$_SESSION['pseudo'];?>

    <div>

      <table style="margin-top: 10px;" class="border">

        <thead>

          <tr><th colspan="6">Bilan du <?=(new dateTime($_SESSION['date01']))->format('d/m/Y');?> au <?=(new dateTime($_SESSION['date02']))->format('d/m/Y');?></th></tr>

          <tr>                  
            <th>Nbre V</th>
            <th>Ventes</th>
            <th>Dépenses</th>
            <th>Bénéfice</th>
            <th>Fond de Caisse</th>
            <th>Différence</th>
          </tr>

        </thead>

        <tbody>

          <tr><?php 

            $prodnbre =$DB->querys("SELECT Count(num_cmd) as nbre FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

            $prodnbretot =$DB->querys("SELECT sum(Total) as tot, sum(fraisup) as frais, sum(remise) as remise FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

            $totvente=$prodnbretot['tot']-$prodnbretot['remise']-$prodnbretot['frais']?>

            <td style="text-align: center;"><?=$prodnbre['nbre']; ?></td>

            <td style="text-align: center;"><?=number_format($totvente,0,',',' '); ?></td>

            <td style="text-align: center;"><?=number_format($panier->depenseTot($_SESSION['date1'], $_SESSION['date2']),0,',',' '); ?></td>

            <td style="text-align: center;"><?=number_format($panier->benefice($_SESSION['date1'], $_SESSION['date2']),0,',',' '); ?></td>
                        
            <td><?=number_format($_SESSION['fcaisse'],0,',',' ');?></td>

            <td style="text-align:center;"><?=number_format(-$_SESSION['difference'],0,',',' ');?></td>
          </tr>

        </tbody>

      </table>
    </div>

    <div class="bloc_bilan">
              
      <table class="border">

        <thead>

          <tr>
            <th>Désignation</th>
            <th>Montant</th>
          </tr>

        </thead>

        <tbody><?php


          $mode_payement = array(

            "Espèces" => "espèces",
            "Virement" => "virement",
            "Chèque" => "cheque",
            "Virement Bancaire" => "vire bancaire",
            "differe" => "differe"        
          );

           $tot_enc=0;
          foreach ($mode_payement as $key => $produc ){

            if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

              $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");  

            }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

              $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");

            }else{
              
              $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");    
            }

                 

            if (empty($product['mode_payement'])) {

            }else{

              $tot_enc+=($product['TOT']-$product['REM']-$product['frais']); ?>

              <tr >                
                <td ><?=ucwords($key.' '.'encaissés');?></td>              
                <td style="text-align: right" ><?=number_format(($product['montantp']-$product['frais']),0,',',' ');?></td>
              </tr><?php

            }
          }?>

          <tr>
            <td>Chiffre d'affaires</td>
            <td style="text-align:right;"><?=number_format($tot_enc,0,',',' ');?></td>
          </tr><?php
          $total_cours=0;
          $totalpaye=0;
          $remise=0;
          $credclient_gnf=0; 

          $etat='credit'; 

          if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

            $restep=$DB->querys("SELECT SUM(Total) AS totc, SUM(montantpaye) AS montc, SUM(reste) as reste, mode_payement, SUM(remise) AS remc, sum(fraisup) as frais FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND etat= '{$etat}'");  

          }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

            $restep=$DB->querys("SELECT SUM(Total) AS totc, SUM(montantpaye) AS montc, SUM(reste) as reste, mode_payement, SUM(remise) AS remc, sum(fraisup) as frais FROM payement WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND etat= '{$etat}'"); 

          }else{

            $restep=$DB->querys("SELECT SUM(Total) AS totc, SUM(montantpaye) AS montc, SUM(reste) as reste, mode_payement, SUM(remise) AS remc, sum(fraisup) as frais FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND etat= '{$etat}'");    
          }

          $total_cours=$restep['totc'];
          $totalpaye=$restep['montc'];
          $remise= $restep['remc'];
          $credclient_gnf= $restep['reste']; ?>

          <tr>
            <td>Crédits Clients</td>
            <td style="text-align:right;"><?=number_format(($credclient_gnf),0,',',' ');?></td>
          </tr><?php

          $versementgnf=$panier->versementC($_SESSION['date1'], $_SESSION['date2']);?>              

          <tr>
            <td>Totaux Versements</td>
            <td style="text-align:right;"><?=number_format(($versementgnf),0,',',' ');?></td>
          </tr>

          <tr>
            <td>Totaux Net Encaissés</td>
            <td style="text-align:right;"><?=number_format(($tot_enc-($credclient_gnf-$restep['frais'])+$versementgnf),0,',',' ');?></td>
          </tr>

          <tr>
            <td style="text-align: center; background-color: yellow; color: red;" colspan="2">PARTIE DECAISSEMENT</td>
          </tr><?php         

          $montdec_gnf=0;

          foreach ($mode_payement as $key=> $produc ){

            $prodec =$DB->querys('SELECT SUM(montant) AS montant, payement FROM decaissement WHERE (DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2) and payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodep =$DB->querys('SELECT SUM(montant) AS montant, payement FROM decdepense WHERE (DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2) and payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodlo =$DB->querys('SELECT SUM(montant) AS montant, payement FROM decloyer WHERE (DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2) and payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodpers =$DB->querys('SELECT SUM(montant) AS montant, payement FROM decpersonnel WHERE (DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2) and payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodfraisup=$DB->querys('SELECT SUM(montant) AS montant FROM fraisup WHERE DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2 AND payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodfraismarch=$DB->querys('SELECT SUM(frais) AS montant FROM facture WHERE DATE_FORMAT(datecmd, \'%Y%m%d\') >= :date1 and DATE_FORMAT(datecmd, \'%Y%m%d\') <= :date2 AND payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $prodfourn=$DB->querys('SELECT SUM(montant) AS montant FROM histpaiefrais WHERE DATE_FORMAT(date_cmd, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_cmd, \'%Y%m%d\') <= :date2 AND payement= :payement', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2'], 'payement'=>$produc));

            $montdec=$prodec['montant']+$prodep['montant']+$prodlo['montant']+$prodpers['montant']+$prodfraisup['montant']+$prodfourn['montant']+$prodfraismarch['montant'];

            $montdec_gnf+=$montdec ?>

            <tr>               
              <td ><?=ucwords($key);?></td>              
              <td style="text-align: right"  ><?=number_format($montdec,0,',',' ');?></td>
            </tr> <?php 
          }?>

        <tr>
          <td>Totaux Décaissements</td>

          <td style="text-align:right;"><?=number_format($montdec_gnf,0,',',' ');?></td>

        </tr><?php

        $creditfact=0;

        $creditfact=$panier->creditF($_SESSION['date1'], $_SESSION['date2']);?><?php 

        if ($panier->fondCaisse()<0) {
          $colorf='green';
        }else{
          $colorf='green';
        }

        

        if ($panier->totalcaisse()<0) {
          $colorc='green';
        }else{
          $colorc='green';
        }

        $soldet=0;

        foreach ($panier->nomBanque() as $banque) {?>

          <tr><?php

            $soldet+=$panier->montantCompte($banque->id);

            if ($_SESSION['level']>6) {?>
              
              <th><?=strtoupper($banque->nomb);?></th>

              <th style="font-weight: bold; font-size: 14; "><?=number_format($panier->montantCompte($banque->id),0,',',' ');?></th><?php 
            }?>
          </tr><?php
        }

        if ($_SESSION['level']>6) {?>
          <tr>
            <th colspan="6" style="font-weight: bold; font-size: 14;">Total <?=number_format($soldet,0,',',' ');?></th>
          </tr><?php
        }?>

      </tbody>
    </table>

  </div>

  <div class="bloc_prodinv" style="width: 30%;">

    <table class="border">

      <thead>
        <tr>
          <th class="legende" colspan="2"><?="Produits Vendus "?></th>
        </tr>

        <tr>
          <th style="width: 90%;">Désignation</th>
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

              $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'");    
            }    

            foreach ($products as $product ){

              $total+= $product->qtite;

              if (!empty($product->qtite)) {?>

                <tr>
                  <td style="text-align: left;"><?= ucwords($produc->designation); ?></td>
                  <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
                </tr><?php

              }else{

              }
            }
          }?>

          <tr>          
            <th colspan="1">TOTAL</th>
            <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
          </tr>

        </tbody>

      </table>
    </div>

    <div>

    <table class="border">

      <thead>

        <tr>
          <th class="legende" colspan="2" height="30"><?="Accompagnements Vendus " ;?></th>
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

            $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'");    
          }     

          foreach ($products as $product ){

            $total+= $product->qtite;

            if (!empty($product->qtite)) {?>

              <tr>
                <td style="text-align: left;"><?= ucwords($produc->designation); ?></td>
                <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
              </tr><?php

            }else{

            }
          }
        }?>

        <tr>          
          <th colspan="1">TOTAL</th>
          <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
        </tr>

      </tbody>

    </table>

  </div>

  <div>

    <table class="border">

      <thead>

        <tr>
          <th class="legende" colspan="2" height="30"><?="Supplements Vendus "; ?></th>
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

            $products =$DB->query("SELECT SUM(quantity) AS qtite FROM commande inner join payement on payement.num_cmd=commande.num_cmd WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' AND commande.id_produit= '{$produc->id}'");    
          }     

          foreach ($products as $product ){

            $total+= $product->qtite;

            if (!empty($product->qtite)) {?>

              <tr>
                <td style="text-align: left;"><?= ucwords($produc->designation); ?></td>
                <td style="text-align:center;"><?= number_format($product->qtite,1,',',' '); ?></td>
              </tr><?php

            }else{

            }
          }
        }?>

        <tr>          
          <th colspan="1">TOTAL</th>
          <th style="text-align: center;"><?= number_format($total,1,',',' '); ?></th>          
        </tr>

      </tbody>

    </table>

  </div>

  <div>

    <table class="border">
      <thead>

        <tr><th colspan="4" height="30"><?="Tableau des ingrédients Vendus " ?></th></tr>
        <tr>
          <th>N°</th>
          <th>Désignation</th>
          <th>Qtite Vendues</th>
          <th>Qtite dispo</th>
        </tr>
      </thead>
      <tbody><?php

        

        $cumulmontanremp=0;
        $cumulmontantotp=0;
        $cumulmontanrestp=0;

        if (isset($_GET['clientsearch'])) {

          $prodingredient = $DB->query("SELECT id, nom, qtite FROM ingredient where id='{$_GET['clientsearch']}'");
        }else{

          $prodingredient = $DB->query("SELECT id, nom, qtite FROM ingredient");

        }

        foreach ($prodingredient as $key=> $valuei ){

          $sortie='sortie';

          if (isset($_POST['j1'])) {

            $products=$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv where idstock='{$valuei->id}' and libelle='{$sortie}' and DATE_FORMAT(dateop, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(dateop, \"%Y%m%d\") <= '{$_SESSION['date2']}'");          

          }elseif (isset($_GET['clientsearch'])) {

            $products=$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv where idstock='{$_GET['clientsearch']}' and libelle='{$sortie}' ");         

          }else{

            $products =$DB->querys("SELECT sum(qtiterecette) as qtite FROM ingredientmouv WHERE  idstock='{$valuei->id}' and libelle='{$sortie}' and DATE_FORMAT(dateop, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(dateop, \"%Y%m%d\") <= '{$_SESSION['date2']}'"); 
          }

          if (!empty($products['qtite'])) {?>

            <tr>
              <td style="text-align:center;"><?=$key+1;?></td>
              <td><?=ucwords(strtolower($valuei->nom));?></td>

              <td style="text-align:center;"><?=number_format(-1*$products['qtite'],2,',',' ');?></td>

              <td style="text-align:center;"><?=$valuei->qtite;?></td>

              
            </tr><?php 
          }
        } ?>   
      </tbody>
    </table>
  </div>

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

      <table class="border">

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

          <table class="border">

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

    <div class="dec"><?php

    if ($_SESSION['level']>6) {

      $products =$DB->query('SELECT decaissement.id as id, montant, nom_client as client, coment, DATE_FORMAT(date_payement, \'%H:%i:%s\')AS DateTemps FROM decaissement inner join client on client=client.id  WHERE DATE_FORMAT(date_payement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\') <= :date2', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

      if (!empty($products)) {?>
       
        <table  class="border">

          <thead>

            <tr>
              <th class="legende" colspan="5" height="30"><?="Liste des Décaissements du ";?></th>
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

      $products =$DB->query("SELECT num_cmd, nom_client as clientvip, Total, remise, montantpaye, reste, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps FROM payement left join client on num_client=client.id WHERE etat='{$Etat}' AND  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");   
    }

    if (!empty($products)) {?>

      <table class="border">

        <thead>

          <tr>
            <th class="legende" colspan="7" height="30"><?="Crédits Clients "; ?></th>
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

    <table style="margin-top: 30px;" class="border">

      <thead>
        <tr>
          <th class="legende" colspan="9" height="30"><?="Détails des Produits Vendus " ;?></th>
        </tr>

        <tr>

          <th>Désignation</th>
          <th>Qtité</th>
          <th>P.Vente</th>
          <th>P.Revient</th>
          <th>Bénéfice</th>
          <th>Heure</th>
          <th>Etat</th>
          <th>Client</th>
          <th>vendeur</th>
        </tr>
      </thead>

      <tbody><?php  

        if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

          $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

        }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

          $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

        }else{

          $products =$DB->query("SELECT nom as designation, commande.quantity as quantity, commande.prix_vente as prix_vente,commande.prix_revient as prix_revient, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM stock inner join commande on commande.id_produit=stock.id inner join payement on payement.num_cmd=commande.num_cmd left join client on client.id=id_client WHERE  DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'"); 
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
            <td><?= $product->designation; ?></td>
            <td style="text-align:center"><?= $product->quantity; ?></td>
            <td style="text-align: right"  ><?= number_format($product->prix_vente*$product->quantity,0,',',' '); ?></td>
            <td style="text-align: right"  ><?= number_format($product->prix_revient*$product->quantity,0,',',' '); ?></td>
            <td style="text-align: right"  ><?= number_format($product->prix_vente*$product->quantity-$product->prix_revient*$product->quantity,0,',',' '); ?></td>
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

    <table style="margin-top: 30px;" class="border">
      <thead>
        <tr>
          <th class="legende" colspan="10" height="30"><?="Détail des Commandes "; ?></th>
        </tr>

        <tr>
          <th>N°</th>
          <th>Heure</th>
          <th>Etat</th>
          <th>Payement</th>
          <th>Remise</th>
          <th>Total</th>
          <th>Montant</th>
          <th>Contact du Client</th>
          <th>vendeur</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $products =$DB->query('SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \'%H:%i:%s\')AS DateTemps,vendeur FROM payement left join client on client.id=num_client WHERE DATE_FORMAT(date_cmd, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_cmd, \'%Y%m%d\') <= :date2', array('date1' => $_SESSION['date1'],'date2' => $_SESSION['date2']));

        if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

          $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM payement left join client on client.id=num_client WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

        }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

          $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM payement left join client on client.id=num_client WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");

        }else{

          $products =$DB->query("SELECT num_cmd, remise, montantpaye, Total, mode_payement, etat, nom_client as clientvip, DATE_FORMAT(date_cmd, \"%H:%i:%s\")AS DateTemps,vendeur FROM payement left join client on client.id=num_client WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'");
        }

        $cumulmontanremp=0;
        $cumulmontantotp=0;
        $cumulmontanrestp=0;

        foreach ($products as $product ){
          
          $cumulmontanremp+=$product->remise;
          $cumulmontantotp+=$product->Total-$product->remise;
          $cumulmontanrestp+=$product->montantpaye; ?>

          <tr>
            <td><a style="color: red;" href="recherche.php?recreditc=<?=$product->num_cmd;?>"><?= $product->num_cmd; ?></a></td>
            <td><?= $product->DateTemps; ?></td>
            <td><?= $product->etat; ?></td>
            <td><?= $product->mode_payement; ?></td>
            <td style="text-align:right"><?= number_format($product->remise,0,',',' '); ?></td>
            <td style="text-align: right"><?= number_format(($product->Total-$product->remise),0,',',' '); ?></td>
            <td style="text-align:right"><?= number_format($product->montantpaye,0,',',' '); ?> </td>


            <td><?= $product->clientvip; ?></td>
            <td><?=strtolower($panier->nomPersonnel($product->vendeur)[0]) ; ?></td>
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
    </table>
  </div>

  
  </div>



    

</page><?php

$content = ob_get_clean();
try {
  $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
  $pdf->pdf->SetAuthor('Amadou');
  $pdf->pdf->SetTitle(date("d/m/y"));
  $pdf->pdf->SetSubject('Création d\'un Portfolio');
  $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
  $pdf->pdf->IncludeJS("print(true);");
  $pdf->writeHTML($content);
  ob_clean();
  $pdf->Output('compta du'.date("d/m/y").date("H:i:s").'.pdf');
  // $pdf->Output('Devis.pdf', 'D');    
} catch (HTML2PDF_exception $e) {
  die($e);
}
//header("Location: index.php");
?>