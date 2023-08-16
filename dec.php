<?php require 'header3.php';?><?php 

if (!empty($_SESSION['pseudo'])) {


  if ($_SESSION['level']>=3) {

    if (isset($_GET['deleteret'])) {

      $DB->delete("DELETE from decaissement where numdec='{$_GET['deleteret']}'");

      $DB->delete("DELETE from bulletin where numero='{$_GET['deleteret']}'");

      $DB->delete("DELETE from banque where numero='{$_GET['deleteret']}'");?>

      <div class="alert alert-success">Suppression reussi!!</div><?php 
    }

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

      $datenormale='à la date du '.(new DateTime($dates))->format('d/m/Y');
    }

    if (isset($_POST['clientliv'])) {
      $_SESSION['clientliv']=$_POST['clientliv'];
    }

    require 'navdec.php'; ?>

    <div class="container-fluid"><?php 

      if (isset($_GET['ajout']) or isset($_GET['searchclient']) ) {

        if (isset($_GET['searchclient']) ) {

            $_SESSION['searchclient']=$_GET['searchclient'];
        }?>

        <form method="post"  action="dec.php" target="_blank">

          <table class="table table-hover table-bordered table-striped table-responsive text-center">

            <thead>
              <tr>
                <th colspan="6" class="text-center bg-info">Effectuez un Décaissement</th>  
              </tr>

              <tr>
                <th>Montant décaissé</th>
                <th>Payement</th>
                <th>Compte à Prélever</th>
                <th>Destinataire</th>
                <th>commentaires</th>
                <th>Date dec</th>              
              </tr>

            </thead>
                    
            <tbody>

              <td><input class="form-control" type="number" min="0"  name="montant" required=""></td>
                        

              <td><select class="form-select" name="mode_payement" required="" >
                  <option value=""></option><?php 
                  foreach ($panier->modep as $value) {?>
                      <option value="<?=$value;?>"><?=$value;?></option><?php 
                  }?></select>
              </td>

              <td><select class="form-select" name="compte" required="">
                  <option></option><?php
                      $type='Banque';

                      foreach($panier->nomBanque() as $product){?>

                          <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                      }?>
                  </select>               
              </td>

              <td><select class="form-select" class="nomstock" type="text" name="client" required="">
                <option></option><?php

                foreach($panier->client() as $product){?>

                    <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
                }?></select>
              </td>

              <td><input class="form-control" type="text" name="coment" required=""></td>

              <td><input class="form-control" type="date" name="datedep"></td>                              

            </tbody>

          </table><?php

          if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

              <input class="btn btn-primary" id="button"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();"><?php

          }else{?>

              <div class="alert alert-danger"> CAISSE CLOTUREE OU LA LICENCE EST EXPIREE </div><?php

          }?>
                
        </form><?php 
      }


      if (isset($_POST['montant'])){

        if ($_POST['montant']<0){?>

            <div class="alert alert-warning">FORMAT INCORRECT</div><?php

        }elseif ($_POST['montant']>$panier->montantCompte($_POST['compte'])) {?>

            <div class="alert alert-warning">Echec montant decaissé est > au montant disponible</div><?php

        }else{                          

          if ($_POST['montant']!="") {

              $numdec = $DB->querys('SELECT max(id) AS id FROM decaissement ');
              $numdec=$numdec['id']+1;

              $heure=date("H:i:s");
              
              if (empty($_POST['datedep'])) {

                $dateop=$_SESSION['datev'].' '.$heure;
                
              }else{

                $dateop=$_POST['datedep'].' '.$heure;
              }

              if (empty($dateop)) {

                $DB->insert('INSERT INTO decaissement (numdec, montant, payement, coment, client, cprelever, date_payement) VALUES(?, ?, ?, ?, ?, ?,  now())',array('ret'.$numdec, $_POST['montant'], $_POST['mode_payement'], $_POST['coment'], $_POST['client'], $_POST['compte']));

                $client=$DB->querys("SELECT id, type from client where id='{$_POST['client']}'");

                if ($client['type']!='Banque') {

                    $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['client'], -$_POST['montant'], "Retrait", 'ret'.$numdec));
                }

                $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], -$_POST['montant'], "Retrait (".$_POST['coment'].')', 'ret'.$numdec));

                if ($client['type']=='Banque') {

                  $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($client['id'], $_POST['montant'], "Depot(".$_POST['coment'].')', 'ret'.$numdec));
                      }
                  }else{
                    $DB->insert('INSERT INTO decaissement (numdec, montant, payement, coment, client, cprelever, date_payement) VALUES(?, ?, ?, ?, ?, ?,  ?)',array('ret'.$numdec, $_POST['montant'], $_POST['mode_payement'], $_POST['coment'], $_POST['client'], $_POST['compte'], $dateop));

                    $client=$DB->querys("SELECT id, type from client where id='{$_POST['client']}'");

                    if ($client['type']!='Banque') {

                      $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['client'], -$_POST['montant'], "Retrait", 'ret'.$numdec, $dateop));
                    }

                    $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], -$_POST['montant'], "Retrait (".$_POST['coment'].')', 'ret'.$numdec, $dateop));

                    if ($client['type']=='Banque') {

                      $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($client['id'], $_POST['montant'], "Depot(".$_POST['coment'].')', 'ret'.$numdec, $dateop));
                    }
                  }

                } else{?>

                  <div class="alert">Saisissez tous les champs vides</div><?php

                }

            }

        }else{

        }



        if (!isset($_GET['ajout'])) {?>

          <table class="table table-hover table-bordered table-striped table-responsive">

            <thead>

              <tr>
                <form method="POST" action="dec.php" id="suitec" name="termc">

                  <th colspan="3" >
                    <div class="container">
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

                  <form method="POST" action="dec.php">

                    <th colspan="1">

                      <select name="clientliv" onchange="this.form.submit()" class="form-select"><?php

                        if (isset($_POST['clientliv'])) {?>

                          <option value="<?=$_POST['clientliv'];?>"><?=ucwords($panier->nomClient($_POST['clientliv']));?></option><?php

                        }else{?>
                          <option></option><?php
                        }

                        foreach($panier->client() as $product){?>

                          <option value="<?=$product->id;?>"><?=ucwords($product->nom_client);?></option><?php
                        }?>
                      </select>
                    </th>
                  </form>

                  <th colspan="3" class="text-center"><?="Décaissements " .$datenormale ?> <a class="btn btn-warning" href="dec.php?ajout">Effectuer un Retraît</a></th>
                </tr>

                <tr>
                  <th>N°</th>
                  <th>Montant</th>
                  <th >Motif</th>
                  <th>Client</th>
                  <th>Date</th>
                  <th>Justif</th>
                  <th></th>
                </tr>

              </thead>

              <tbody><?php 
                $cumulmontant=0;

                if (isset($_POST['j1'])) {

                  $products= $DB->query('SELECT decaissement.id as id, client.id as idc, numdec, client.nom_client as client, payement as type, montant, coment, payement, DATE_FORMAT(date_payement, \'%d/%m/%Y\')AS DateTemps FROM decaissement inner join client on client.id=decaissement.client  WHERE DATE_FORMAT(date_payement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_payement, \'%Y%m%d\')<= :date2 order by(client.nom_client) LIMIT 50', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));

                }elseif (isset($_POST['clientliv'])) {

                  $products= $DB->query('SELECT decaissement.id as id, client.id as idc, numdec, client.nom_client as client, payement as type, montant, coment, payement, DATE_FORMAT(date_payement, \'%d/%m/%Y\')AS DateTemps FROM decaissement inner join client on client.id=decaissement.client  WHERE decaissement.client = :client order by(client.nom_client) LIMIT 50', array('client' => $_POST['clientliv']));

                }else{

                  $products= $DB->query('SELECT decaissement.id as id, client.id as idc, numdec, client.nom_client as client, payement as type, montant, coment, payement, DATE_FORMAT(date_payement, \'%d/%m/%Y\')AS DateTemps FROM decaissement inner join client on client.id=decaissement.client  WHERE YEAR(date_payement) = :annee order by(client.nom_client) LIMIT 50', array('annee' => date('Y')));
                }

                foreach ($products as $product ){

                  $cumulmontant+=$product->montant;?>

                  <tr>

                      <td style="text-align: center;"><?= $product->numdec; ?></td>
                      
                      <td style="text-align: right; padding-right: 20px;"><?= number_format($product->montant,0,',',' '); ?></td>

                      <td style="font-size:14px;"><?= Ucwords($product->coment); ?></td>

                      <td style="font-size:14px;"><?= $product->client; ?></td>          
                      <td><?= $product->DateTemps; ?></td>

                      <td style="text-align: center">

                          <a href="printdecaissement.php?numdec=<?=$product->id;?>&idc=<?=$product->idc;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a>
                        </td>

                      <td><a class="btn btn-danger" onclick="return alerteS();" href="dec.php?deleteret=<?=$product->numdec;?>">Supprimer</a></td>

                  </tr><?php

                }?>

              </tbody>

              <tfoot>
                  <tr>
                      <th></th>
                      <th style="text-align: right; padding-right: 20px;"><?= number_format($cumulmontant,0,',',' ');?></th>
                  </tr>
              </tfoot>

            </table><?php 
        }

    }else{

        echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
    }

}else{

    header('Location: deconnexion.php');


  }?>
  
</body>
</html><?php

require 'footer.php';?>

<script type="text/javascript">
    function alerteS(){
        return(confirm('Valider la suppression'));
    }

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>