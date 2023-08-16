<?php require 'header1.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];
  

  if ($products['level']>=3) {

    if (isset($_GET['deletevers'])) {

      $numero=$_GET['deletevers'];
      $DB->delete('DELETE FROM versement WHERE numcmd = ?', array($numero));

      $DB->delete('DELETE FROM bulletin WHERE numero = ?', array($numero));

      $DB->delete('DELETE FROM banque WHERE numero = ?', array($numero));?>

        <div class="alerteV">LE VERSEMENT A BIEN ETE SUPPRIME</div><?php
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

      $datenormale=(new DateTime($dates))->format('d/m/Y');
    }

    if (isset($_POST['clientliv'])) {
      $_SESSION['clientliv']=$_POST['clientliv'];
    }

    if (isset($_GET['ajout']) or isset($_GET['searchclientvers'])) {

      if (isset($_GET['searchclientvers']) ) {

          $_SESSION['searchclientvers']=$_GET['searchclientvers'];
      }?>

      <form method="post"  action="versement.php">

        <div class="decaissement">

          <div class="box_decaiss">

            <table class="payement" style="width: 90%;">

              <thead>

                <tr>
                  <th class="legende" colspan="6" height="30"><?="Effectuez un versement"; ?></th> 
                </tr>

                <tr>
                  <th>Selectionnez</th>
                  <th>Montant Versé</th>
                  <th>Motif</th>
                  <th>Payement</th> 
                  <th>Compte à Déposer</th>
                  <th>Date de depôt</th>               
                </tr>

              </thead>
                
              <tbody>

                <td><select type="text" name="client">
                  <option></option><?php

                  foreach($panier->client() as $product){?>
                      <option value="<?=$product->id;?>"><?=$product->nom_client;?></option><?php
                  }?></select>
                </td>

                <td><input type="number"   name="montant" min="0" required="" style="font-size: 30px;"></td>

                <td><input type="text"   name="motif" required=""></td>

                <td><select name="mode_payement" required="" >
                  <option value=""></option><?php 
                  foreach ($panier->modep as $value) {?>
                      <option value="<?=$value;?>"><?=$value;?></option><?php 
                  }?></select>
                </td>

                <td><select  name="compte" required="">
                  <option></option><?php
                      $type='Banque';

                      foreach($panier->nomBanque() as $product){?>

                          <option value="<?=$product->id;?>"><?=strtoupper($product->nomb);?></option><?php
                      }?>
                  </select>               
                </td>

                <td><input type="date" name="datedep"></td>               

              </tbody>

            </table><?php
            
            if (empty($panier->totalsaisie()) AND $panier->licence()!="expiree") {?>

              <input id="button"  type="submit" name="valid" value="VALIDER" onclick="return alerteV();"><?php

            }else{?>

              <div class="alertes"> CAISSE CLOTUREE OU LA LICENCE EST EXPIREE </div><?php

            }?>               

          </div>
        </form> <?php
      }

      if (isset($_POST["client"])) {

        if ($_POST["client"]=='' OR $_POST["montant"]=='') {

          echo "FORMAT INCORRECT";

        }else{

          $maximum = $DB->querys('SELECT max(id) AS max_id FROM versement ');

          $max=$maximum['max_id']+1;
          $dateop=$_POST['datedep'];

          if (empty($_POST['datedep'])) {
      
            $dateop=$_SESSION['datev'].' '.$heure;
            
          }else{

            $dateop=$_POST['datedep'].' '.$heure;
          }

          if (empty($dateop)) {

            $DB->insert('INSERT INTO versement (numcmd, nom_client, montant, motif, type_versement, comptedep, date_versement) VALUES(?, ?, ?, ?, ?, ?, now())', array('dep'.$max, $_POST['client'], $_POST['montant'], $_POST['motif'], $_POST['mode_payement'], $_POST['compte']));

          }else{

            $DB->insert('INSERT INTO versement (numcmd, nom_client, montant, motif, type_versement, comptedep, date_versement) VALUES(?, ?, ?, ?, ?, ?, ?)', array('dep'.$max, $_POST['client'], $_POST['montant'], $_POST['motif'], $_POST['mode_payement'], $_POST['compte'], $dateop));
          }

          

          $client=$DB->querys("SELECT id, type from client where id='{$_POST['client']}'");

          if (empty($dateop)) {

            if ($client['type']!='Banque') {

              $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['client'], $_POST['montant'], $_POST['motif'], 'dep'.$max));
            }

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($_POST['compte'], $_POST['montant'], "Depot(".$_POST['motif'].')', 'dep'.$max));
          

            if ($client['type']=='Banque') {

              $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, now())', array($client['id'], -$_POST['montant'], "Retrait(".$_POST['motif'].')', 'dep'.$max));
            }

          }else{

            if ($client['type']!='Banque') {

              $DB->insert('INSERT INTO bulletin (nom_client, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['client'], $_POST['montant'], $_POST['motif'], 'dep'.$max, $dateop));
            }

            $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($_POST['compte'], $_POST['montant'], "Depot(".$_POST['motif'].')', 'dep'.$max, $dateop));
          

            if ($client['type']=='Banque') {

              $DB->insert('INSERT INTO banque (id_banque, montant, libelles, numero, date_versement) VALUES(?, ?, ?, ?, ?)', array($client['id'], -$_POST['montant'], "Retrait(".$_POST['motif'].')', 'dep'.$max, $dateop));
            }
          }

          if (isset($_POST["valid"])) {
                  
            $_SESSION['reclient']=$_POST['client'];
            $_SESSION['nameclient']=$_POST['client'];

            //header("Location:printversement.php");
      
          }

        }

      }else{

        
      }

      if (!isset($_GET['ajout']) ) {?>  

        <table class="payement">

          <thead>

            <tr>
              <form method="POST" action="versement.php" id="suitec" name="termc">

              <th colspan="2" ><?php

                if (isset($_POST['j1'])) {?>

                  <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()" value="<?=$_POST['j1'];?>"><?php

                }else{?>

                  <input style="width:150px;" type = "date" name = "j1" onchange="this.form.submit()"><?php

                }

                if (isset($_POST['j2'])) {?>

                  <input style="width:150px;" type = "date" name = "j2" value="<?=$_POST['j2'];?>" onchange="this.form.submit()"><?php

                }else{?>

                  <input style="width:150px;" type = "date" name = "j2" onchange="this.form.submit()"><?php

                }?>
              </th>
            </form>

            <form method="POST" action="versement.php">

              <th colspan="2">

                <select name="clientliv" onchange="this.form.submit()" style="width: 300px;"><?php

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

              <th colspan="4" height="30"><?="Liste des dépôts " .$datenormale ?> <a href="versement.php?ajout">Effectuer un dépôt</a></th>
            </tr>

            <tr>
              <th>N°</th>
              <th>Nom</th>
              <th>Montant Versé</th>
              <th>Motif</th>
              <th>Payement</th>
              <th>Date</th>
              <th>Justif</th>
              <th></th>
            </tr>

          </thead>

          <tbody><?php 
            $cumulmontant=0;

            if (isset($_POST['j1'])) {

              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE DATE_FORMAT(date_versement, \'%Y%m%d\')>= :date1 and DATE_FORMAT(date_versement, \'%Y%m%d\')<= :date2 order by(versement.nom_client) LIMIT 50', array('date1' => $_SESSION['date1'], 'date2' => $_SESSION['date2']));

            }elseif (isset($_POST['clientliv'])) {

              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE versement.nom_client = :client order by(versement.nom_client) LIMIT 50', array('client' => $_POST['clientliv']));

            }else{

              $products= $DB->query('SELECT versement.id as id, client.id as idc, numcmd, client.nom_client as nom_client, montant, motif, type_versement, date_versement FROM versement inner join client on client.id=versement.nom_client  WHERE YEAR(date_versement) = :annee order by(versement.nom_client) LIMIT 50', array('annee' => date('Y')));
            }

            foreach ($products as $product ){

              $cumulmontant+=$product->montant; ?>

              <tr>
                <td style="text-align: center;"><?= $product->numcmd; ?></td>

                <td><?= $product->nom_client; ?></td> 

                <td style="text-align: right; padding-right: 5px;"><?= number_format($product->montant,0,',',' '); ?></td>

                <td><?= $product->motif; ?></td>

                <td><?= $product->type_versement; ?></td>

                <td><?=(new dateTime($product->date_versement))->format("d/m/Y"); ?></td>

                 <td style="text-align: center">

                    <a href="printversement.php?numdec=<?=$product->id;?>&idc=<?=$product->idc;?>" target="_blank"><img  style="height: 20px; width: 20px;" src="css/img/pdf.jpg"></a>
                  </td>

                <td><a href="versement.php?deletevers=<?=$product->numcmd;?>"> <input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white; cursor: pointer;"  type="submit" value="Supprimer" onclick="return alerteS();"></a></td>
              </tr><?php 
            }?>

          </tbody>

          <tfoot>
              <tr>
                <th colspan="2">Totaux</th>
                <th style="text-align: right; padding-right: 5px;"><?= number_format($cumulmontant,0,',',' ');?></th>
              </tr>
          </tfoot>

        </table><?php 
      }

      

    }else{

      echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";

    }

  }else{

  }?>
    
</body>

</html>

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
