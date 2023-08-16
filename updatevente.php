<?php require 'header3.php';

if (!empty($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

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

  if (isset($_POST['clientliv'])) {
    $_SESSION['clientliv']=$_POST['clientliv'];
  }

  require 'headercompta.php';?>

  <div class="container-fluid">

    <div class="row">

      <div class="col" style="overflow: auto;">


        <table class="table table-hover table-bordered table-striped table-responsive">

          <thead>
            <tr>
              <th colspan="10" class="text-center"><?="Historique des modifications" ?></th>
            </tr>

            <tr>
              <th>N°</th>
              <th>N°Cmd</th>
              <th>Date Cmd</th>
              <th>Total cmd</th>
              <th>Montant payé</th>
              <th>Remise</th>          
              <th>Client</th>
              <th>N° Table</th>
              <th>Supprimé par</th>
              <th>Date Modif</th>
            </tr>
          </thead>

          <tbody><?php 

            $products =$DB->query("SELECT *FROM venteupdate order by(num_cmd)");
            foreach ($products as $key =>$product ){?>

              <tr>
                <td style="text-align:center"><?= $key+1; ?></td>
               <td style="text-align:center"><?= $product->num_cmd; ?></td>
                <td><?=(new dateTime($product->date_cmd))->format("d/m/Y h:m") ;?></td>
                <td style="text-align: right"  ><?= number_format($product->Total,0,',',' '); ?></td>

                <td style="text-align: right"  ><?= number_format($product->montantpaye,0,',',' '); ?></td>

                <td style="text-align: right"  ><?= number_format($product->remise,0,',',' '); ?></td>
                
                <td><?=$panier->nomClient($product->num_client); ?></td>

                <td><?= $product->idtable; ?></td>

                <td><?=$panier->nomPersonnel($product->idpersonnel)[1]; ?></td>

                <td><?=(new dateTime($product->dateop))->format("d/m/Y h:m") ;?></td>
              </tr><?php 

            }?>

          </tbody>

        </table>
      </div>
    </div>
  </div><?php 
}else{
  header("Location: form_connexion.php");
}