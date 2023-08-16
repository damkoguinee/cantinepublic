<?php require 'header1.php';
if (isset($_SESSION['pseudo'])) {
  $pseudo=$_SESSION['pseudo'];

  $products = $DB->querys('SELECT statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

  if ($products['statut']!="vendeur") {?>

    <div class="box_modif">

      <div class="box_stock"><?php
        $tot_achat=0;
        $tot_vente=0;
        $products = $DB->query('SELECT * FROM stock WHERE genre="boisson" ORDER BY (quantity)');?>

        <table style="margin-top: 30px;" class="payement">
          <thead>
            <tr>
              <th class="legende" colspan="3" height="30"><?php echo "Stock boissons " .date("d/m/y"). "  à  ".date("H:i") ?></th>
            </tr>
            <tr>
              <th>Désignation</th>
              <th>Qtité</th>
              <th>P. Vente</th>
            </tr>
          </thead>
          <tbody><?php 
            foreach ($products as $product):
              $tot_vente+=$product->prix_vente*$product->quantity;?>

              <tr>              
                <td><?= $product->nom; ?></td>
                <td style="text-align: center;"><?= $product->quantity; ?></td>
                <td style="text-align: right;"><?= number_format($product->prix_vente,2,',',' '); ?></td>
              </tr>
                      
            <?php endforeach ?>

          </tbody>

          <thead>

            <tr>
              <th class="10p"colspan="2">TOTAL</th>            
              <th style="text-align: right;"><?= number_format($tot_vente,2,',',' ') ; ?></th>
            </tr>

          </thead>

        </table>
              
      </div>

        <div class="modif_prix"><?php

          $products = $DB->query('SELECT * FROM stock');?>

          <form method="post" action="modif.php">

            <table style="margin-top: 30px;" class="payement">

              <thead>              
                <tr>
                  <th>Selectionnez</th>                
                  <th>P. Vente</th>
                  <th>Qtité</th>
                  <th colspan="2">Action</th>
                </tr>
              </thead>

              <tbody>
                <td><?='<select type="text" name="nom" required="" style="width: 100%;>',"n";
                  echo "\t",'<option></option>',"\n";

                  foreach($products as $product): 

                    echo "\t",'<option value="', $product->nom,'">', $product->nom,'</option>',"\n";?>

                  <?php endforeach ?></td>

                <td style="width: 30%;"><input class="prixstock" type="number" name="prix" value=""></td> 

                <td style="width: 15%;"><input class="quantitestock" type="number" name="quantity" value=""></td>

                <td><input style="width: 100%;height: 30px; font-size: 17px;"  type="submit" name="update" value="Modifier" onclick="return alerteV();"></td>

                <td><input style="width: 100%;height: 30px; font-size: 17px; background-color: red;color: white;"  type="submit" name="delete" value="Supprimer" onclick="return alerteS();"></td>
              </tbody>
            </table>

          </form>


          <div class="sup_prod">

            <form method="post" action="stock.php">
              <table class="payement">
                <thead>
                  <tr><th colspan="2">Supprimer des commandes</th></tr>
                  <tr>
                    <th>N° Commande</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="number" name="num_cmd" ></td><?php
                      if (empty($panier->totalsaisie())) {?>

                        <td><input style="width: 100%;height: 30px; font-size: 15px; cursor: pointer;"  type="submit" name="valid" value="VALIDER" onclick="return alerteS();"></td><?php
                      }else{?>
                        <td class="alertes">Caisse cloturée</td><?php
                      }?>
                  </tr>
                </tbody>
                
              </table>

            </form><?php

              if (!isset($_POST['num_cmd'])) {

              }else{

                $numero=$_POST['num_cmd'];

                $productcom=$DB->querys('SELECT nom , quantity FROM commande WHERE num_cmd=:Num', array('Num'=>$numero));

                $nom=$productcom['nom'];

                $qtite1=$productcom['quantity'];

                $productstock = $DB->querys('SELECT nom, quantity FROM stock WHERE nom=:Nom', array('Nom'=>$nom));

                $quantite=$productstock['quantity']+$productstock['quantity'];
                
                $DB->insert('UPDATE stock SET quantity = ? WHERE nom = ?', array($quantite, $nom));


                $DB->delete('DELETE FROM payement WHERE num_cmd = ?', array($numero));

                $DB->delete('DELETE FROM commande WHERE num_cmd = ?', array($numero));

                $products = $DB->querys('SELECT num_cmd FROM payement WHERE num_cmd=:Num', array('Num'=>$numero));

                if (empty($products)) {

                  echo "Commande supprimer avec succès";

                }else{

                  echo "Commande non supprimer";
                }

              }?>
            
              <form method="post" action="stock.php">
                <table class="payement" style="margin-top: 40px;">
                  <thead>
                    <tr><th colspan="2">Supprimer un décaissement</th></tr>
                    <tr>
                      <th>N° décaissement</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><input type="number" name="num_dec" ></td><?php
                        if (empty($panier->totalsaisie())) {?>

                          <td><input style="width: 100%;height: 30px; font-size: 15px; cursor: pointer;"  type="submit" name="valid" value="VALIDER" onclick="return alerteS();"></td><?php
                        }else{?>
                          <td class="alertes">Caisse cloturée</td><?php
                        }?>
                    </tr>
                  </tbody>
                
                </table>
              </form><?php

              if (!isset($_POST['num_dec'])) {

              }else{

                $numero=$_POST['num_dec'];
                $DB->delete('DELETE FROM decaissement WHERE id = ?', array($numero));

                $req = $DB->query('SELECT id FROM decaissement WHERE id=:ID', array('ID'=>$numero));

                if (empty($req)) {

                  echo "Décaissement supprimer avec succès";

                }else{

                  echo "La suppression a echouée verifiez le numero";
                }

              }?>

            </div>

          </div>

        </div><?php

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
