<?php
require 'header1.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];
  $products = $DB->querys('SELECT statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

  if ($products['statut']!="vendeur") {?>

    <div class="box_emprunt">

      <div class="reglement">

        <form method="post"  action="emprunt.php" id="naissance" style="width: 92%; margin: 2px;">

          <fieldset><legend>Enregistrer un réglement</legend>

            <ol><?php

              if (isset($_GET['reglement'])) {?>
                <li><label>Saisir N°</label><input type="number" name="num_cmd" value="<?=$_GET['reglement'];?>" min="0"  required=""></li>

                <li><label>Montant à payer</label><input type="text"   name="montant" id="pointeur" required="" min="0"></li><?php

              }else{?>
                <li><label>Saisir N°</label><input type="number" name="num_cmd" min="0"  required=""></li>

                <li><label>Montant à payer</label><input type="text"   name="montant" id="pointeur" required="" min="0"></li><?php
              }?>

              <li><label>Payement</label><select name="mode_payement" required="">
                <option></option>
                <option value="espece">Especes</option>               
                <option value="versement">Versement</option>
                <option value="cheque">Cheque</option>
                <option value="vire Bancaire">Virement</option></select>
              </li>

            </ol>            

             <?php
            
            if (empty($panier->totalsaisie())) {?>

              <fieldset><input type="reset" value="Annuler" name="valid" id="form" style="width:150px;"/><input type="submit" value="Valider" name="savemag" id="form" onclick="return alerteV();" style="margin-left: 20px; width:150px;" /></fieldset><?php

            }else{?>
              <div class="alertes"> La caisse est cloturée </div><?php
            }?>
          </fieldset>

        </form><?php

          if (isset($_POST['num_cmd'])){

            if ($_POST['montant']!=0) {

              $product = $DB->query('SELECT num_cmd FROM payement WHERE num_cmd=:num_cmd', array('num_cmd' => $_POST['num_cmd']));

              if(empty($product)){
                echo "Ce Numero est incorrect";        
              }else{

                $productpay = $DB->query('SELECT * FROM payement  WHERE num_cmd =:Num', array('Num'=>$_POST['num_cmd']));                    

                foreach ($productpay as $product):

                  $reste=$product->reste;

                  $montantpaye=$product->montantpaye+$_POST['montant']; ?>

                <?php endforeach ?><?php

                if ($product->etat=="credit") {

                  if ($_POST['montant']>$reste) {

                    echo "Impossible car le montant saisi est sup au credit";

                  }elseif ($_POST['montant']<0) {

                    echo "format incorrect";

                  }else{

                    $remb="client";
                    $products = $DB->query('SELECT num_cmd FROM historique WHERE num_cmd=:num_cmd', array('num_cmd' => $_POST['num_cmd']));

                    if (!empty($products)) {
                      
                    }else{

                      $DB->insert('INSERT INTO historique(num_cmd, montant, payement, nom_client, date_cmd, remboursement) VALUES(?, ?, ?, ?, ?, ?)', array($product->num_cmd, $product->montantpaye, $product->mode_payement, $product->client, $product->date_cmd, $remb));
                    }
                        
                    $date = date('y-m-d H:i');
                    $reste=$product->reste-$_POST['montant'];

                    $DB->insert('UPDATE payement SET montantpaye= ? , reste=? , mode_payement= ?, date_regul=? WHERE num_cmd = ?', array($montantpaye, $reste, $_POST['mode_payement'], $date, $_POST['num_cmd']));

                    $DB->insert('UPDATE commande SET mode_payement= ?, date_regul=? WHERE num_cmd = ?', array($_POST['mode_payement'], $date, $_POST['num_cmd']));

                    $DB->insert('INSERT INTO historique (num_cmd, montant, payement, nom_client, date_cmd, remboursement, date_regul) VALUES(?, ?, ?, ?, ?, ?, now())', array($product->num_cmd, $_POST['montant'],$_POST['mode_payement'],$product->client, $product->date_cmd, $remb));        

                    $productpaye=$DB->query('SELECT * FROM payement  WHERE num_cmd =:Num', array('Num'=>$_POST['num_cmd']));

                    foreach ($productpaye as $product):

                      if (($product->Total-$product->remise)== $product->montantpaye) {

                        $DB->insert('UPDATE payement SET etat= ? , date_regul=? WHERE num_cmd = ?',array('totalite', $date, $_POST['num_cmd']));

                        $DB->insert('UPDATE commande SET etat= ? , date_regul=? WHERE num_cmd = ?',array('totalite', $date, $_POST['num_cmd']));

                        echo "La commande  N° ". $_POST['num_cmd']." au nom de ".$product->client." est reglée"  ;
                            
                      }else{

                        echo $product->client." a reglé  ". number_format($_POST['montant'],2,',',' ')." GNF sur la commande N° ".$_POST['num_cmd'] ;
                      }?>

                    <?php endforeach ?> <?php

                  }

                }else{

                  echo "La Commande est déjà reglée";

                }

              }

            }else{
              echo "Remplissez tous les Champs vides";
            }
          }else{

              
          }

          $credit_client=0;
          $Etat="credit";

          $productable = $DB->query('SELECT num_cmd, client, Total, remise, reste, montantpaye, DATE_FORMAT(date_cmd, \'%d/%m/%Y \')AS DateTemps FROM payement  WHERE etat=:Etat ORDER BY (date_cmd)DESC', array('Etat'=>$Etat));

          if (empty($productable)) {
            
          }else{?>

            <table style="margin-top: 30px;" class="payement">

              <thead>

                <tr>
                  <th class="legende" colspan="8" height="30"><?php echo "Crédits Clients" ?></th>
                </tr>

                <tr>
                  <th>N°</th>
                  <th>Client</th>
                  <th>Montant</th>            
                  <th>Date</th>
                  <th>Reste</th>
                  <th>Reglé</th>
                </tr>

              </thead>

              <tbody><?php

                foreach ($productable as $product):

                  $credit_client+=($product->Total-$product->remise-$product->montantpaye);?>

                  <tr>
                    <td style="text-align: center;"><?= $product->num_cmd; ?></td>

                    <td><?= $product->client; ?></td>

                    <td style="text-align: right;"><?= number_format($product->Total,2,',',' ') ; ?></td>

                    <td style="text-align: center;"><?= $product->DateTemps; ?></td>

                    <td style="color: red; text-align: right;"><?= number_format(($product->reste),2,',',' '); ?></td>

                    <td style="text-align: center;"><a href="emprunt.php?reglement=<?= $product->num_cmd; ?>"  class="print"><img src="css/img/regul.jpg" width="30" height="25"></a></td>
                  </tr>
                      
                <?php endforeach ?>

              </tbody>

              <thead>

                <tr>
                  <th colspan="4">TOTAL</th>            
                  <th style="text-align: right;"><?= number_format($credit_client,2,',',' ') ; ?></th>            
                </tr>

              </thead>

            </table>

            </br></br></br></br></br></br></br></br><?php
          }?>

        </div>

        <div class="credit_mag">

          <form method="post"  action="emprunt.php" id="naissance" style="width: 92%; margin: 2px;">

            <fieldset><legend>Effectuer un Remboursement</legend>

              <ol><?php

                if (isset($_GET['rembours'])) {?>
                  <li><label>Saisir N°</label><input type="number" name="id" value="<?=$_GET['rembours'];?>" min="0"  required=""></li>

                  <li><label>Montant à payer</label><input type="text"   name="montant_dec" id="pointeur" required="" min="0"></li><?php

                }else{?>
                  <li><label>Saisir N°</label><input type="number" name="id" min="0"  required=""></li>

                  <li><label>Montant à payer</label><input type="text"   name="montant_dec" id="pointeur" required="" min="0"></li><?php
                }?>

                <li><label>Payement</label><select name="mode_payement" required="">
                  <option></option>
                  <option value="espece">Especes</option>               
                  <option value="versement">Versement</option>
                  <option value="cheque">Cheque</option>
                  <option value="vire Bancaire">Virement</option></select>
                </li>
              </ol><?php
            
            if (empty($panier->totalsaisie())) {?>

              <fieldset><input type="reset" value="Annuler" name="valid" id="form" style="width:150px;"/><input type="submit" value="Valider" name="savemag" id="form" onclick="return alerteV();" style="margin-left: 20px; width:150px;" /></fieldset><?php

            }else{?>
              <div class="alertes"> La caisse est cloturée </div><?php
            }?>
          </fieldset>

        </form><?php


        if (isset($_POST['id'])){

          if ($_POST['montant_dec']!=0) {

            $product = $DB->query('SELECT id FROM decaissement WHERE id=:num_cmd', array('num_cmd' => $_POST['id']));

            if(empty($product)){

              echo "Ce numero est incorrect";

            }else{

              $productdec = $DB->query('SELECT * FROM decaissement  WHERE id =:ID', array('ID'=>$_POST['id']));

              foreach ($productdec as $product):
                $reste=$product->prix_reel-$product->montant;                
                $montant=($product->montant+$_POST['montant_dec']);?>
              <?php endforeach ?><?php

              if ($product->etat=="credit") {

                if($_POST['montant_dec']>$reste) {

                  echo "Impossible montant saisi est sup au credit";

                }elseif($_POST['montant_dec']<0) {

                  echo "format incorrect";

                }else{

                  $remb="magasin";
                  $products = $DB->query('SELECT num_cmd FROM historique WHERE num_cmd=:ID', array('ID' => $product->id));

                  if (!empty($products)) {
                    
                  }else{

                    $DB->insert('INSERT INTO historique (num_cmd, montant, payement, nom_client, date_cmd, remboursement) VALUES(?, ?, ?, ?, ?, ?)', array($_POST['id'], $product->montant, $product->payement, $product->client, $product->date_payement, $remb));
                  }

                  $date = date('y-m-d H:i');              
                  $DB->insert('UPDATE decaissement SET montant=?, payement= ?, date_regul=? WHERE id = ?', array($montant, $_POST['mode_payement'], $date, $_POST['id']));

                  $DB->insert('INSERT INTO historique (num_cmd, montant, payement, nom_client, date_cmd, remboursement, date_regul) VALUES(?, ?, ?, ?, ?, ?, now())', array($_POST['id'], $_POST['montant_dec'], $_POST['mode_payement'], $product->client, $product->date_payement, $remb));

                    $productdec = $DB->query('SELECT * FROM decaissement  WHERE id =:ID', array('ID'=>$_POST['id']));

                    foreach ($productdec as $product):

                      if (($product->montant)== $product->prix_reel) {

                        $DB->insert('UPDATE decaissement SET etat= ? , date_regul=? WHERE id= ?', array('clos', $date, $_POST['id']));

                        echo "La commande  N° ". $_POST['id']." au nom de ".$product->client." est reglée"  ;
                        
                      }else{

                        echo $product->client." a reglé  ". number_format($_POST['montant_dec'],0,',',' ')." GNF sur la commande N° ".$_POST['id'] ;

                      }?>

                    <?php endforeach ?> <?php

                  }

                }else{

                  echo "Crédit déjà reglé";
                }

              }

            }else{

              echo "Saisissez les Champs vides";

            }

          }else{
              
          }

          $credit_mag=0;
          $Etat="credit";

          $productlis = $DB->query('SELECT id, prix_reel,montant, monnaie, motif, client, DATE_FORMAT(date_payement, \'%d/%m/%Y \')AS DateTemps FROM decaissement  WHERE etat=:Etat ORDER BY (date_payement)DESC', array('Etat'=>$Etat));
          if (empty($productlis)) {
            
          }else{?>

            <table class="payement" style="margin-top: 30px;">

              <thead>

                <tr>
                  <th class="legende" colspan="6" height="30" ><?php echo "Crédits du restaurant" ?></th>
                </tr>

                <tr>
                  <th>N°</th>
                  <th>Destinataire</th>            
                  <th>Montant</th>            
                  <th>Date</th>
                  <th>Reste</th>
                  <th>Reglé</th>
                </tr>
              </thead>

              <tbody><?php             

                foreach ($productlis as $product): 

                  $credit_mag+=(($product->prix_reel)-($product->montant)); ?>

                  <tr>
                    <td style="text-align: center;"><?= $product->id; ?></td>

                    <td><?= $product->client; ?></td>

                    <td style="text-align: right;"><?= number_format($product->montant,2,',',' ') ; ?></td>

                    <td><?= $product->DateTemps; ?></td>

                    <td style="color: red;text-align: right"><?= number_format((($product->prix_reel)-($product->montant)),2,',',' '); ?></td>

                    <td style="text-align: center;"><a href="emprunt.php?rembours=<?= $product->id; ?>"  class="print"><img src="css/img/regul.jpg" width="30" height="30"></a></td>

                  </tr>

                <?php endforeach ?>

              </tbody>

              <thead>
                <tr>
                  <th colspan="4">TOTAL</th>            
                  <th colspan="2" style="text-align: right;"><?= number_format($credit_mag,2,',',' ') ; ?></th>
                </tr>
              </thead>

            </table><?php
          }?>

        </div>

      </div> <?php 

    }else{
      echo "vous n'avez pas les autorisations requises";
    }

  }else{

  }?>

</div>
</div>

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
