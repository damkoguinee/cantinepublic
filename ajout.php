<?php require 'header3.php';

if (isset($_SESSION['pseudo'])) {  

  if ($_SESSION['level']>=3) {

    if(isset($_GET["ajout"])){

      require 'headercmd.php';

    }else{

    }

    if (!isset($_POST['ajouter'])) {
              
      }else{
        $name=$_POST['name'];

        if (!empty($_POST['qtiteint'])) {
          $qtiteint=$_POST['qtiteint'];
        }else{
          $qtiteint=0;
        }

        if ($_POST['type']=='accompagnements') {
          $idcat=4;
        }elseif ($_POST['type']=='supplements') {
          $idcat=5;
        }elseif ($_POST['genre']=='aromes') {
          $idcat=7;
        }elseif ($_POST['type']=='Nos Chichas') {
          $idcat=3;
        }elseif ($_POST['genre']=='cafes') {
          $idcat=6;
        }elseif ($_POST['genre']=='plat') {
          $idcat=2;
        }else{
          $idcat=1;
        }


        $DB->insert('INSERT INTO stock (nom, prix_achat, prix_vente, prix_revient, quantity, type, genre, taille, qtiteint, idcat) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($_POST['name'], $_POST['prix_achat'], $_POST['prix_vente'], $_POST['prix_revient'], $_POST['quantity'], $_POST['type'], $_POST['genre'], $_POST['taille'], $qtiteint, $idcat));

        if (isset($_POST['etating']) or $_POST['genre']=='ingredient' or $_POST['taille']=='bouteille' or $_POST['genre']=='aromes') {

          $prodstock = $DB->querys("SELECT max(id) as id FROM stock");
          $ids=$prodstock['id'];

          $DB->insert('INSERT INTO ingredient (nom, qtite) VALUES(?, ?)', array($ids, 0));

          
        }?>

        <div class="alert alert-success" role="alert">Produit ajouté avec succèe!!!</div><?php

      }

      if (isset($_POST['genre']) and $_POST['genre']=='menu') {

        $producttype = $DB->query("SELECT nom, type FROM menucompose");
      }else{

        $producttype = $DB->query("SELECT nom, type FROM menu");

      }?>
    
    <div class="container">

      <div class="row">

        <div class="col-2 m-3">

          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="ajout.php?boisson">Ajout Boissons</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="ajout.php?ingredient">Ajout Ingredients</a>
            </li>

            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="ajout.php?autres">Autres Ajout</a>
            </li>
          </ul>
        </div>

        <div class="col">

      

        <form method="POST" action="ajout.php" >
          <fieldset><legend>Completez pour ajouter un produit</legend>

            <div class="row">

              <div class="col-sm-12 col-md-6"><?php 

                if (isset($_GET['boisson'])) {?>          

                  <div class="mb-1">
                    <input type="hidden"  class="form-control" name="genre" value="boissons">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Désignation*</label>
                    <input type="text"  class="form-control" name="name" required="">
                  </div>             

                  <div class="mb-1">
                    <label class="form-label">Type*</label>
                    <select class="form-select" name="type" required="" >
                      <option></option><?php
                      foreach ($producttype as $key => $value) {?>

                        <option value="<?=$value->type;?>"><?=$value->nom;?></option><?php 
                      }?>

                      <option value="Cocktails Speciaux Alcoolises">Cocktails Speciaux Alcoolises</option>
                      <option value="Cocktails Chauds">Cocktails Chauds</option>                  
                      <option value="Cocktails sans alcool">Cocktails sans alcool</option>
                      <option value="Cocktails Avec Alcool">Cocktails avec alcool</option>
                      <option value="Jus de Fruit">Jus de Fruit</option>

                      <option value="liqueur">Liqueurs</option>
                      <option value="gin">Gin</option>
                      <option value="wisky">Wisky</option>
                      <option value="cognac">Cognac</option>
                      <option value="tequilas">Tequilas</option>
                      <option value="vodka">Vodka</option>
                      <option value="rhums">Rhums</option>
                      <option value="vins rouges">Vins Rouges</option>
                      <option value="vins roses">Vins Roses</option>
                      <option value="vins blancs">Vins Blancs</option>
                      <option value="champagnes">Champagnes</option>
                      <option value="bordeaux">Bordeaux</option>
                      <option value="saint emilion">Saint Emilion</option>
                      <option value="medocs">Médocs et Haut Médocs</option>
                      <option value="graves">Graves</option>
                      <option value="vin mousseux">Vin Mousseux</option>
                    </select>
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Taille</label>
                    <select class="form-select" name="taille">
                      <option value="bouteille">Bouteille</option>
                      <option value="verre">Verre</option>
                    </select>
                  </div>

                  <div class="mb-1">
                    <label class="form-label">Quantité Int</label>
                    <input type="number"  class="form-control" name="qtiteint" min="0">
                  </div>
                </div>

                <div class="col-sm-12 col-md-6">
                  <div class="mb-1">
                    <label class="form-label">P. Achat</label>
                    <input type="number"  class="form-control" name="prix_achat" value="0" min="0">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">P. Revient</label>
                    <input type="number"  class="form-control" name="prix_revient" value="0" min="0">
                  </div>
            

                  <div class="mb-1">
                    <label class="form-label">P. Vente</label>
                    <input type="number"  class="form-control" name="prix_vente" value="0" min="0">
                  </div>

                  <div class="mb-1">
                    <label class="form-label">quantite</label>
                    <input type="number"  class="form-control" name="quantity" value="0" min="0">
                  </div>
                </div>
              </div><?php 
            }elseif (isset($_GET['ingredient'])) {?>

              
              <input type="hidden"  class="form-control" name="genre" value="ingredient">
              <input type="hidden"  class="form-control" name="type" value="ingredient">
              <input type="hidden"  class="form-control" name="taille">
              <input type="hidden"  class="form-control" name="qtiteint" min="0">            

              <div class="mb-1">
                <label class="form-label">Désignation*</label>
                <input type="text"  class="form-control" name="name" required="">
              </div>              
            
              <div class="mb-1">
                <label class="form-label">P. Achat</label>
                <input type="number"  class="form-control" name="prix_achat" value="0" min="0">
              </div>

              <div class="mb-1">
                <label class="form-label">P. Revient</label>
                <input type="number"  class="form-control" name="prix_revient" value="0" min="0">
              </div>
        

              <div class="mb-1">
                <label class="form-label">P. Vente</label>
                <input type="number"  class="form-control" name="prix_vente" value="0" min="0">
              </div>

              <div class="mb-1">
                <label class="form-label">quantite</label>
                <input type="number"  class="form-control" name="quantity" value="0" min="0">
              </div>
            </div>
          </div><?php
          }else{?>

            <div class="mb-1">
              <input type="hidden"  class="form-control" name="qtiteint" min="0">
              <label class="form-label">Genre*</label>
              <select class="form-select" name="genre" required="" onchange="this.form.submit()"><?php 

                if (isset($_POST['genre'])) {?>
                  <option value="<?=$_POST['genre'];?>"><?=ucfirst($_POST['genre']);?></option><?php
                }else{?>
                  <option></option><?php 
                }?>
                <option value="dessert">DESSERT</option>
                <option value="cafes">CAFE</option>
                <option value="frite">Frite</option>
                <option value="sauce">Sauces</option>
                <option value="plat">PLAT</option>
                <option value="aromes">Aromes</option>
                <option value="menu">MENU</option>
              </select>
            </div>

            <div class="mb-1">
              <label class="form-label">Désignation*</label>
              <input type="text"  class="form-control" name="name" required="">
            </div>             

              <div class="mb-1">
                <label class="form-label">Type*</label>
                <select class="form-select" name="type" required="" >
                  <option></option><?php
                  foreach ($producttype as $key => $value) {?>

                    <option value="<?=$value->type;?>"><?=$value->nom;?></option><?php 
                  }?>

                  <option value="accompagnements">Accompagnement</option>
                  <option value="supplements">Supplement</option>
                </select>
              </div>

              <div class="mb-1">
                <input type="hidden" name="taille">
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="etating" id="flexRadioDefault1">
                <label class="form-check-label" for="flexRadioDefault1">
                  Ingredients
                </label>
              </div>
            </div>

            <div class="col-sm-12 col-md-6">
              <div class="mb-1">
                <label class="form-label">P. Achat</label>
                <input type="number"  class="form-control" name="prix_achat" value="0" min="0">
              </div>

              <div class="mb-1">
                <label class="form-label">P. Revient</label>
                <input type="number"  class="form-control" name="prix_revient" value="0" min="0">
              </div>
          

              <div class="mb-1">
                <label class="form-label">P. Vente</label>
                <input type="number"  class="form-control" name="prix_vente" value="0" min="0">
              </div>

              <div class="mb-1">
                <label class="form-label">quantite</label>
                <input type="number"  class="form-control" name="quantity" value="0" min="0">
              </div>
            </div>
          </div><?php 
        } ?>  

        <button type="submit" name="ajouter" value="Ajouter" class="btn btn-primary" onclick="return alerteV();">Ajouter</button>
      </fieldset>
    </form>
  </div>
</div>

    </div><?php
  

  }else{

    echo "VOUS N'AVEZ PAS LES AUTORISATIONS REQUISES";
  }

}else{

  header('Location: deconnexion.php');


}?>
  
</body>
</html><?php

require 'footer.php';?>

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
