<?php
require '_header.php'
?><!DOCTYPE html>
<html lang="fr">

<head>
    <title>restaurant</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="Page par défaut" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="css/comptabilite.css" type="text/css" media="screen" charset="utf-8">
    
    
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="choix1.php?retour=<?='vider';?>"><img src="css/img/deconn.jpg" width="30" alt="damko"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="choix1.php?retour=<?='vider';?>">Accueil</a>
          </li><?php if ($_SESSION['level']>=6) {?>
            <li class="nav-item">
              <a class="nav-link" href="dec.php">Sorties</a>
            </li><?php 
          }?>
          <li class="nav-item">
            <a class="nav-link" href="stockgeneral.php">Gestion/Stock</a>
          </li>
          
          
          <li class="nav-item">
            <a class="nav-link" href="comptasemaine.php?bilan">Comptabilité</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="ajout.php">Nouveau Produit</a>
          </li>
        </ul>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
      </div>
    </div>
  </nav><?php

  require 'headercompta.php';

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
    $tot_revient=0;
    $stock = $DB->query('SELECT * FROM stock');

    foreach ($stock as $product){

      $tot_achat+=$product->prix_achat*$product->quantity;
      $tot_vente+=$product->prix_vente*$product->quantity;

      $tot_revient+=$product->prix_revient*$product->quantity;

    }?>

  <form id='liquide' method="POST" action="inventairegeneral.php">

    <div class="tbord">      

      <div class="casem">

        <div class="descriptd">ARGENT LIQUIDE</br>

          <input class="descriptmf" type="float" name="liquide" onchange="document.getElementById('liquide').submit()" value="<?=number_format($liquide,0,',',' ');?>">
        </div>
      </div>

      <div class="descripts">+</div>
    
      <div class="casem">
        <div class="descriptd">MONTANT CAISSE
          <div class="descriptm"><?=number_format($panier->montantCompte(1),0,',',' ');?></div>
        </div>
      </div>

      <div class="descripts">+</div>
    
      <div class="casem">
        <div class="descriptd">MONTANT BANQUE
          <div class="descriptm"><?=number_format($panier->soldeBanque(),0,',',' ');?></div>
        </div>
      </div>
    

      <div class="descripts">+</div>
    
      <div class="casem">
        <div class="descriptd">MONTANT STOCK
          <div class="descriptm"><?=number_format($tot_revient,0,',',' ');?></div>
        </div>
      </div>

        <div class="descripts">+</div>
        <div class="casem">
          <div class="descriptd">SOLDE CREDITS
            <div class="descriptm" style="background-color: red;"><?= number_format((-1)*$panier->soldecredit(),0,',',' '); ?></div>
          </div>
        </div>
        
      <div class="casem">
        <div class="descriptd">DEPENSES
          <div class="descriptm"><?=number_format($panier->totdepense($_SESSION['date']),0,',',' ') ; ?></div>
        </div>
      </div>

    </div>
    <div class="descripts">| |</div>

    <div class="casem" style="display: flex; margin: auto; margin-top: 20px;"><?php

      $chiffrea=$liquide+$panier->montantCompte(1)+$panier->soldeBanque()+$tot_revient+$panier->soldecredit();?>

      <div style="margin-left:25%;">

        <div class="descriptd">SOLDE COMPTE <?= date("Y");?>
          <div class="descriptm"><?=number_format($chiffrea,0,',',' ') ; ?></div>
        </div>

      </div>
    </div>
  </form>                

  <form id="chiffrea" action="inventairegeneral.php" method="POST"><?php

    if (isset($_POST['chiffrea'])) {

      $chiffreaa=$_POST['chiffrea'];
    
    }else{
      $chiffreaa=0;
    }?>              

    <div class="tbord">                

      <div class="casem">
        <div class="descriptd">SOLDE COMPTE <?= date("Y")-1;?></br>
          <input class="descriptmf" type="float" name="chiffrea" onchange="document.getElementById('chiffrea').submit()" value="<?=number_format($chiffreaa,0,',',' ');?>">
        </div>
      </div>

      <div class="casem" style="margin-left: 20px;"><?php            

        if (!isset($_POST['chiffrea'])) {

        }else{

          if (($chiffrea-$chiffreaa)<0) {?>
            
            <div class="descriptd">MANQUE NET <?= date("Y");?>
            <div class="descriptmbn"><?=number_format($chiffrea-$chiffreaa,0,',',' ');?></div><?php

          }else{?>

            <div class="descriptd">BENEFICE NET <?= date("Y");?>
            <div class="descriptmbp"><?=number_format($chiffrea-$chiffreaa,0,',',' ');?></div><?php
          }
        }?>

      </div>
    </div>
  </form>