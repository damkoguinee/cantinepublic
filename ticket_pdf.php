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
    font-size: 40px;
    color: black;
    background: white;
    text-align: center;}
  .footer{
    font-size: 40px;
    font-style: italic;
  }
</style>

<page backtop="0mm" backleft="0mm" backright="0mm" backbottom="10mm" footer="page;"><?php
  $adress = $DB->querys('SELECT * FROM adresse ');
  $total = 0;
  $total_tva = 0; ?>

  <div class="ticket">
    <table style="margin:auto; text-align: center;color: black; background: white;" >
      <tbody>
        <tr>
          <th style="font-weight: bold; font-size: 35px; padding: 5px"><?=$adress['nom_mag']; ?></th>
        </tr>
        <tr>      
          <td style="font-size: 35px;"><?=$adress['type_mag']; ?></td>
        </tr>

        <tr>      
          <td style="font-size: 35px;"><?=($adress['adresse']); ?></td>
        </tr>
      </tbody>

    </table><?php
    if (isset($_GET['ticket'])) {

      $Num_cmd=$_GET['ticket'];

    }elseif (isset($_GET['ticketrechercher'])) {

      $Num_cmd=$_SESSION['numcmd'];

    }elseif (isset($_GET['numcmd'])) {

      $Num_cmd=$_GET['numcmd'];

    }else{

      $productnum = $DB->querys("SELECT date_cmd, max(num_cmd) as num, montantpaye, remise, reste FROM payement ");
      $Num_cmd=$productnum['num'];

    }

    $payement= $DB->querys('SELECT *FROM payement WHERE num_cmd=:Num', array('Num'=>$Num_cmd));

    $frais=$DB->querys('SELECT numcmd, montant, motif  FROM fraisup WHERE numcmd= ?', array($Num_cmd));?>

    ********************************************************************************************************************************************************

    <table style="margin:0px; margin-top: 30px; font-size: 40px;color: black; background: white;" >

      <tbody>

        <tr>
          <td><?="N° cmd: " .$Num_cmd; ?></td>

          <td style="border: 2px solid #CFD1D2; font-size: 40px;">
            <strong><?= "Ticket N°: " .$payement['num_ticket']; ?></strong>
          </td>
        </tr>

        <tr>
          <td><?="Paiement:  " .$payement['mode_payement']; ?> </td>

          <td style="font-size: 35px;"><label style="font-weight: bold; font-size: 35px;"><?=$payement['position'];?><?php if ($payement['position']=='surplace') {?> <?=' /'.$payement['idtable']; ?><?php }?></label></td> 
        </tr>
        <tr> 
          <td>
            <?php echo "Date   " .(new dateTime($payement['date_cmd']))->format('d/m/Y à H:i'); ?>     
          </td> 
        </tr>
        <tr>
          <td>
            <?php echo "Vendeur: " .strtolower($panier->nomPersonnel($payement['vendeur'])[0]); ?>       
          </td>
        </tr>
      </tbody>
      
    </table><?php 

    if ($payement['position']=='livraison') {?>

      ********************************************************************************************************************************************************

      <table style="margin:auto; margin-top: 30px; font-size: 40px;color: black; background: white;">
        <tbody>
          <tr>
            <td><?=$panier->adClient($payement['num_client'])[0];?></td>
          </tr>

          <tr>
            <td><?=$panier->adClient($payement['num_client'])[1];?></td>
          </tr>

          <tr>
            <td><?=$panier->adClient($payement['num_client'])[2];?></td>
          </tr>
        </tbody>
      </table>

      ********************************************************************************************************************************************************<?php 
    }?>



    <table style="margin-top: 30px; margin-left:0px;" class="border">
        <tbody>
          <tr>
            <th style="width: 7%;"></th>
            <th style="width: 1%; text-align: left;"></th>
            <th style="width: 60%;"></th>
            <th style="width: 32%; padding-right: 15px;"></th>
          </tr>
        </tbody>
      </table>
      
      <table style="margin-top: 1px; margin-left:0px;" class="border">
        <tbody><?php 
        $total=0;

        $productcom= $DB->query('SELECT commande.quantity as quantity, commande.prix_vente as prix_vente, nom, type FROM commande inner join stock on stock.id=id_produit WHERE num_cmd=:Num order by(commande.id)', array('Num'=>$Num_cmd));

        $prodaccomp= $DB->query('SELECT commande.quantity as quantity, commande.prix_vente as prix_vente, nom FROM commande inner join stock on stock.id=id_produit WHERE num_cmd=:Num and type="accompagnements" order by(commande.id)', array('Num'=>$Num_cmd));

        foreach ($productcom as $product){?>
          <tr>
            <td style="width: 7%;border:0px"><?= $product->quantity; ?></td>
            <td style="width: 1%;border:0px; text-align:right"></td><?php 

            if ($product->type=='accompagnements') {?>

              <td style="width: 60%;border:0px;text-align:left; font-size:35px; padding-left: 15px;"><?=ucfirst(strtolower($product->nom)); ?></td>

              <td style="width: 32%;border:0px; text-align:right; padding-right: 10px;"></td><?php
            }elseif ($product->prix_vente==0) {?>

              <td style="width: 60%;border:0px;text-align:left; font-size:35px; padding-left: 15px;"><?=ucfirst(strtolower($product->nom)); ?></td>

              <td style="width: 32%;border:0px; text-align:right; padding-right: 30px;">offert</td><?php
            }else{?>

              <td style="width: 60%;border:0px;text-align:left"><?=ucfirst(strtolower($product->nom)); ?></td>
              <td style="width: 32%;border:0px; text-align:right; padding-right: 15px;"><?= number_format($product->prix_vente*$product->quantity,0,',',' '); ?></td><?php

            }?>
            <?php
            $price=($product->prix_vente*$product->quantity);         
            $total += $price;?>

          </tr><?php 
        }
        

        if (!empty($frais['motif'])) {?>

          <tr>
            <td style="width: 7%;">-</td>
            <td style="width: 1%;"></td>             
            <td style="width: 60%;text-align:left"><?=ucfirst($frais['motif']); ?></td>
            <td style="width: 32%; text-align:right; padding-right: 15px;"><?=number_format($frais['montant'],0,',',' '); ?></td>
          </tr><?php
        }

        $total=$total+$frais['montant'];
        $montantverse=$payement['montantpaye'];
        $Remise=$payement['remise'];
        $ttc = $total-$Remise; 
        $tot_Rest = $payement['reste']; 
        ?>

      <tr>
        <td colspan="4" style="border:0px; padding-top: 50px;" class="space"></td>
      </tr>
      <tr>
        <td colspan="2" rowspan="4" style="padding: 1px; text-align: left; font-size:35px;"></td>
      </tr>

      <tr>
        <td style="text-align: right;" class="no-border">Total </td>
        <td style="text-align:right; padding-right: 15px;"><?php echo number_format($total,0,',',' ') ?></td>
      </tr>

      <tr>
        <td style="text-align: right;" class="no-border">Remise <?php if (!empty($Remise)) {?> de <?=($Remise/$total)*100;?>%<?php }?></td>               
        <td style="text-align:right; padding-right: 15px;"><?php echo number_format($Remise,0,',',' ') ?></td>        
      </tr>

      <tr>
        <td style="text-align: right; margin-bottom: 5px" class="no-border">Total Net </td>
        <td style="text-align:right; font-weight: bold; padding-right: 15px;"><?php echo number_format($ttc,0,',',' ') ?></td>
      </tr>
    </tbody>
  </table>

  <table style="margin-top: 30px; margin-left:0px;" class="border">
    <thead>
      <tr>
        <th style="width: 0%;"></th>
        <th style="width: 0%; text-align: left;"></th>
        <th style="width: 10%;"></th>
        <th style="width: 90%; padding-right: 15px;"></th>
      </tr>
    </thead>

    <tbody><?php

      if ($tot_Rest<=0) {?>
        <tr style="margin-top: 10px; width: 100%">
          <td></td>
          <td></td>
          <td colspan="2" rowspan="4" style="padding-right: 20px; text-align: right; font-size:35px;"><?php echo"Montant payé : ".number_format($montantverse,0,',',' ');?><br/>
          </td>
        </tr><?php
      }else{?>
        <tr style="margin-top: 10px; width: 100%">
          <td></td>
          <td></td>
          <td colspan="2" rowspan="4" style="padding-right: 20px; text-align: right; font-size:35px;"><?php echo"Montant payé : ".number_format($montantverse,0,',',' ');?><br/>
              <?php echo "Reste à payer: ".number_format($payement['reste'],0,',',' '); ?>
          </td>
        </tr><?php
      }?>

      <tr>
        <td colspan="4" style="font-size:40px;"><?php 

          if (!empty($payement['coment'])) {?>

            ==============Commentaires===============================<br><?=ucfirst(strtolower($payement['coment']));?><?php
          }?>
        </td>
      </tr>

      <tr>

        <td colspan="4" style="font-size:30px; padding-top: 50px; font-style: italic;">

          ***Le <?=$adress['nom_mag']. " vous souhaite une excellente Soirée***"; ?>
        </td>
      </tr>

      <tr>

        <td colspan="4" style="font-size:30px; padding-top: 20px; font-style: italic;">

          *************************A Bientôt *****************************
        </td>
      </tr>

    </tbody>
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