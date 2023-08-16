<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
	<head>
	    <title>Restaurant</title>
	    <meta charset="utf-8">
	    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	    <meta content="Page par défaut" name="description">
	    <meta content="width=device-width, initial-scale=1" name="viewport">
	  	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	  	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">


	</head>
	<body><?php

		if (isset($_GET['retour'])) {
			$_SESSION['positionpl']=array();
			$_SESSION['positionem']=array();
			unset($_SESSION['mange']);

		}else{

		}?>

		<div class="container-fluid">
			<div class="row">

				<div class="col"><a class="btn btn-success" href="choix1.php">Gestion</a></div>	

				<div class="col">
					<form class="d-flex" role="search" method="post" action="recherche.php">
						<input class="form-control me-2"  type = "search" name = "rechercher" placeholder="rechercher !!!" aria-label="Search">

						<button class="btn btn-outline-success" name="s" type="submit">Search</button>
	                    
	            	</form>
            	</div>
			</div>

			<div class="row mt-2">
                <div class="card m-auto" style="width: 8rem;">
                  <img src="css/img/logo.jpg" class="card-img-top m-auto" alt="..." style="width: 8rem; height: 8rem">
                </div>
            </div>

            <div class="row"><?php

				if (date('H')>16) {?>

					<div class="bonj">BONSOIR</div><?php

				}else{?>

					<div class="bonj">BONJOUR</div><?php
				}?>

				<div class="manger">OÙ VOULEZ-VOUS MANGER?</div>

			</div>
			
			<div class="row bg-danger pt-3 pb-3 m-auto">

				<div class="col">
          <a style="text-decoration: none" href="table.php?surplace">
              <div class="card m-auto" style="width: 8rem;">
                <img src="css/img/surplace.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                <div class="card-bod m-auto">
                  <h5 class="card-title">SURPLACE</h5>
                </div>
              </div>
          </a>
        </div>

        <div class="col">
            <a style="text-decoration: none" href="accueil.php?emporter">
                <div class="card m-auto" style="width: 8rem;">
                  <img src="css/img/emporter.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                  <div class="card-bod m-auto">
                    <h5 class="card-title">À EMPORTER</h5>
                  </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a style="text-decoration: none" href="accueil.php?livrer">
                <div class="card m-auto" style="width: 8rem;">
                  <img src="css/img/livraison.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                  <div class="card-bod m-auto">
                    <h5 class="card-title">À LIVRER</h5>
                  </div>
                </div>
            </a>
        </div>

				<div class="col">
                    <a style="text-decoration: none" href="tablecommande.php?surplace">
                        <div class="card m-auto" style="width: 8rem;">
                          <img src="css/img/table/commandes.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem">
                          <div class="card-bod m-auto">
                            <h5 class="card-title">COMMANDES</h5>
                          </div>
                        </div>
                    </a>
                </div>
			</div>

		</div>

	</body>
</html>
