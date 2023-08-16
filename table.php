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
	  	
	  	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

	  	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	</head>
	<body><?php 

	if (isset($_GET['deletec'])) {

	    $DB->delete('DELETE FROM tableresto WHERE id = ?', array($_GET['deletec']));
	  }

	if (!empty($_SESSION['pseudo'])) {
		if (isset($_GET['surplace'])) {

			$_SESSION['mange']='SURPLACE';

		}elseif (isset($_GET['emporter'])) {

			$_SESSION['mange']='EMPORTER';

		}elseif (isset($_GET['livrer'])) {

			$_SESSION['mange']='LIVRAISON';

		}

		$bdd='tableresto';   

		$DB->insert("CREATE TABLE IF NOT EXISTS `".$bdd."`(
		    `id` int(11) NOT NULL AUTO_INCREMENT,
		    `nom` varchar(100),
		    `place` int(4) DEFAULT '0',
		    `emplacement` varchar(100),
		    `dispo` int(1) DEFAULT '1',
		    PRIMARY KEY (`id`)
		)");

		if (isset($_GET['ajouttable'])) {?>

			<div class="container-fluid mb-3">


				<form class="from" method="POST" action="table.php" >
	          		<fieldset><legend>Ajouter une table</legend>

	          			<div class="row">

	          				<div class="col-sm-12 col-md-4">

	          					<div class="mb-1">
	          						<label class="form-label">Désignation</label>
		                			<input class="form-control" type="text" name="name" required="">
		                		</div>

	          					<div class="mb-1">
	          						<label class="form-label">Nombre de Places</label>
		                			<input class="form-control" type="number" name="place" required="">
		                		</div>

		                		<div class="mb-1">
		                			<label class="form-label">Emplacement</label>
			              			<select class="fom-select" name="emplacement">
			              				<option value="salle 1">Salle1</option>
			              				<option value="salle 2">Salle 2</option>
			              				<option value="terrasse">Terrasse</option>
			              			</select>
			              		</div>	              		
		              		</div>
		              	</div>

		              	<input class="btn btn-light" type="reset" value="Annuler" name="valid" id="form"/><input class="btn btn-primary" type="submit" value="Ajouter" name="ajouter" id="form" onclick="return alerteV();" /></fieldset>
	            	</fieldset>
	          	</form>
	        </div><?php
		}

		if (isset($_POST['ajouter'])) {
		 	$name=$panier->h($_POST['name']);
		 	$place=$panier->h($_POST['place']);
		 	$emplacement=$panier->h($_POST['emplacement']);

		 	$prodverif=$DB->querys("SELECT *FROM tableresto WHERE nom='{$name}' and emplacement='{$emplacement}'");

		 	if (empty($prodverif['nom'])) {

		 		$DB->insert('INSERT INTO tableresto(nom, place, emplacement) values(?, ?, ?)', array($name, $place, $emplacement));?>
		 		<div class="alert alert-success">Table crée avec succèe!!!</div><?php 

		 	}else{?>
		 		<div class="alert alert-warning">Cette Table existe!!!</div><?php
		 	}
		}

		if (isset($_GET['liste'])) {?>

			<div class="container-fluid">
				<div class="row"><?php 

					require 'navtable.php';

					$products = $DB->query('SELECT * FROM tableresto order by(nom)');?>

					<div class="col-sm-12 col-md-6">

						<table class="table table-hover table-bordered table-striped table-responsive">

		        			<thead>

				          <tr>
				            <th class="text-center bg-info" colspan="4">Liste des Tables</th>
				          </tr>

				          <tr>
				            <th>Nom</th>
				            <th>Nbre de Places</th>
				            <th>Emplacement</th>
				            <th></th>
				          </tr>

				        </thead>

				        <tbody><?php

				          foreach ($products as $product ): ?>

				            <tr>
				                      
				              <td><?=ucwords(strtolower($product->nom));?></td>
				              <td><?=$product->place ?></td>
				              <td><?=ucwords(strtolower($product->emplacement)) ; ?></td> 

				              <td><?php if ($product->dispo==1) {?><a onclick="return alerteS();" class="btn btn-danger" href="table.php?deletec=<?=$product->id;?>&liste">Supprimer</a><?php }?></td>

				            </tr>

				          <?php endforeach ?>

				        </tbody>

				      </table>
				  </div>
				</div>
		      
		    </div><?php
		}else{?>

			<div class="container-fluid">

				<div class="row"><?php 

					require 'navtable.php';?>				

					<div class="col p-3 bg-info bg-opacity-5 border border-danger border-3 rounded ">

						<fieldset><legend class="text-center">SALLE DU RESTAURANT</legend>

							<div style="display: flex; flex-wrap: wrap;"><?php 
								$i=1;
								foreach ($panier->tableResto() as $value) {?>

									<a href="accueil.php?surplace&tableresto=<?=$value->id;?>" style="margin: auto;">
										<div style="margin-right: 10px; margin-bottom: 10px; box-shadow: 2px 1px 10px;">
											<div><img src="css/img/table/table1.jpg" width="110" height="100" ></div>
											<div style="text-align: center; font-size: 18px; font-weight: bold; color: blue ;"><?php
											if ($value->dispo==1) {?>
												<img style="border-radius: 50px;" src="css/img/table/voyantvert.jpg" width="10" height="10" ><?php 
											}else{?>
												<img style="border-radius: 50px;" src="css/img/table/voyantrouge.jpg" width="10" height="10" ><?php
											}?><?=ucfirst($value->nom);?></div>				
										</div>
									</a><?php 
									$i++;
								}?>
							</div>
						</fieldset>
					</div>
				</div><?php
			} 
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
                    url: 'recherche_utilisateur.php',
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

<script type="text/javascript">
  function alerteS(){
    return(confirm('Valider la suppression'));
  }

  function focus(){
    document.getElementById('reccode').focus();
  }
</script>
