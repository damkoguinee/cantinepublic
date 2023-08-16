<?php
require_once "lib/html2pdf.php";
ob_start(); ?>

<?php require '_header.php';?>

<style type="text/css">

body{
  margin: 0px;
  width: 100%;
  height:68%;
  padding:0px;}
  .ticket{
    margin:0px;
    width: 100%;
  }
  table {
    width: 100%;
    color: #717375;
    font-family: helvetica;
    line-height: 10mm;
    border-collapse: collapse;
  }
  
  .border th {
    border: 1px solid black;
    padding-bottom: 5px;
    padding-top: 5px;
    font-weight: bold;
    font-size: 14px;
    color: black;
    background: white;
    text-align: right; }
  .border td {
    line-height: 15mm;
    padding-bottom: 5px;
    padding-top: 5px;
    border: 1px solid black;    
    font-size: 14px;
    color: black;
    background: white;
    text-align: left;}
  .footer{
    font-size: 18px;
    font-style: italic;
  }

  .legende{
    font-size: 18px;
    text-align: center;
    padding-bottom: 5px;
    padding-top: 5px;
  }

</style>

<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">

  <?php 
    $_SESSION['reclient']=$_GET['compte'];
    require 'headerticketclient.php';

    unset($_SESSION['reclient']);

    $solde=0;
    $prod = $DB->query('SELECT nom_client, libelles, numero, montant, DATE_FORMAT(date_versement, \'%d/%m/%Y \')AS DateTemps FROM bulletin WHERE nom_client= :CLIENT AND DATE_FORMAT(date_versement, \'%Y%m%d\') >= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\') <= :date2 ORDER BY (date_versement) DESC', array('CLIENT' => $_GET['compte'], 'date1' => $_GET['date1'], 'date2' => $_GET['date2']));

    foreach ($prod as $product ){

      $solde+=$product->montant;

      if ($solde>0) {
        $solde=(-1)*$solde;
      }else{

         $solde=$solde;
      }

    }?>

    <table style="margin-top: 20px; margin-left:0px;" class="border">

      <thead><?php 
        $date1=(new DateTime($_GET['date1']))->format('d/m/Y');
        $date2=(new DateTime($_GET['date2']))->format('d/m/Y')?>

        <tr>
          <th colspan="2" style="background-color: white; color: black; font-size: 14px; padding: 5px; padding-bottom:20px; text-align: center;">Situation du compte entre le <?=$date1;?> et le <?=$date2;?> </th>  

          <th colspan="2" style="background-color: white; color: black; text-align: center; font-size: 14px;">Solde: <?= number_format($solde,0,',',' ') ; ?></th>
        </tr>

        <tr>
          <th style="width: 18%; text-align: center;">Date</th>
          <th style="width: 46%;text-align: center;">Désignation</th>
          <th style="width: 18%;text-align: center;">Debiter</th>
          <th style="width: 18%;text-align: center;">Créditer</th>
        </tr>

      </thead>

      <tbody><?php 
        
        foreach ($prod as $product ):

          $solde+=$product->montant; ?>

          <tr>
            <td style="width: 18%; text-align: center;;"><?= $product->DateTemps; ?></td>
            <td style="width: 46%; text-align: left;"><?= ucfirst($product->libelles)." N° ".$product->numero; ?></td>

            <?php if ($product->montant<0) {?>

              <td style="width: 18%; text-align: right"><?= number_format((-1)*$product->montant,0,',',' '); ?></td>
              <td style="width: 18%; text-align: left;"></td><?php

            }else{?>

              <td style="width: 18%; text-align: left;"></td>

              <td style="width: 18%; text-align: right"><?= number_format($product->montant,0,',',' '); ?></td><?php
            }?>

          </tr>

        <?php endforeach ?>

      </tbody>

      <tfoot>

       
        <?php $zero=0;

        $products = $DB->query('SELECT SUM(montant) AS sommedebit FROM bulletin WHERE nom_client= :CLIENT AND montant< :MONTANT ', array(
          'CLIENT' => $_SESSION['client'],
          'MONTANT'=> $zero                  
        ));

        foreach ( $products as $somme ):?>

        <?php endforeach; ?>

        <tr>
          <th colspan="2" style="padding-right: 10px; text-align:center;">Totaux</th>           
          <th style="text-align: right;"><?= number_format((-1)*$somme->sommedebit,0,',',' ') ; ?></th>

          <?php $zero=0;

          $products = $DB->query('SELECT SUM(montant) AS sommecredit FROM bulletin WHERE nom_client= :CLIENT AND montant> :MONTANT ',array(
            'CLIENT' => $_SESSION['client'],
            'MONTANT'=> $zero                  
          ));

          foreach ( $products as $somme ):?>

          <?php endforeach; ?>

          <th style="text-align: right;"><?= number_format($somme->sommecredit,0,',',' ') ; ?></th>            
        </tr>

      </tfoot>
    </table>
  </div>

</page>
<?php
  $content = ob_get_clean();
  try {
    $pdf = new HTML2PDF("p","A4","fr", true, "UTF-8" , 0);
    $pdf->pdf->SetAuthor('Amadou');
    $pdf->pdf->SetTitle(date("d/m/y"));
    $pdf->pdf->SetSubject('Création d\'un Portfolio');
    $pdf->pdf->SetKeywords('HTML2PDF, Synthese, PHP');
    $pdf->pdf->IncludeJS("print(true);");
    $pdf->writeHTML($content);
    $pdf->Output('COMPTE EDITE'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
?>