<?php
require 'header3.php';?>

<style type="text/css">

.search{
  display: flex;
  flex-wrap: nowrap;
}
  .history{
    width: 50%;
    margin-top: 30px;
  }
 .ticket{
    margin-right: 10px;
    width: 95%;
  }

  table.border {
    width: 100%;
    color: #717375;
    font-family: helvetica;
    line-height: 10mm;
    border-collapse: collapse;
  }
  
  .border th {
    border: 1px solid black;
    padding: 0px;
    font-weight: bold;
    font-size: 14px;
    color: black;
    background: white;
    text-align: right;
    height: 30px; }
  .border td {
    line-height: 15mm;
    border: 1px solid black;    
    font-size: 14px;
    color: black;
    background: white;
    text-align: center;
    height: 18px;
  }
  .footer{
    font-size: 14px;
    font-style: italic;
  }
</style><?php 

require 'entetelivraisonachat.php';

if (isset($_GET['livraison'])) {

  $Num_cmd=$_GET['livraison'];
  $_SESSION['livraison']=$Num_cmd;
}else{

  $Num_cmd=$_SESSION['livraison'];

}

$_SESSION['rechercher']=$Num_cmd;

  $payement=$DB->querys('SELECT num_cmd, montantpaye, remise, reste, etat, num_client, DATE_FORMAT(date_cmd, \'%d/%m/%Y \à %H:%i:%s\')AS DateTemps, vendeur FROM payement WHERE num_cmd= ?', array($Num_cmd));

  //$frais=$DB->querys('SELECT numcmd, montant, motif  FROM fraisup WHERE numcmd= ?', array($Num_cmd));

  $_SESSION['reclient']=$payement['num_client'];
  $_SESSION['nameclient']=$payement['num_client'];

  if (isset($_POST['livrer'])) {

    $numcmd=$panier->h($_POST['numcmd']);

    $DB->insert("UPDATE payement SET etatliv=? WHERE num_cmd=? ", array('livre', $numcmd));

    $DB->insert("INSERT INTO livraison (numcmdliv, id_clientliv, livreur, dateliv) VALUES(?, ?, ?, now())", array($numcmd, $_SESSION['reclient'], $_SESSION['idpseudo']));

  }

  if (isset($_POST['annuler'])) {

    $numcmd=$panier->h($_POST['numcmd']);

    $DB->insert("UPDATE payement SET etatliv=? WHERE num_cmd=? ", array('nonlivre', $numcmd));

    $DB->delete("DELETE FROM livraison where numcmdliv='{$numcmd}'");

    //$DB->insert("INSERT INTO livraison (numcmdliv, id_clientliv, livreur, dateliv) VALUES(?, ?, ?, now())", array($numcmd, $_SESSION['reclient'], $_SESSION['idpseudo']));
  }?>

  <div class="container-fluid">

    <div class="row">

      <div class="col-sm-12 col-md-4">

        <table class="table table-hover table-bordered table-striped table-responsive">

          <tr>
            <td><strong><?php echo "Facture N°: " .$Num_cmd; ?></strong></td>
          </tr>

          <tr>
            <td><?php echo "Date:  " .$payement['DateTemps']; ?></td>
          </tr>

          <tr>
            <td><?php echo "Vendeur:  " .$panier->nomPersonnel($payement['vendeur'])[0]; ?></td>  
          </tr>

          <tr>
            <td><?=$panier->adClient($_SESSION['reclient'])[0]; ?></td>
          </tr>
          
          <tr>
            <td><?='Téléphone: '.$panier->adClient($_SESSION['reclient'])[1]; ?></td>
          </tr>

          <tr>
            <td><?='Adresse: '.$panier->adClient($_SESSION['reclient'])[2]; ?></td>
          </tr>

        </table>
      </div>

      <div class="col">

        <table class="table table-hover table-bordered table-striped table-responsive">

          <thead>

            <tr>
              <th style="text-align: center;">Désignation</th>
              <th style="text-align: center;">Qtité cmd</th>
              <th>Prix de Vente</th>
              <th>Etat</th>
            </tr>

          </thead>

          <form method="Post" action="livraison.php">

            <tbody><?php

              $total=0;
              $totqtiteliv=0;

              $prodpayement=$DB->querys('SELECT * FROM payement WHERE num_cmd= ?', array($Num_cmd));

              if ($prodpayement['etatliv']=='livre') {
                $reste=0;
                $etat='livraison terminée';
              }else{
                $etat='non livré';
                $reste=1;
              }

               $products=$DB->query('SELECT stock.id as id, commande.quantity as quantity, commande.prix_vente as prix_vente, nom as designation, num_cmd, type FROM commande inner join stock on stock.id=commande.id_produit WHERE num_cmd= ? order by(commande.id)', array($Num_cmd));
              $totqtite=0;
              foreach ($products as $product){

                $qtitecmd=$product->quantity;
                $totqtite+=$qtitecmd;?>         

                <tr>           

                  <td style="text-align:left"><?=ucwords(strtolower($product->designation)); ?></td>

                  <td style="text-align: center;"><?= $product->quantity; ?></td>

                  <td style="text-align: right;"><?= $product->prix_vente; ?></td>

                  <input type="hidden" name="id" value="<?=$product->id;?>">

                  <input type="hidden" name="numcmd" value="<?=$product->num_cmd;?>">

                  <input type="hidden" name="type" value="<?=$product->type;?>">

                  <td style="text-align:center; color: green; font-size:12px;"><?php if ($reste==0) {echo $etat;}else{echo $etat;};?></td> 
                </tr><?php
              }?>

              <tr><?php 
                if ($prodpayement['etatliv']!='livre') {?>
                  <td><input class="btn btn-success" id="button" type="submit" name="livrer" value="Livrer" onclick="return alerteL();"></td><?php
                }else{?>
                  <td><input class="btn btn-danger" id="button" type="submit" name="annuler" value="Annuler" onclick="return alerteS();" style="background-color: red;" ></td><?php
                }?>
              </tr> 
            </tbody>
          </form>

        </table>
      </div>
    </div><?php 
    require 'footer.php';?>

<script type="text/javascript">
    function alerteS(){
        return(confirm("Annuler la livraison"));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteL(){
        return(confirm('Confirmer la livraison'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>