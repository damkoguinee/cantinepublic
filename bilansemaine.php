

  <div class="col-sm-12 col-md-4">
          
    <table class="table table-hover table-bordered table-striped table-responsive">

      <thead>

        <tr>
          <th class="text-center bg-info" colspan="2"><?="Bilan du " .$datenormale ?></th> 
        </tr>

        <tr>
          <th class="text-center bg-info">Désignation</th>
          <th class="text-center bg-info">Montant</th>
        </tr>

      </thead>

      <tbody><?php

      $tot_enc=0;
      foreach ($panier->modep as $key=> $produc){

        if (isset($_POST['magasin']) and $_POST['magasin']=='general') {

          $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");  

        }elseif (isset($_POST['magasin']) and $_POST['magasin']!='general') {

          $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE vendeur='{$_POST['magasin']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");

        }else{
          
          $product =$DB->querys("SELECT SUM(Total) AS TOT, SUM(montantpaye) as montantp, SUM(remise) AS REM, SUM(fraisup) AS frais, mode_payement FROM payement WHERE vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND mode_payement= '{$produc}'");    
        }

             

        if (empty($product['mode_payement'])) {

        }else{

          $tot_enc+=($product['TOT']-$product['REM']-$product['frais']); ?>

          <tr >                
            <td ><?=ucwords($produc.' '.'encaissés');?></td>              
            <td style="text-align: right" ><?=number_format(($product['montantp']-$product['frais']),0,',',' ');?></td>
          </tr><?php

        }
      }
      $versementgnf=$panier->remboursementC($_SESSION['date1'], $_SESSION['date2'], 'credit');?>              

      <tr>
        <td>Remboursements Créances</td>
        <td style="text-align:right;"><?=number_format(($versementgnf),0,',',' ');?></td>
      </tr>

          

      <?php
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

        $restep=$DB->querys("SELECT SUM(Total) AS totc, SUM(montantpaye) AS montc, SUM(reste) as reste, mode_payement, SUM(remise) AS remc, sum(fraisup) as frais FROM payement WHERE vendeur='{$_SESSION['idpseudo']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") >= '{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}'  AND etat= '{$etat}'");    
      }

      $total_cours=$restep['totc'];
      $totalpaye=$restep['montc'];
      $remise= $restep['remc'];
      $credclient_gnf= $restep['reste']; ?>      

      <tr>
        <td colspan="2" class="text-center bg-info fw-bold fs-6">Net Encaissés <?=number_format(($tot_enc-($credclient_gnf-$restep['frais'])+$versementgnf),0,',',' ');?></td>
      </tr>

      <tr>
        <td>Créances Clients</td>
        <td style="text-align:right;"><?=number_format(($credclient_gnf),0,',',' ');?></td>
      </tr>

      <tr>
        <td colspan="2" class="text-center bg-info fw-bold fs-6">Chiffre d'affaires <?=number_format(($tot_enc),0,',',' ');?></td>
      </tr>

        <tr>
          <td style="text-align: center; background-color: yellow; color: red;" colspan="2">PARTIE DECAISSEMENT</td>
        </tr><?php         

        $montdec_gnf=0;

        foreach ($panier->modep as $key=> $produc ){

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
            <td ><?=ucwords($produc);?></td>              
            <td style="text-align: right"  ><?=number_format($montdec,0,',',' ');?></td>
          </tr> <?php 
        }?>

      <tr>
        <td>Totaux Décaissements</td>

        <td style="text-align:right;"><?=number_format($montdec_gnf,0,',',' ');?></td>

      </tr>

      <tr>
        <td class="text-center bg-success fw-bold" colspan="2">CAISSES</td>
      </tr><?php
      $colorf='green';


      $soldet=0;

      foreach ($panier->nomBanque() as $banque) {?>

        <tr><?php

          $soldet+=$panier->caisseJour($banque->id, $_SESSION['date1'], $_SESSION['date2']);

          if ($_SESSION['level']>3) {?>
            
            <th><?=strtoupper($banque->nomb);?></th>

            <th style="text-align: right"><?=number_format($panier->caisseJour($banque->id, $_SESSION['date1'], $_SESSION['date2']),0,',',' ');?></th><?php 
          }?>
        </tr><?php
        }

        $soldet=0;

        foreach ($panier->nomBanque() as $banque) {?>

            <tr><?php

              $soldet+=$panier->montantCompte($banque->id);

              if ($_SESSION['level']>3) {?>
                
                <th>Cumul <?=strtoupper($banque->nomb);?></th>

                <th style="text-align: right"><?=number_format($panier->montantCompte($banque->id),0,',',' ');?></th><?php 
              }?>
            </tr><?php
        }

        if ($_SESSION['level']>3) {?>
            <tr>
              <th colspan="6" class="text-center">Total <?=number_format($soldet,0,',',' ');?></th>
            </tr><?php
        }?>

    </tbody>
  </table>
</div>