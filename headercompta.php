<div class="container-fluid text-left bg-info mt-3 mb-3" >

  <div class="row p-4 m-0">

    <div class="col mt-1">
      <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light " href="comptasemaine.php?semaine=<?='semaine';?>&bilan">
      Bilan</a>
    </div>

    <div class="col mt-1">
      <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="facturations.php">Facturations</a>
    </div>

    <div class="col mt-1">
      <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="produitvendus.php?produit">Produits Vendus</a>
    </div>

    <div class="col mt-1">
      <a  style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="produitvendusdet.php?detailv">Détail des Ventes</a>
    </div><?php

    if ($_SESSION['level']>6) {?>

      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="inventairegeneral.php?invent">Solde</a>
      </div>

      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="updatevente.php?invent">Ventes Modifiées</a>
      </div>

      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="deletevente.php?invent">Historiques Sup</a>
      </div>

      <?php
    }?>
        
    <div class="col mt-1">
      <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="comptasemaine.php?supclot" onclick="return alerteSC();">Annuler la fermeture</a>
    </div><?php

    if (isset($_POST['fcaisse'])) {?>
      
      <div class="col mt-1">
        <a style="font-weight: bold; width: 130px; height: 70px; padding-top: 10px;" class="btn btn-info bg-light" href="dump.php?fermer" onclick="return alerteC();">
        <div class="descript_optiong" style="color: red;">Fermer</div></a>
      </div><?php
    }?>
  </div>
</div>