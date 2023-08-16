<?php
require '_header.php'
?><!DOCTYPE html>
<html>

<head>
  <title>GANDAAL Gestion de Scolarite</title>
  <meta charset="utf8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
  <meta content="Page par défaut" name="description">
  <meta content="width=device-width, initial-scale=1" name="viewport">    
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head><?php

  if (!empty($_SESSION['pseudo'])) {

    if (isset($_GET['printnote'])) {?>

      <body  onload="window.print()"><?php

    }else{?>

      <body onload="return focus();"><?php
    }  

    if (empty($_SESSION['prodtype'])) {
      
      $prodtype=$DB->querys('SELECT id, type from repartition  where promo=:promo',array('promo'=>$_SESSION['promo']));

      $_SESSION['prodtype']=$prodtype['type'];

      $typerepart=ucfirst($prodtype['type']);
    }

    if (isset($_POST['groupe'])){

      $prodclass=$DB->querys('SELECT codef from groupe where nomgr=:nom and promo=:promo', array('nom'=>$_POST['groupe'], 'promo'=>$_SESSION['promo']));

      $prodform=$DB->querys('SELECT niveau from formation where codef=:code', array('code'=>$prodclass['codef']));

      $prodtype=$DB->querys('SELECT type from cursus inner join repartition on repartition.codecursus=cursus.codecursus where nom=:code', array('code'=>$prodform['niveau']));

      $prodtype=$prodtype['type'];

      $_SESSION['prodtype']=$prodtype;

      $prodtype=$_SESSION['prodtype'];

      $typerepart=ucfirst($_SESSION['prodtype']);

    }else{

      if (!isset($_GET['note'])){

        $prodtype=$_SESSION['prodtype'];

        $typerepart=ucfirst($_SESSION['prodtype']);
      }
    }

    if (isset($_GET['disci'])){

      $prodform=$DB->querys('SELECT niveau from inscription where matricule=:mat and annee=:promo', array('mat'=>$_GET['disci'], 'promo'=>$_SESSION['promo']));

      $prodtype=$DB->querys('SELECT type from cursus inner join repartition on repartition.codecursus=cursus.codecursus where nom=:code', array('code'=>$prodform['niveau']));

      $prodtype=$prodtype['type'];

      $_SESSION['prodtype']=$prodtype;

      $prodtype=$_SESSION['prodtype'];

      $typerepart=ucfirst($_SESSION['prodtype']);

    }

    if ($_SESSION['prodtype']=='trimestre') {

      if (date('m')=='11' or date('m')=='12' or date('m')=='01' or date('m')=='02' or date('m')=='03') {
        $semcourant='1';
      }elseif (date('m')=='04' or date('m')=='05') {
        $semcourant='2';
      }else{
        $semcourant='3';

      }
    }else{

      if (date('m')=='11' or date('m')=='12' or date('m')=='01' or date('m')=='02' or date('m')=='03') {
        $semcourant='1';
      }else{
        $semcourant='2';

      }
    }

    $products = $DB->querys('SELECT type, matricule, niveau FROM login WHERE pseudo= :PSEUDO',array('PSEUDO'=>$_SESSION['pseudo']));

    if (isset($_SESSION['pseudo'])) {

      if (!isset($_GET['printnote'])) {?>

        <nav class="navbar navbar-expand-lg  fs-5 fw-bold" style="background-color: #253553;">
          <div class="container-fluid">
            <a class="navbar-brand" href="deconnexion.php"><img src="css/img/deconn.jpg" width="30" alt="damko"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                  <a class="nav-link active btn btn-danger text-light m-1" aria-current="page" href="index.php?form">Accueil</a>
                </li>

                <li class="nav-item"><?php 
                  if ($products['type']!='enseignant'){?>
                    <a class="nav-link btn btn-danger text-light m-1" href="formation.php?form&note">Gestion</a> <?php
                  };?>
                </li>

                <li class="nav-item"><?php 
                  if ($products['type']!='enseignant'){?> 
                    <a class="nav-link btn btn-danger text-light m-1" href="ajout_eleve.php?ajoute&note">Scolarite</a><?php
                  };?>
                </li>

                <li class="nav-item"><?php 
                  if ($products['type']!='enseignant'){?> 
                    <a class="nav-link btn btn-danger text-light m-1" href="activitesgestion.php?ideleve">Activités</a><?php
                  };?>
                </li>
                <?php 
                if ($products['type']=='admin' or $products['type']=='fondateur' or $products['type']=='Administrateur Général' or $products['type']=='informaticien' or $products['type']=='Directeur Général' or $products['type']=='proviseur' or $products['type']=='DE/Censeur' or $products['type']=='surveillant Général' or $products['type']=="Conseille a l'éducation" or $products['type']=='coordonateur bloc B' or $products['type']=='Directeur du primaire' or $products['type']=='coordinatrice maternelle' or $products['type']=='secrétaire' or $products['type']=='enseignant' or $products['type']=='bibliothecaire') {?>

                  <li class="nav-item">
                    <a class="nav-link btn btn-danger text-light m-1" href="note.php?note&note">Pédagogie</a>
                  </li><?php 
                }

                if ($products['type']=='admin' or $products['type']=='fondation' or $products['type']=='fondateur' or $products['type']=='Administrateur Général' or $products['type']=='Directeur Général' or $products['type']=='comptable' or $products['type']=='informaticien' or $products['type']=='secrétaire') {?>

                  <li class="nav-item">
                    <a class="nav-link btn btn-danger text-light m-1" href="comptabilite.php?note">Comptabilite</a>
                  </li><?php 
                }

                if ($products['type']!='enseignant'){?>
                  <li class="nav-item">
                  
                    <a class="nav-link btn btn-danger text-light m-1" href="rapport.php?rapport">Statistiques</a>
                  </li><?php
                }

                if ($products['type']!='enseignant'){
                  

                  if ($products['niveau']>4) {?>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-light m-1 " href="csv.php?save">Sauvegarde</a></li><?php

                  }
                }?> 
              </ul>
            </div>
          </div>
        </nav><?php
      }

      $products = $DB->querys('SELECT type, matricule, niveau FROM login WHERE pseudo= :PSEUDO',array('PSEUDO'=>$_SESSION['pseudo']));
      $personnelsup=$products['matricule'];

    }else{

      require 'form_connexion.php';

    }
  }else{
      header("Location: form_connexion.php");
  }?>

  <?php require 'footer.php';?>