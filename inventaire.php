<?php require 'header1.php';

if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

  $products = $DB->querys('SELECT statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

  if ($products['statut']!="vendeur") {?>
    <fieldset style="margin-top: 10px;"><legend>Compatabilité</legend>
      <div class="choixg">
        <div class="optiong">
          <a href="inventaire.php?journaliere=<?='journa';?>">
          <div class="descript_optiong">Journalière</div></a>
        </div>
        <div class="optiong">
          <a href="comptamensuelle.php?mensuelle=<?='mensu';?>">
          <div class="descript_optiong">Mensuelle</div></a>
        </div>
        <div class="optiong">
          <a href="inventaire.php?annuelle=<?='annu';?>">
          <div class="descript_optiong">Annuelle</div></a>
        </div> 
      </div>
    </fieldset><?php

    if (isset($_GET['journaliere']) or isset($_POST['aujourdhui']) or isset($_GET['deletep']) or isset($_GET['actual']) ) {      
      require 'comptabilite.php';
    }else{

      if (!isset($_POST['annee'])) {

        $_SESSION['date']=date("Y");
        
      }else{

        $_SESSION['date']=$_POST['annee'];
        
      }

      if (isset($_POST['liquide'])) {

        $_SESSION['liquide']=$_POST['liquide'];

        $liquide=$_SESSION['liquide'];

      }elseif(isset($_POST['chiffrea'])){

        $liquide=$_SESSION['liquide'];

      
      }else{

        $liquide=0;

      }

      $tot_achat=0;
      $tot_vente=0;
      $stock = $DB->query('SELECT * FROM products ');

      foreach ($stock as $product){

        $tot_achat+=$product->prix_achat*$product->quantite;
        $tot_vente+=$product->prix_vente*$product->quantite;

      }?>

      <form id='naissance' method="POST" action="inventaire.php"> 

        <ol>
          <li><label></label>

            <?='<select id="reccode" style="width: 250px; font-size: 14px;"  type="number" name="annee" required="" onchange="this.form.submit();">',"n";
              if (isset($_POST['annee'])) {?>
                <option value=""><?="Année ".$_POST['annee'];?></option><?php

              }else{

                echo "\t",'<option value="">Choisir une année...</option>',"\n";

              }

            $annee=date("Y");

            for($i=2018;$i<=$annee ;$i++){

              echo "\t",'<option value="', $i,'">', $i,'</option>',"\n";

            }?></select>
            
          </li>
        </ol>
      </form>

      <form id='liquide' method="POST" action="inventaire.php">

        <div class="tbord">      

          <div class="casem">

            <div class="descriptd">ARGENT LIQUIDE</br>

              <input class="descriptmf" type="float" name="liquide" onchange="document.getElementById('liquide').submit()" value="<?=number_format($liquide,2,',',' ');?>">
            </div>
          </div>
        

          <div class="descripts">+</div>
        
          <div class="casem">
            <div class="descriptd">MONTANT STOCK
              <div class="descriptm"><?=number_format($tot_vente,2,',',' ');?></div>
            </div>
          </div>

          <div class="descripts">+</div>
          <div class="casem">
            <div class="descriptd">CREDITS CLIENTS
              <div class="descriptm"><?= number_format(0,2,',',' ') ; ?></div>
            </div>
          </div>
          <div class="descripts">-</div>

          <div class="casem">
            <div class="descriptd">CREDITS MAGASIN
              <div class="descriptm"><?=number_format(0,2,',',' ') ; ?></div>
            </div>
          </div>
          <div class="descripts">-</div>
          <div class="casem">
            <div class="descriptd">DEPENSES
              <div class="descriptm"><?=number_format(0,2,',',' ') ; ?></div>
            </div>
          </div>

        </div>
        <div class="descripts">| |</div>

        <div class="casem"><?php

          $chiffrea=$liquide+$tot_vente+0;?>
          <div class="descriptd">CHIFFRE D'AFFAIRE <?= date("Y");?>
            <div class="descriptm"><?=number_format($chiffrea,2,',',' ') ; ?></div>
          </div>
        </div>
      </form>                

      <form id="chiffrea" action="inventaire.php" method="POST"><?php

        if (isset($_POST['chiffrea'])) {

          $chiffreaa=$_POST['chiffrea'];
        
        }else{
          $chiffreaa=0;
        }?>              

        <div class="tbord">                

          <div class="casem">
            <div class="descriptd">CHIFFRE D'AFFAIRE <?= date("Y")-1;?></br>
              <input class="descriptmf" type="float" name="chiffrea" onchange="document.getElementById('chiffrea').submit()" value="<?=number_format($chiffreaa,2,',',' ');?>">
            </div>
          </div>

          <div class="casem" style="margin-left: 20px;"><?php            

            if (!isset($_POST['chiffrea'])) {

            }else{

              if (($chiffrea-$chiffreaa)<0) {?>
                
                <div class="descriptd">MANQUE NET <?= date("Y");?>
                <div class="descriptmbn"><?=number_format($chiffrea-$chiffreaa,2,',',' ');?> €</div><?php

              }else{?>

                <div class="descriptd">BENEFICE NET <?= date("Y");?>
                <div class="descriptmbp"><?=number_format($chiffrea-$chiffreaa,2,',',' ');?> €</div><?php
              }
            }?>

          </div>
        </div>
      </form>

      <div id="bilaninv">

        <div class="bloc_prodinv">

          <table class="bilan">

            <thead>

              <tr>
                <th class="legende" colspan="2" height="30"><?php echo "PRODUITS VENDUS EN  " .$_SESSION['date'] ?></th>
              </tr>

              <tr>
                <th style="width: 10%;">Qtite</th>
                <th>Designation</th>
              </tr>

            </thead>

            <tbody>
              <?php 
              $quantite=0;
              $totpv=0;

              foreach ($stock as $produc ): 

                $products = $DB->query('SELECT SUM(quantity) AS quantite, SUM(prix_vente) AS pv, designation FROM commande WHERE YEAR(date_cmd) = :annee AND designation= :desig', array(
                  'annee' => $_SESSION['date'],
                  'desig'=>$produc->designation));     

                foreach ($products as $product ):

                  $quantite+= $product->quantite;
                  $totpv+= $product->pv;

                  if (!empty($product->designation)) {?>

                    <tr>
                      <td style="text-align: right;"><?= $product->quantite; ?></td>
                      <td style="text-align: left;"><?= strtolower($product->designation); ?></td>
                    </tr><?php

                  }else{

                  }?>                      
                <?php endforeach ?>
              <?php endforeach ?>

              <tr> 
                <th style="text-align: right; padding-right:20px"><?= $quantite; ?></th>        
                <th colspan="1" height="40"></th>          
              </tr>

            </tbody>

          </table>

        </div>

        <div class="box_stockinv" style="margin-right: 30px">

          <table class="bilan">

                <thead>

                    <tr>
                      <th class="legende" colspan="3" height="30"><?php echo "LISTE DES DEPENSES " .$_SESSION['date'] ?></th>
                    </tr>

                    <tr>                      
                      <th>DATE</th>
                      <th>MOTIF</th>
                      <th>MONTANT</th>
                    </tr>

                </thead>

                <tbody><?php 
                  $totaldepenses=0;
                  $products=$DB->query('SELECT montant, client, DATE_FORMAT(date_payement, \'%d/%m/%Y \')AS DateTemps FROM decaissement WHERE motif=:MOTIF AND YEAR(date_payement) = :annee ORDER BY(id)DESC', array(
                    'MOTIF' => "depenses",
                    'annee' => $_SESSION['date']
                  ));

                  foreach ($products as $product ):?>                   
                      <td><?= $product->DateTemps; ?></td>                       
                      <td><?= strtolower($product->client); ?></td>
                      <td style="text-align: right; padding-right: 15px"><?= number_format($product->montant,2,',',' '); ?> € </div>          
                      
                    </tr>

                  <?php endforeach ?>


                </tbody>

                <thead>

                <tr>
                  <th colspan="2">TOTAL</th>
                  <th style="text-align: right;padding-right: 15px"><?= number_format($panier->totdepense(),2,',',' ') ; ?>  € </th>
                </tr>

            </thead>

            </table>
          </div>

          <div class="box_stockinv">

          <a href="printstock.php?stock"><div class="printstock">IMPRIMER STOCK</div></a>

          <table class="bilan">

            <thead>

              <tr>
                <th class="legende" colspan="4" height="30"><?php echo "STOCK DISPONIBLE LE " .date("d/m/y"). "  à  ".date("H:i") ?></th>
              </tr>

              <tr>
                <th>DESIGNATION</th>
                <th>QT</th>
                <th>P. ACHAT</th>
                <th>P. VENTE</th>
              </tr>

            </thead>

            <tbody>

              <?php
              $tot_achat=0;
              $tot_vente=0;
              $products = $DB->query('SELECT * FROM products WHERE quantite!=0 AND type="en_gros" ORDER BY (quantite)');

              foreach ($products as $product):

                $tot_achat+=$product->prix_achat*$product->quantite;
                $tot_vente+=$product->prix_vente*$product->quantite;?>

                <tr>              
                  <td><?= strtolower($product->designation); ?></td>
                  <td style="text-align: center;"><?= $product->quantite; ?></td>
                  <td style="text-align: right;"><?= number_format($product->prix_achat,2,',',' ') ; ?> </td>
                  <td style="text-align: right;"><?= number_format($product->prix_vente,2,',',' '); ?>  </td>
                </tr>
                  
              <?php endforeach ?>

            </tbody>

            <thead>

              <tr>
                <th colspan="2">TOTAL</th>

                <th style="text-align: right;"><?= number_format($tot_achat,2,',',' ') ; ?> </th>
                <th style="text-align: right;"><?= number_format($tot_vente,2,',',' ') ; ?> </th>

              </tr>

            </thead>

          </table>

          <table style="margin-top: 30px;" class="bilan">

            <thead>

              <tr>
                <th class="legende" style="background: #FF0FCF;" colspan="4"><?php echo "STOCK DETAILS DISPONIBLE LE " .date("d/m/y"). "  à  ".date("H:i") ?></th>
              </tr>

              <tr>
                <th style="background: #FF0FCF;">DESIGNATION</th>
                <th style="background: #FF0FCF;">Qt</th>
                <th style="background: #FF0FCF;">P.ACHAT</th>
                <th style="background: #FF0FCF;">P.VENTE</th>
              </tr>

            </thead>

            <tbody><?php

              $tot_achat=0;
              $tot_vente=0;
              $products = $DB->query('SELECT * FROM products WHERE quantite!=0 AND type!="en_gros" ORDER BY (quantite)');

              foreach ($products as $product):

                
                $tot_achat+=$product->prix_achat*$product->quantite;
                $tot_vente+=$product->prix_vente*$product->quantite;?>

                  <tr>
                   
                    <td><?=strtolower($product->designation); ?></td>
                    <td style="text-align: center;"><?= $product->quantite; ?></td>
                    <td style="text-align: right;"><?= number_format($product->prix_achat,2,',',' ') ; ?>  </td>
                    <td style="text-align: right;"><?= number_format($product->prix_vente,2,',',' '); ?>  </td>

                  </tr>
                 
              <?php endforeach ?>

            </tbody>

            <thead>          
              <tr>
                <th style="background: #FF0FCF;"colspan="2">TOTAL</th>            
                <th style="background: #FF0FCF; text-align: right;"><?= number_format($tot_achat,2,',',' ') ; ?> </th>
                <th style="background: #FF0FCF; text-align: right;"><?= number_format($tot_vente,2,',',' ') ; ?> </th>
              </tr>
            </thead>

          </table>

        </div>     

      </div><?php

    }
  }else{

      echo "VOUS N'AVEZ PAS TOUTES LES AUTORISATIOS REQUISES";
    }

  }else{


  }?>
  
</body>
</html>

