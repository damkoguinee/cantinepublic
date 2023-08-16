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
<body><?php 
if (!empty($_SESSION['pseudo'])) {?>

	<div class="container-fluid">

		<div class="row m-0 p-0"><?php 
			if (!empty($_SESSION['numcmdmodif'])) {
				require 'navmodif.php';
				$positionmodif="modifventeprod.php";
			}else{
				require 'nav.php';
				$positionmodif="accueil.php";
			}?>

			<div class="col">

				<div class="container-fluid p-0 m-0">
					<div class="row">

						<div class="col-sm-12 col-md-6 border border-5 border-primary m-0 p-0"><?php

							if (!isset($_GET['id'])) {?>

								<div class="container">

									<div class="row">

										<div  class="col fw-bold fs-5 p-1 mb-1 text-white text-center m-auto rounded" style="background-color: #2C1A6C">
											<input class="form-control me-2" id="search-user" type="search" placeholder="Search client" aria-label="Search" >
		                					<div class="text-info" id="result-search"></div>
		                				</div>
											
										<div class="container-fluid">

											<div class="row"><?php

												if (isset($_GET['nomq'])) {

													if ($_GET['nomq']=='BOISSONS') {

														$products = $DB->query('SELECT * FROM stock WHERE genre="boissons" and type="boissons" AND quantity>0 order by(quantity)desc');
													}elseif ($_GET['nomq']=='alcools') {
														$typealcool=$_GET['type'];
														$typeb='boissons';
														$taille=$_GET['taille'];

														if ($taille=='verre') {
															$products = $DB->query("SELECT * FROM stock WHERE genre='{$typeb}' and type='{$typealcool}' and taille='{$taille}' order by(quantity)desc");
														}else{
															$products = $DB->query("SELECT * FROM stock WHERE genre='{$typeb}' and type='{$typealcool}' and taille='{$taille}' and quantity>0 order by(quantity)desc");
														}

														
													}else{

														$products = $DB->query('SELECT * FROM stock WHERE genre="boissons" and type="Bieres" AND quantity>0 order by(quantity)desc');

													}
												}else{

													$products = $DB->query('SELECT * FROM stock WHERE genre="boissons" and type="boissons" and quantity>0 order by(quantity)desc');
												}
												foreach ($products as $plat){?>

													<div class="col pb-2">

										        		<a style="text-decoration: none" href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&addboisson&suplement">
											            	<div class="card m-auto border-10 border-primary" style="width: 9rem; height: 150px;">

											              		<div class="card-bod m-auto text-center fw-bold fs-7"><?= ucwords(strtolower($plat->nom)); ?></div>

											                	<img src="css/img/plat/<?= $plat->id; ?>.jpg" class="card-img-top m-auto" alt=" " style="width: 5rem; height: 4rem"><?php 
											                	if (isset($_GET['taille'])) {
																	if ($_GET['taille']=="bouteille") {?>

																		<div class="card-bod m-auto">
														              		<h5 class="card-title text-center text-danger"><?=$plat->quantity; ?></h5>
														            	</div><?php 
																	}
																	
																}else{?>

																	<div class="card-bod m-auto">
													              		<h5 class="card-title text-center text-info"><?=$plat->quantity; ?></h5>
													            	</div><?php

																}?>

																<div class="card-bod m-auto">
												              		<h5 class="card-title text-center text-danger"><?= number_format($plat->prix_vente,0,',',' '); ?></h5>
												            	</div>

											            	</div>
										          		</a>
										        	</div><?php
												}?>
											</div>
					

										    <div class="row bg-light p-1">
												<div class="col">
													<a class="btn btn-primary" href="dessert.php?menu=<?='';?>">Desserts</a>
												</div>

												<div class="col">
													<a class="btn btn-primary" href="cafe.php?menu=<?='';?>">Cafés</a>
												</div>


												<div class="col">
													<a class="btn btn-primary" href="supplement.php?menu=<?='';?>">Supplements</a>
												</div>											
											
											</div>

										</div>
									</div>
								</div><?php

							}else{

								require 'plat.php';
							}?>
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
	</div><?php 
}else{

    header('Location: deconnexion.php');


}?>
  
</body>
</html><?php

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
                    url: 'rechercheproduit.php',
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
