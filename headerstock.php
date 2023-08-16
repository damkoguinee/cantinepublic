<div class="container-fluid text-left bg-info mt-3 mb-3" >

  <div class="row p-4 m-0"><?php

    if ($_SESSION['level']>3) {?>

      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light " href="stockmouv.php?stock">Historiques Ventes Prod</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="stockboisson.php?stockboisson">Stock Boissons</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="stockretirer.php?stockboisson">Boissons Retirées</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="stockrecette.php?stockboisson">Stock Recette</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="stockgeneral.php?stock">Liste des Produits</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="pertes.php?invent">Pertes</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="ingredient.php">Recettes des Plats</a>
      </div>

      <div class="col mt-1">
        <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="ingredientvente.php">Ventes Ingredients</a>
      </div><?php 

      /*

        <div class="col mt-1">
          <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="ingredientmouv.php?stock">Historiques Ventes Ing</a>
        </div><?php
        */ 
    }?>
  </div>
</div>
