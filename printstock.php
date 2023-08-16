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
    border-collapse: collapse;
  }
  
  .border th {
    border: 1px solid black;
    padding:5px;
    font-weight: bold;
    font-size: 12px;
    color: black;
    background: white;
    text-align: center; }
  .border td {
    padding-bottom: 5px;
    padding-top: 5px;
    border: 1px solid black;    
    font-size: 10px;
    color: black;
    background: white;
    text-align: left;
    padding-right: 5px;}
  .footer{
    font-size: 30px;
    font-style: italic;
  }

  .legende{
    font-size: 14px;
    text-align: center;
    padding-bottom: 5px;
    padding-top: 5px;
  }

</style>

<page backtop="10mm" backleft="5mm" backright="5mm" backbottom="10mm" footer="page;">

  <?php require 'headerticket.php';?>

    <table class="border">

    <thead>

      <tr>
        <th colspan="8"><?="Stock disponible le " .(new dateTime($_SESSION['date']))->format('d/m/Y'); ?><a href="printstock.php?stock"><div class="printstock" style="width: 40px;"></div></a></th>
      </tr>

      <tr>
        <th>Désignation</th>
        <th>Qtité</th>
        <th>P.Achat</th>
        <th>Tot Achat</th>
        <th>P.Revient</th>
        <th>Tot Revient</th>
        <th>P.Vente</th>
        <th>Tot Vente</th>
      </tr>

    </thead>

    <tbody><?php

      $tot_achat=0;
      $tot_revient=0;
      $tot_vente=0;
      $quantite=0;
      $products = $DB->query('SELECT * FROM stock WHERE quantity!=0 AND genre="boissons" ORDER BY (nom)');

      foreach ($products as $product):

        $tot_achat+=$product->prix_achat*$product->quantity;
        $tot_revient+=$product->prix_revient*$product->quantity;
        $tot_vente+=$product->prix_vente*$product->quantity;
        $quantite+=$product->quantity;?>

        <tr>              
          <td style="padding-right: 1px;"><?= ucwords(strtolower($product->nom)); ?></td>
          <td style="text-align: center;"><?= $product->quantity; ?></td>
          <td style="text-align: right;"><?= number_format($product->prix_achat,0,',',' ') ; ?> </td>
          <td style="text-align: right;"><?= number_format($product->prix_achat*$product->quantity,0,',',' ') ; ?> </td>
          <td style="text-align: right;"><?= number_format($product->prix_revient,0,',',' ') ; ?> </td>
          <td style="text-align: right;"><?= number_format($product->prix_revient*$product->quantity,0,',',' ') ; ?> </td>
          <td style="text-align: right;"><?= number_format($product->prix_vente,0,',',' '); ?>  </td>
          <td style="text-align: right;"><?= number_format($product->prix_vente*$product->quantity,0,',',' ') ; ?> </td>
        </tr>
          
      <?php endforeach ?>

    </tbody>

    <tfoot>

      <tr>
        <th colspan="1">TOTAL</th>
        <th style="text-align: center; padding-right: 10px; font-size: 10px;"><?= number_format($quantite,0,',',' ') ; ?> </th>
        <th></th>

        <th style="text-align: right; padding-right: 10px; font-size: 10px;"><?= number_format($tot_achat,0,',',' ') ; ?> </th>
        <th></th>

        <th style="text-align: right; padding-right: 10px; font-size: 10px;"><?= number_format($tot_revient,0,',',' ') ; ?> </th>
        <th></th>

        <th style="text-align: right; padding-right: 10px; font-size: 10px;"><?= number_format($tot_vente,0,',',' ') ; ?> </th>

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
    $pdf->Output('STOCK EDITE'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
?>