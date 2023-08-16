<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

  if ($_SESSION['level']>=3) {

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

    <div class="container-fluid"><?php 

      if (isset($_GET['numcmdpaye'])) {

        if (isset($_GET['numcmdpaye'])) {
          $_SESSION['numchequech']=$_GET['numcmdpaye'];
          $_SESSION['montantch']=$_GET['montant'];
        }?>

        <div class="col-sm-12 col-md-8">

          <form method="post"  action="paiecreditclient.php" target="_blank" >

            <table class="table table-hover table-bordered table-striped table-responsive text-center">

              <thead>
                <tr>
                  <th class="text-center bg-info" colspan="4">Paiement de la commande N°: <?=$_SESSION['numchequech'];?></th>  
                </tr>

                <tr>
                  <th>Montant à Payer</th>
                  <th>Type de P</th>
                  <th>Compte de dépôt</th>
                  <th>Date</th>              
                </tr>

                </thead>
                  
                <tbody>

                  <td style="text-align:center; font-size: 22px;"><?=number_format($_SESSION['montantch'],0,',',' ');?>
                    <input type="hidden"   name="numcmd" value="<?=$_GET['numcmdpaye'];?>">
                    <input type="hidden"   name="montant" value="<?=$_SESSION['montantch'];?>">
                    <input type="hidden"   name="client" value="<?=$_GET['client'];?>">
                  </td>

                  <td><select class="form-select" name="mode_payement" required="" >
                    <option value=""></option><?php 
                    foreach ($panier->modep as $value) {?>
                        <option value="<?=$value;?>"><?=$value;?></option><?php 
                    }?></select>
                  </td>

                  <td style="width:20%;">
                    <select class="form-select"  name="compte" required="" ><?php
                      $type='Banque';

                      foreach($panier->nomBanqueCaisse() as $product){?>

                        <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                      }?>
                      </select>
                  </td>

                  <td><input class="form-control" type="date" name="datedep"></td>                              

                </tbody>

              </table><?php

              if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

                <input class="btn btn-primary" id="button"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();"><?php

              }else{?>

                <div class="alertes"> CAISSE CLOTUREE OU LA LICENCE EST EXPIREE </div><?php

              }?>
    
          </form>
        </div><?php
      }

      if (isset($_POST["valid"])) {
        $numcmd=$_POST['numcmd'];
        $montant=$_POST['montant'];
        $client=$_POST['client'];
        $numcmd=$_POST['numcmd'];
        $dateop=$_POST['datedep'];
        $motif="paiement credit";
        $mode=$_POST['mode_payement'];
        $compte=$_POST['compte'];

        $maximum = $DB->querys('SELECT max(id) AS max_id FROM versement ');

        $max=$maximum['max_id']+1; 

        $heure=date("H:i:s");
              
        if (empty($_POST['datedep'])) {

          $dateop=$_SESSION['datev'].' '.$heure;
          
        }else{

          $dateop=$_POST['datedep'].' '.$heure;
        }     

        if (empty($dateop)) {

          //$DB->insert('INSERT INTO versement (numcmd, nom_client, montant, motif, type_versement, comptedep, date_versement) VALUES(?, ?, ?, ?, ?, ?, now())', array('dep'.$max, $client, $montant, $motif, $mode, $compte));

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($client, $montant, $motif, $numcmd));

        }else{

          //$DB->insert('INSERT INTO versement (numcmd, nom_client, montant, motif, type_versement, comptedep, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?)', array('dep'.$max, $client, $montant, $motif, $mode, $compte, $dateop));

          $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($client, $montant, $motif, $numcmd, $dateop));
        }

        if (empty($dateop)) {

          $DB->insert('INSERT INTO banque (id_banque, montant, provenance, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?, now())', array($compte, $montant, 'credit', "Depot(".$motif.')', 'vente'.$numcmd));

        }else{

          $DB->insert('INSERT INTO banque (id_banque, montant, provenance, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?, ?)', array($compte, $montant, 'credit', "Depot(".$motif.')', 'vente'.$numcmd, $dateop));
        }

        $prodpayementcred = $DB->querys("SELECT * FROM payement where num_cmd='{$numcmd}' ");
        $totalpaye=$prodpayementcred['montantpaye']+$montant;
        $DB->insert("UPDATE payement SET etat = ?, mode_payement=?, date_regul=?, reste=?, montantpaye=? WHERE num_cmd = ?", array('totalite', $mode, $dateop, 0, $totalpaye, $numcmd));

        header("Location: ticket_pdf.php?numcmd=".$numcmd);
      }?>

      <div class="col" style="overflow: auto;">

        <table class="table table-hover table-bordered table-striped table-responsive text-center">
      
          <thead>

          <tr>
            <th class="text-center bg-info" colspan="7"><?="Liste des Facturations non Payées " .$datenormale ?></th>
          </tr>

          <tr>
            <form method="POST" action="paiecreditclient.php" id="suitec" name="termc">

              <th colspan="3" ><div class="container">
                <div class="row">
                  <div class="col"><?php

                    if (isset($_POST['j1'])) {?>

                      <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                    }else{?>

                      <input class="form-control" style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                    }?>
                  </div>

                  <div class="col"><?php 

                    if (isset($_POST['j2'])) {?>

                      <input class="form-control" style="width:150px;" type = "date" name = "j2" value="<?=$_POST['j2'];?>" onchange="this.form.submit()"><?php

                    }else{?>

                      <input class="form-control" style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                    }?>
                  </div>
                </div>
              </th>
            </form>

            <form method="GET" action="paiecreditclient.php">

              <th colspan="4">

                <input style="width:65%;" class="form-control" id="search-user" type="text" name="clientsearch" placeholder="rechercher un client" />
                  <div style="color:white; background-color: grey; font-size: 16px;" id="result-search"></div>
              </th>
            </form>          
          </tr>

          <tr>
            <th>N°</th>
            <th>N° Cmd</th>
            <th>Date Cmd</th>
            <th>Montant</th>
            <th>Client</th>
            <th colspan="2">Actions</th>          </tr>
        </thead>

        <tbody><?php

          $etat='credit';

          if (isset($_POST['j1'])) {

            $products=$DB->query("SELECT *FROM payement where DATE_FORMAT(date_cmd, \"%Y%m%d\") >='{$_SESSION['date1']}' and DATE_FORMAT(date_cmd, \"%Y%m%d\") <= '{$_SESSION['date2']}' and etat='{$etat}'");          

          }elseif (isset($_GET['clientsearch'])) {

            $products=$DB->query("SELECT *FROM payement inner join client on client.id=num_client where (num_client='{$_GET['clientsearch']}' or telephone='{$_GET['clientsearch']}') and etat='{$etat}' ");         

          }else{

            $products =$DB->query("SELECT *FROM payement WHERE  etat='{$etat}'");
            

          }

          $cumulmontanremp=0;
          $cumulmontantotp=0;
          $cumulmontanrestp=0;

          foreach ($products as $key=> $product ){
            $cumulmontantotp+=$product->Total-$product->remise;
            $montantp=$product->Total-$product->remise;?>

            <tr>
              <td style="text-align:center;"><?=$key+1;?></td>

              <td><a style="color: red;" href="recherche.php?recreditc=<?=$product->num_cmd;?>"><?= $product->num_cmd; ?></a></td>

              <td style="text-align:center;"><?= $panier->formatDate($product->date_cmd); ?></td>

              <td style="text-align: right"><?= number_format(($product->Total-$product->remise),0,',',' '); ?></td>

              <td><?= $panier->nomClient($product->num_client); ?></td>

              <td><a class="btn btn-warning" href="modifventeprod.php?numcmdpaye=<?=$product->num_cmd;?>&numticketpaye=<?=$product->num_ticket;?>&surplace">Modifier</a></td>

              <td><a class="btn btn-success" href="paiecreditclient.php?numcmdpaye=<?=$product->num_cmd;?>&montant=<?=$montantp;?>&client=<?=$product->num_client;?>">Payer</a></td>
            </tr><?php 
          } ?>   
        </tbody>

      <tfoot>
        <tr>
          <th colspan="3"></th>
          <th style="text-align: right;"><?= number_format($cumulmontantotp,0,',',' ');?></th>
        </tr>
      </tfoot>
    </table><?php 

  }else{

    echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

  }
}

require 'footer.php';?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
  $(document).ready(function(){
      $('#search-user').keyup(function(){
          $('#result-search').html("");

          var utilisateur = $(this).val();

          if (utilisateur!='') {
              $.ajax({
                  type: 'GET',
                  url: 'recherche_utilisateur.php?clientfact',
                  data: 'user=' + encodeURIComponent(utilisateur),
                  success: function(data){
                      if(data != ""){
                        $('#result-search').append(data);
                      }else{
                        document.getElementById('result-search').innerHTML = "<div style='font-size: 20px; text-align: center; margin-top: 10px'>Aucun utilisateur</div>"
                      }
                  }
              })
          }
    
      });
  });
</script>

<script type="text/javascript">
  function alerteS(){
    return(confirm('Valider la suppression'));
  }

  function alerteM(){
    return(confirm('Voulez-vous vraiment modifier cette vente?'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>