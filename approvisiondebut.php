<?php require 'header3.php';
 require 'headercmd.php';?>

<script>
  function suivant(enCours, suivant, limite){
    if (enCours.value.length >= limite)
    document.term[suivant].focus();
  }
</script>

<div class="container-fluid"><?php  

  if (isset($_POST['qtiteap'])) {

    $id=$panier->h($_POST['idap']);

    $qtite=$panier->h($_POST['qtiteap']);

    $depart = $DB->querys("SELECT quantity as qtite FROM stock WHERE id=?", array($id));

    $qtited=$depart['qtite']+$qtite;

    $DB->insert("UPDATE stock SET quantity= ? WHERE id = ?", array($qtited, $id));

    $DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($id, 'approv', 'entree' , $qtite));

  }?>

  <div class="col-sm-12 col-md-8">

    <table class="table table-hover table-bordered table-striped table-responsive">

      <form method="GET" action="approvisiondebut.php" id="suite" name="term">

        <thead>

          <tr>
            <th colspan="5"><input class="form-control" id="search-user" type="text" name="client" placeholder="rechercher dans une liste" />
              <div style="color:white; background-color: black; font-size: 11px;" id="result-search"></div></th>
            
          </tr>  
         

          <tr>
            <th>N°</th>
            <th>Désignation</th>
            <th>dispo</th>
            <th>Qtité</th>
            <th></th> 
          </tr>

        </thead>
      </form>

      <tbody>

        <?php
        $tot_achat=0;
        $tot_revient=0;
        $tot_vente=0;
        $qtiteR=0;
        $qtiteS=0;

        if (!isset($_GET['termeliste'])) {

          if (isset($_GET['terme'])) {

            if (isset($_GET["terme"])){

                $_GET["terme"] = htmlspecialchars($_GET["terme"]); //pour sécuriser le formulaire contre les failles html
                $terme = $_GET['terme'];
                $terme = trim($terme); //pour supprimer les espaces dans la requête de l'internaute
                $terme = strip_tags($terme); //pour supprimer les balises html dans la requête

                $_SESSION['terme']=$terme;
            }

            if (isset($terme)){

                $terme = strtolower($terme);
                $products = $DB->query("SELECT * FROM stock WHERE nom LIKE ? OR nom LIKE ? order by(nom)",array("%".$terme."%", "%".$terme."%"));
            }else{

             $message = "Vous devez entrer votre requete dans la barre de recherche";

            }

            if (empty($products)) {?>

              <div class="alertes">Produit indisponible<a href="ajout.php">Ajouter le produit</a></div><?php

            }

          }else{

            if (!empty($_SESSION['terme'])) {
              
              $products = $DB->query("SELECT * FROM stock WHERE nom LIKE ? OR nom LIKE ? order by(nom)",array("%".$_SESSION['terme']."%", "%".$_SESSION['terme']."%"));

            }else{
              $genre="boissons";
              $taille='bouteille';
              $genre1="aromes";

              $products = $DB->query("SELECT * FROM stock WHERE genre='{$genre}'  and taille='{$taille}' order by(nom) LIMIT 50");
            }
          }
        }else{

           $products = $DB->query("SELECT * FROM stock WHERE id= ? order by(nom)",array($_GET['termeliste']));
        }

        if (!empty($products)) {

          foreach ($products as $key=> $product){
            $color='';?>

            <tr>
              <td style="text-align: center;"><?=$key+1;?></td>  

              <td style="font-size: 15px; color:<?=$color;?>"><?= ucwords(strtolower($product->nom)); ?></td>

              <td style="text-align: center;"><?=$product->quantity;?></td>

              <form action="approvisiondebut.php" method="POST">

                <td><input class="form-control" type="number" name="qtiteap" min="0" style="width: 90%;" /><input type="hidden" name="idap" value="<?=$product->id;?>"></td>

                <td><?php if ($_SESSION['level']>6) {?><input class="btn btn-warning" type="submit" name="validap" value="Approvisionner" onclick="return alerteT();" ><?php }?></td>
              </form>

            </tr><?php
        }
      }?>


    </tbody>

  </table>
  
</div>
</div><?php 
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
                    url: 'rechercheproduit.php?appro',
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

    function alerteV(){
        return(confirm('Confirmer la validation'));
    }

    function alerteT(){
        return(confirm('Confirmer le transfert des produits'));
    }

    function focus(){
        document.getElementById('pointeur').focus();
    }

</script>  