<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>Restaurant</title>
    <meta charset="utf-8">
  <!--  <meta name="viewport" content="width=device-width, initial-scale=1.0"/> -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>
<body>
	<div class="container-fluid">

		<div class="row m-0 p-0"><?php 
			if (!empty($_SESSION['numcmdmodif'])) {
				require 'navmodif.php';
				$positionmodif="modifventeprod.php";
				$positionpanier="modifventepanier.php";
			}else{
				require 'nav.php';
				$positionmodif="accueil.php";
				$positionpanier="panier.php";

			}?>
			<div class="col">

				<div class="container-fluid p-0 m-0">

					<div class="row">

						<div class="col-sm-12 col-md-6 border border-5 border-primary m-0 p-0"><?php
							$nomq=strtoupper($_GET['nomq']);?>
							<div  class="col fw-bold fs-5 p-1 mb-1 text-white text-center m-auto rounded" style="background-color: #2C1A6C">QUE VOULEZ-VOUS COMME <?=$nomq; ?> ?</div>

							<div class="container-fluid">

								<div class="row"><?php
				
									if (isset($_GET['type'])) {?>
										<?php $productsm = $DB->query('SELECT * FROM stock WHERE (genre=:genre or genre=:genre1) AND type=:id', array('genre' => "plat", 'genre1' => "frite", 'id' => $_GET['type']));
										if (empty($productsm)) {
											$productsm=array();
										}else{
											
											$_SESSION['menu']=$productsm;
										}
										$products = $DB->query('SELECT * FROM stock WHERE (genre=:genre or genre=:genre1) AND type=:id', array('genre' => "plat" , 'genre1' => "frite", 'id' => $_GET['type']));
										foreach ($products as $plat):?>

											<div class="col pb-2">
									        	<a style="text-decoration: none" href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&addplat&type=<?= $_GET['type'];?>">
									            	<div class="card m-auto border-10 border-primary" style="width: 9rem; height: 180px;">

									              		<div class="card-bod m-auto text-center fw-bold fs-7"><?= ucwords(strtolower($plat->nom)); ?>
									                	</div>

									                	<img src="css/img/plat/<?= $plat->id; ?>.jpg" class="card-img-top m-auto" alt=" " style="width: 6rem; height: 6rem">

									                	<div class="card-bod m-auto">
									                  		<h5 class="card-title text-center text-danger"><?= number_format($plat->prix_vente,0,',',' '); ?></h5>
									                	</div>

									            	</div>
									          	</a>
									        </div>
											
										<?php endforeach ?><?php
									}else{
										require 'plat.php';
									}?>
								</div>

								<div class="row bg-light p-1">
									<div class="col">
										<a class="btn btn-primary" href="accompagnement.php?menu=<?='';?>">Accompagnements</a>
									</div>


									<div class="col">
										<a class="btn btn-primary" href="supplement.php?menu=<?='';?>">Supplements</a>
									</div>

									<div class="col">
										<a class="btn btn-primary" href="boisson.php?menu=<?='';?>">Boissons</a>
									</div>
								
								</div>
							</div>

						</div>

						<div class="col"><?php 
							if (!empty($_SESSION['numcmdmodif'])) {
								require 'modifventepanier.php';
							}elseif ($_SESSION['mange']=='SURPLACE'){
								require 'paniersurplace.php';
							}else{
								require 'panier.php';
							}?>	
						</div>
					</div>
				</div>
			</div>
		</div>
	
</body>
</html>
