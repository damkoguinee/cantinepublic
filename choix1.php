<?php require '_header.php';?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant</title>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta content="Page par défaut" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="css/formulaire.css" type="text/css" media="screen" charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>

  <style type="text/css">
    a {text-decoration: none;

    }
  </style>

    <?php

    if (isset($_SESSION['pseudo'])){?>

        <div class="container-fluid">

          <div class="alert alert-danger text-center fs-5 fw-bold">Journée en-cours <?=(new dateTime($_SESSION['datev']))->format("d/m/Y");?> <label class="text-success ml-5">Personnel Connecté: <?=$panier->nomPersonnel($_SESSION['idpseudo'])[0];?></label></div>

            <div class="row align-items-center pt-5 pb-5 bg-info" style="margin: auto; margin-top: 10rem;">



                <div class="col mt-1">
                    <a href="choix.php">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/achat.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto">
                            <h5 class="card-title">Ventes</h5>
                          </div>
                        </div>
                    </a>
                </div>

                <div class="col mt-1">
                    <a href="paiecreditclient.php">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/gestion.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto">
                            <h5 class="card-title">Credits</h5>
                          </div>
                        </div>
                    </a>
                </div>

                <div class="col mt-1">
                    <a href="livraisonachat.php?nonlivre">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/livraison.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto">
                            <h5 class="card-title">Livraison</h5>
                          </div>
                        </div>
                    </a>
                </div>

                <div class="col mt-1">
                    <a href="commande.php">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/commande.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto" style="width: 8rem;">
                            <h5 class="card-title">Approvision..</h5>
                          </div>
                        </div>
                    </a>
                </div><?php 

                if ($_SESSION['level']>1) {?>

                    <div class="col mt-1">
                        <a href="stockmouv.php">
                            <div class="card" style="width: 8rem;">
                              <img src="css/img/stock.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                              <div class="card-bod m-auto" style="width: 8rem;">
                                <h5 class="card-title">Gestion Stock</h5>
                              </div>
                            </div>
                        </a>
                    </div>

                    <div class="col mt-1">
                        <a href="ajout.php">
                            <div class="card" style="width: 8rem;">
                              <img src="css/img/ajout.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                              <div class="card-bod m-auto" style="width: 8rem;">
                                <h5 class="card-title" style="text-align: center;">Ajout Produit</h5>
                              </div>
                            </div>
                        </a>
                    </div>

                    <div class="col m-auto mt-1">
                        <a href="client.php">
                            <div class="card" style="width: 8rem;">
                              <img src="css/img/client.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                              <div class="card-bod m-auto" style="width: 8rem;">
                                <h5 class="card-title" style="text-align: center;">Clients</h5>
                              </div>
                            </div>
                        </a>
                    </div><?php 

                    if ($_SESSION['level']>6) {?>

                      <div class="col m-auto mt-1">
                          <a href="personnel.php?enseig">
                              <div class="card" style="width: 8rem;">
                                <img src="css/img/personnel.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                                <div class="card-bod m-auto" style="width: 8rem;">
                                  <h5 class="card-title" style="text-align: center;">Personnels</h5>
                                </div>
                              </div>
                          </a>
                      </div><?php 
                    }?>

                    <div class="col m-auto mt-1">
                        <a href="dec.php">
                            <div class="card" style="width: 8rem;">
                              <img src="css/img/retrait.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                              <div class="card-bod m-auto" style="width: 8rem;">
                                <h5 class="card-title" style="text-align: center;">Sorties</h5>
                              </div>
                            </div>
                        </a>
                    </div>

                    <div class="col m-auto mt-1">
                        <a href="bulletin.php?client">
                            <div class="card" style="width: 8rem;">
                              <img src="css/img/compte.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                              <div class="card-bod m-auto" style="width: 8rem;">
                                <h5 class="card-title" style="text-align: center;">Comptes</h5>
                              </div>
                            </div>
                        </a>
                    </div><?php
                }?>

                <div class="col m-auto mt-1">
                    <a href="comptasemaine.php?bilan">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/compta.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto" style="width: 8rem;">
                            <h5 class="card-title" style="text-align: center;">Comptabilite</h5>
                          </div>
                        </div>
                    </a>
                </div>

                <div class="col m-auto mt-1">
                    <a href="deconnexion.php">
                        <div class="card" style="width: 8rem;">
                          <img src="css/img/deconn.jpg" class="card-img-top m-auto" alt="..."style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto" style="width: 8rem;">
                            <h5 class="card-title" style="text-align: center;">Déconnexion</h5>
                          </div>
                        </div>
                    </a>
                </div>
            </div>
        </div><?php        

    }else{

        header("Location: deconnexion.php");

    }?>
    
</body>
</html>