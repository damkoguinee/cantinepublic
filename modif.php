<?php
require '_header.php';

if (isset($_POST['update'])) {
  $products = $DB->query('SELECT * FROM stock  WHERE nom =:Nom', array('Nom'=>$_POST['nom'])); 

  foreach ($products as $product):

    $quantity=($product->quantity+$_POST['quantity']);?>

  <?php endforeach ?><?php
  
  if ($_POST['prix']==0) {

    $DB->insert('UPDATE stock SET quantity= ? WHERE nom = ?', array($quantity,$_POST['nom']));

    header("Location: stock.php");

  }else{
    
    $DB->insert('UPDATE stock SET prix_vente = ? WHERE nom = ?', array($_POST['prix'],$_POST['nom']));

   header("Location: stock.php");

  }

  if ($_POST['prix']!=0 and $_POST['quantity']!=0 ) {

    $DB->insert('UPDATE stock SET quantity= ?, prix_vente=? WHERE nom = ?', array($quantity, $_POST['prix'], $_POST['nom']));

    header("Location: stock.php");

  }
}

if (isset($_POST['delete'])) {
  $nom=$_POST['nom'];

  $DB->delete('DELETE FROM stock WHERE nom = ?',array($nom));
  
  $req=$DB->querys('SELECT nom FROM stocks WHERE nom=:Nom', array('Nom'=>$nom));

  if (!empty($req)) {

    echo "La suppression a reussie";

  }else{

    echo "La suppression a echouée";
  }

  header("Location: stock.php");
}?>

</body>
</html> 