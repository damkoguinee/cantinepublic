<?php require '_header.php';?>

<style type="text/css">
  body{
    margin: 0px;
    width: 100%;
    height:30%;
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
    font-size: 30px;
    color: black;
    background: white;
    text-align: right; }
  .border td {
    line-height: 10mm;
    border: 0px solid #CFD1D2;    
    font-size: 35px;
    color: black;
    background: white;
    text-align: center;}
  .footer{
    font-size: 30px;
    font-style: italic;
  }
</style><?php

  $products = $DB->query('SELECT * FROM adresse ');
  foreach ( $products as $adress ):?>

  <?php endforeach ?><?php
  $total = 0;
  $total_tva = 0; ?>

  <div class="ticket">
    <table style="margin:0px; text-align: center;color: black; background: white;" >
      <tr>
        <th style="font-weight: bold; font-size: 30px; padding: 5px"><?php echo $adress->nom_mag; ?></th>
      </tr>
      <tr>      
        <td style="font-size: 30px;">
          <?php echo nl2br($adress->adresse); ?><br /><br /> 
        </td>
      </tr>

      <tr>      
        <td style="font-size: 30px;">
          <?php echo nl2br($adress->type_mag); ?><br /> <br />   
        </td>
      </tr>

    </table><?php

    $productnum = $DB->querys("SELECT date_cmd, max(num_cmd) as num, montantpaye, remise, reste FROM payement ");
    $Num_cmd=$productnum['num'];

    $products= $DB->query('SELECT num_cmd, num_ticket, montantpaye, remise, reste, etat, client, mode_payement, DATE_FORMAT(date_cmd, \'%d/%m/%Y \à %H:%i:%s\')AS DateTemps, position, vendeur FROM payement WHERE num_cmd=:Num', array('Num'=>$Num_cmd));

    foreach ($products as $payement): ?>

      <table style="margin:0px; font-size: 28px;color: black; background: white;" >
        
        <tr>
          <td>
            <?php echo "Commande: " .$Num_cmd; ?>       
          </td>

          <td style="border: 2px solid #CFD1D2; font-size: 35px;">
            <strong><?php echo "Ticket N°: " .$payement->num_ticket; ?></strong>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo "Client:  " .$payement->client; ?>     
          </td>
           <td style="font-size: 35px;">
            <strong><?php echo "" .$payement->position; ?></strong>     
          </td> 
        </tr>
        <tr>
          <td>
            <?php echo "Payement:  " .$payement->mode_payement; ?>s     
          </td>
        </tr>
        <tr> 
          <td>
            <?php echo "Edité le   " .$payement->DateTemps; ?>     
          </td> 
        </tr>
        <tr>
          <td>
            <?php echo "Vendeur: " .$payement->vendeur;; ?>       
          </td>
        </tr>
        
      </table>

      <table style="margin-top: 30px; margin-left:0px;" class="border">
        <tbody>
          <tr>
            <th style="width: 7%;"></th>
            <th style="width: 4%; text-align: left;"></th>
            <th style="width: 60%;"></th>
            <th style="width: 29%; padding-right: 10px;"></th>
          </tr>
        </tbody>
      </table>
      
      <table style="margin-top: 1px; margin-left:0px;" class="border">
        <tbody><?php 
        $total=0;
        $productcom= $DB->query('SELECT * FROM commande WHERE num_cmd=:Num', array('Num'=>$Num_cmd));
        foreach ($productcom as $product): ?>
          <tr>
            <td style="width: 7%;border:0px"><?= $product->quantity; ?></td>
            <td style="width: 4%;border:0px; text-align:right"></td>
            <td style="width: 60%;border:0px;text-align:left"><?= $product->nom; ?></td>
            <td style="width: 29%;border:0px; text-align:right; padding-right: 10px;"><?= number_format($product->prix_vente*$product->quantity,2,',',' '); ?></td>
            <?php
            $price=($product->prix_vente*$product->quantity);         
            $total += $price;?>
          </tr>
        <?php endforeach ?><?php
        $montantverse=$payement->montantpaye;
        $Remise=$payement->remise;
        $ttc = $total-$Remise; 
        $tot_Rest = $payement->reste; 
        ?>
      <?php endforeach ?>

      <tr>
        <td colspan="4" style="border:0px; padding-top: 50px;" class="space"></td>
      </tr>
      <tr>
        <td colspan="2" rowspan="4" style="padding: 1px; text-align: left; font-size:25px;"></td>
      </tr>

      <tr>
        <td style="text-align: right;" class="no-border">Total </td>
        <td style="text-align:right; padding-right: 5px;"><?php echo number_format($total,2,',',' ') ?></td>
      </tr>

      <tr>
        <td style="text-align: right;" class="no-border">Remise</td>               
        <td style="text-align:right; padding-right: 5px;"><?php echo number_format($Remise,2,',',' ') ?></td>        
      </tr>

      <tr>
        <td style="text-align: right; margin-bottom: 5px" class="no-border">Total Net </td>
        <td style="text-align:right; padding-right: 5px;"><?php echo number_format($ttc,2,',',' ') ?></td>
      </tr>
    </tbody>
  </table>

  <table style="margin-top: 30px; margin-left:0px;" class="border">
    <thead>
      <tr>
        <th style="width: 0%;"></th>
        <th style="width: 0%; text-align: left;"></th>
        <th style="width: 10%;"></th>
        <th style="width: 90%; padding-right: 10px;"></th>
      </tr>
    </thead>

    <tbody><?php

      if ($tot_Rest<=0) {?>
        <tr style="margin-top: 10px; width: 100%">
          <td></td>
          <td></td>
          <td colspan="2" rowspan="4" style="padding-right: 5px; text-align: right; font-size:30px;"><?php echo"Montant payé : ".number_format($montantverse,0,',',' ');?><br/>
            <?php echo "Rendu client: ".number_format(($payement->reste*(-1)),2,',',' '); ?>
          </td>
        </tr><?php
      }else{?>
        <tr style="margin-top: 10px; width: 100%">
          <td></td>
          <td></td>
          <td colspan="2" rowspan="4" style="padding-right: 5px; text-align: right; font-size:30px;"><?php echo"Montant payé : ".number_format($montantverse,2,',',' ');?><br/>
              <?php echo "Reste à payer: ".number_format($payement->reste,2,',',' '); ?>
          </td>
        </tr><?php
      }?>

    </tbody>
  </table> 

</div>