<?php
require_once "lib/html2pdf.php";
require '_header.php';
ob_start(); ?>
<style type="text/css">
  body{
    margin: 0px;
    width: 100%;
    height:68%;
    padding:0px;
  }
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
    font-size: 30px;
    color: black;
    background: white;
    text-align: right; }
  .border td {
    line-height: 10mm;
    border: 0px solid #CFD1D2;    
    font-size: 30px;
    color: black;
    background: white;
    text-align: center;}
  .footer{
    font-size: 30px;
    font-style: italic;
  }
</style>

<page backtop="0mm" backleft="0mm" backright="0mm" backbottom="10mm" footer="page;"><?php

  $table=$_GET['idtable'];
  $adress = $DB->querys('SELECT * FROM adresse ');
  $total = 0;
  $total_tva = 0; ?>

  <div class="ticket">
    <table style="margin:auto; text-align: center;color: black; background: white;" >
      <tbody>
        <tr>
          <th style="font-weight: bold; font-size: 30px; padding: 5px"><?=$adress['nom_mag']; ?></th>
        </tr>
        <tr>      
          <td style="font-size: 30px;"><?=$adress['type_mag']; ?></td>
        </tr>

        <tr>      
          <td style="font-size: 30px;"><?=($adress['adresse']); ?></td>
        </tr>
      </tbody>

    </table><?php

    $prodpaie = $DB->querys("SELECT sum(pvente*quantite) as ptotal FROM tablecommande where idtable='{$table}' and pseudov='{$_SESSION['idpseudo']}'");

    $totalp= $prodpaie['ptotal'];

    $products = $DB->query("SELECT stock.id as id, tablecommande.id as idv, id_produit, tablecommande.quantite as quantite, stock.nom as nom, pvente, pvente as prix_vente, stock.type as type FROM tablecommande inner join stock on stock.id=tablecommande.id_produit  where idtable='{$table}' and pseudov='{$_SESSION['idpseudo']}' order by(tablecommande.id)");?>

    <table class="border" style="margin: auto; margin-top:30px;">
      <thead>
        <tr><th colspan="3" style="text-align: center;">Commande <?=$panier->nomTable($table)[0]; ?> à <?=date("h:i");?></th></tr>
        <tr>
          <th style="text-align: center;">Qtite</th>
          <th style="text-align: center;">Désignation</th>
          <th style="text-align: center;">Total</th>
        </tr>

      </thead><?php

      $totachat=0; 

      foreach ($products as $key => $product) {


        $totachat+=$product->prix_vente*$product->quantite;?>

        <tbody>
          <tr>
            <td style="text-align: center;"><?=$product->quantite;?></td>

            <td style="text-align: left;"><?=ucfirst(strtolower($product->nom)); ?></td>

            <td style="text-align: right; padding-right: 10px;"><?= number_format($product->prix_vente,0,',',' '); ?></td>
          </tr>
        </tbody><?php 
      }?>

      <tfoot>
        <tr><th colspan="3" style="text-align: center;">Total: <?= number_format(($totalp),0,',',' '); ?></th></tr>
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
    $pdf->Output('ticket'.date("d/m/y").date("H:i:s").'.pdf');
    // $pdf->Output('Devis.pdf', 'D');    
  } catch (HTML2PDF_exception $e) {
    die($e);
  }
//header("Refresh: 10; URL=index.php");
?>