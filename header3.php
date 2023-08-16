<?php require '_header.php'
?><!DOCTYPE html>
<html lang="fr">

<head>
    <title>restaurant</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="Page par défaut" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    
    
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="choix1.php?retour=<?='vider';?>"><img src="css/img/logo.jpg" width="50" alt="damko"></a>
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
            <a class="nav-link" href="top5.php">Statistiques</a>
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
  </nav>