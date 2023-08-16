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
    border: 2px solid #CFD1D2;
    padding: 0px;
    font-weight: bold;
    font-size: 18px;
    color: black;
    background: white;
    text-align: right; }
  .border td {
    line-height: 10mm;
    border: 0px solid #CFD1D2;    
    font-size: 18px;
    color: black;
    background: white;
    text-align: center;}
  .footer{
    font-size: 18px;
    font-style: italic;
  }

</style>

<page backtop="10mm" backleft="10mm" backright="10mm" backbottom="10mm" footer="page;">
  
  <?php 

    $_SESSION['reclient']=$_GET['idc'];
    $_SESSION['nameclient']=$_GET['idc'];
    require 'headerticketclient.php';
    $Numv=$_GET['numdec'];

    $payement = $DB->querys('SELECT numcmd, montant, motif, type_versement, DATE_FORMAT(date_versement, \'%d/%m/%Y \à %H:%i:%s\')AS DateV FROM versement WHERE versement.id= ?', array($Numv));?>

    <table style="margin:0px; font-size: 18px;color: black; background: white;" >

      <tr>
        <td><?= "N° Vers.........................." .$payement['numcmd'];?></td>
      </tr>

      <tr>
        <td><?='Motif..............................'.ucwords($payement['motif']); ?></td>
      </tr>

      <tr>
        <td><?="Montant Payé..............." .number_format($payement['montant'],0,',',' ');?> GNF</td>
      </tr>      

      <tr>
        <td><?="Payement:...................." .$payement["type_versement"]; ?></td>
      </tr>

      <tr>
        <td style="margin-bottom: 60px;"><?="Date............................." .$payement["DateV"]; ?></td>
      </tr>
    </table><?php

      require 'piedticket.php';?>
      
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
    $pdf->Output('ticket'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
?>