<?php
require 'header1.php';
if (isset($_SESSION['pseudo'])) {

  $pseudo=$_SESSION['pseudo'];

  $products = $DB->querys('SELECT statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));

  if ($products['statut']=="admin") {?>

    <div class="box_admin">

      <div class="ajout_prod">

        <form id='admin' method="POST" action="admin.php">

          <table style="margin-top: 30px;" class="payement">

            <thead>

              <tr>
                <th class="legende" colspan="5" height="30">Ajouter un produit</th> 
              </tr>

              <tr>
                <th>Désignation</th>      
                <th>P. Vente</th>
                <th>Qtité</th>
                <th>Type</th> 
                <th>Genre</th>  
              </tr>

            </thead>

            <tbody>

              <td><input type="text" name="name" required=""></td>
              
              <td><input type="text" name="prix_vente" required=""></td>

              <td><input type="text" name="quantity" required=""></td>

              <td><input type="text" name="type" required=""></td>

              <td><input type="text" name="genre" required=""></td>

            </tbody>

          </table>

          <input style="width: 10%;height: 40px; font-size: 17px;"  type="reset"  value="Annuler" onclick="return alerteS();">

          <input style="width: 10%;height: 40px; font-size: 17px; margin-top: 15px;"  type="submit" name="ajout" value="Ajouter" onclick="return alerteV();">

        </form> <?php
        
        if (!isset($_POST['name'])) {
          
        }else{

          $DB->insert('INSERT INTO stock (nom, prix_vente, quantity, type, genre) VALUES(?, ?, ?, ?, ?)', array(ucwords($_POST['name']),  $_POST['prix_vente'], $_POST['quantity'], ucwords($_POST['type']), ucwords($_POST['genre'])));

          $productmenu = $DB->querys('SELECT type FROM menu WHERE type=:Type', array('Type'=>$_POST['type']));

          if (!empty($productmenu)) {

            $DB->insert('INSERT INTO menu (nom, prix_vente, type) VALUES (?, ?, ?)', array("Menu"." ".$_POST['type'], $_POST['prix_vente'], ucwords($_POST['type'])));

          }else{

          }

          $productstock= $DB->querys('SELECT type FROM stock WHERE nom=:Nom', array('Nom'=>$_POST['name']));

            if (!empty($productstock)) {

              echo "Votre ajout n'est pas pris en compte";

            }else{

              echo $_POST['name']." a  été ajouter";
            }

        }?>

      </div>

      <div>

        <form id='admin' method="POST" action="admin.php">

          <table style="margin-top: 30px;" class="payement">

            <thead>

              <tr>
                <th class="legende" colspan="3" height="30">Infos du restaurant</th> 
              </tr>

              <tr>
                <th>Nom du restaurant</th>                
                <th>Type de restaurant</th>
                <th>Adresse</th>
              </tr>

            </thead>

            <tbody>

              <td><input type="text" name="name_mag" required=""></td>

              <td><input type="text" name="type_mag" required=""></td>

              <td><input type="text" name="adress_mag" required=""></td>

            </tbody>

          </table>

          <input style="width: 10%;height: 40px; font-size: 17px;"  type="reset"  value="Annuler" onclick="return alerteS();">

          <input style="width: 10%;height: 40px; font-size: 17px; margin-top: 15px;"  type="submit" name="ajout" value="Ajouter" onclick="return alerteV();"> 

        </form> <?php

        if (!isset($_POST['name_mag'])) {
          
        }else{

          $products = $DB->query('SELECT * FROM adresse ');

          if (!empty($products)) {

            echo "Une adresse est dejà attribuée à ce restaurant";

          }else{  

            $DB->insert('INSERT INTO adresse (nom_mag, type_mag, adresse) VALUES(?, ?, ?)', array(ucwords($_POST['name_mag']), ucwords($_POST['type_mag']), ucwords($_POST['adress_mag'])));

            $products = $DB->querys('SELECT nom_mag FROM adresse ');

            if (!empty($products)) {

              echo "Votre ajout n'est pas pris en compte";

            }else{

                echo "Restaurant enregistré avec succès";
            }

          }

        }?>

      </div>
    </div><?php  

  }else{
    echo "vous n'avez pas les autorisations requises";
  }
}else{

}?>
</body>

</html>

<script type="text/javascript">
  function alerteS(){
      return(confirm('Valider lannulation'));
  }

  function alerteV(){
      return(confirm('Confirmer la validation'));
  }

  function focus(){
      document.getElementById('pointeur').focus();
  }

</script>
