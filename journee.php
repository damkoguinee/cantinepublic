<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
  <head>
      <title>Restaurant</title>
      <meta charset="utf-8">
      <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
      <link rel="stylesheet" href="css/comptabilite.css" type="text/css" media="screen" charset="utf-8">
      <link rel="stylesheet" href="css/client.css" type="text/css" media="screen" charset="utf-8">
  </head>
  <body><?php

    if (isset($_SESSION['pseudo'])) {?>

      <form id="naissance" method="POST" action="journee.php" style="width: 50%; margin: auto;">
        <fieldset><legend>Commencez une Nouvelle Journée </legend>
          <ol>     

            <li><label>DATE</label>
              <input type="date" name="dated" required="">
            </li>
          </ol>

          <fieldset><input type="reset" value="Annuler" name="valid" id="form" style="cursor: pointer; width:150px;"/><input type="submit" value="Valider" name="ajouter" id="form" onclick="return alerteV();" style="cursor: pointer; margin-left: 20px; width:150px;" /></fieldset>
        </fieldset>

        <div class="option"><a href="deconnexion.php">
          <div class="descript_option" style="color: red;">Déconnexion</div></a>
        </div>
      </form><?php

      if (!isset($_POST['ajouter'])) {
              
      }else{
        $dated=$_POST['dated'];
        $DB->delete('DELETE FROM debutjournee');

        $DB->insert('INSERT INTO debutjournee (datev, etat) VALUES(?, ?)', array($dated, 1));

        $heure=date("h:i:s");

        $_SESSION['datev']=$dated;  
        
        header('Location: choix1.php');
      }

    }else{

      header('Location: index.php');

    }?>
  </body>

</html>

<script>
function suivant(enCours, suivant, limite){
  if (enCours.value.length >= limite)
  document.term[suivant].focus();
}

function focus(){
document.getElementById('reccode').focus();
}

function alerteS(){
  return(confirm('Confirmer la suppression?'));
}

function alerteV(){
    return(confirm('Confirmer la validation'));
}

function alerteM(){
  return(confirm('Confirmer la modification'));
}
</script>