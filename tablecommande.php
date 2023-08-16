<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
	<head>
	    <title>Restaurant</title>
	    <meta charset="utf-8">
	  	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	    <link rel="stylesheet" href="css/comptabilite.css" type="text/css" media="screen" charset="utf-8">
	    <link rel="stylesheet" href="css/client.css" type="text/css" media="screen" charset="utf-8">
	    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	</head>
	<body><?php 

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

		if (isset($_POST['ajouter'])) {
		 	$name=$panier->h($_POST['name']);
		 	$place=$panier->h($_POST['place']);
		 	$emplacement=$panier->h($_POST['emplacement']);

		 	$prodverif=$DB->querys("SELECT *FROM tableresto WHERE nom='{$name}' and emplacement='{$emplacement}'");

		 	if (empty($prodverif['nom'])) {

		 		$DB->insert('INSERT INTO tableresto(nom, place, emplacement) values(?, ?, ?)', array($name, $place, $emplacement));?>
		 		<div class="alerteV">Table crée avec succèe!!!</div><?php 

		 	}else{?>
		 		<div class="alertes">Cette Table existe!!!</div><?php
		 	}
		}?>

		<div class="container-fluid">
			<div class="row"><?php 

				require 'navtable.php';?>

				<div class="col p-3 bg-info bg-opacity-5 border border-danger border-3 rounded ">
					<legend class="shadow-lg bg-body rounded text-center">COMMANDES EN-COURS <label style="color: red; font-size: 25px;"><?=$panier->totalCommandeSurplace()[1];?></label></legend>

					<div style="display: flex; flex-wrap: wrap;"><?php 
						foreach ($panier->tableResto() as $value) {?>
							<div style="margin-right: 10px; margin-bottom: 10px;"><?php 
								
								$prodpaie = $DB->querys("SELECT sum(pvente*quantite) as ptotal FROM tablecommande where idtable='{$value->id}' and pseudov='{$_SESSION['idpseudo']}'");

								$totalp= $prodpaie['ptotal'];

								$products = $DB->query("SELECT stock.id as id, tablecommande.id as idv, id_produit, tablecommande.quantite as quantite, stock.nom as nom, pvente, pvente as prix_vente, stock.type as type FROM tablecommande inner join stock on stock.id=tablecommande.id_produit  where idtable='{$value->id}' and pseudov='{$_SESSION['idpseudo']}' order by(tablecommande.id)");

								if (!empty($products)) {?>

									<table class="table table-hover table-bordered table-striped table-responsive text-center shadow-lg bg-body rounded" style="margin-top:0px; box-shadow: 10px 2px 20px;">
										<thead>
											<tr><th colspan="3" style="font-size: 20px; color: blue;"><?=$value->nom;?><a href="printaddition.php?idtable=<?=$value->id;?>" target="_blank" ><img  style=" margin-left: 20px; height: 20px; width: 20px;" src="css/img/pdf.jpg"></a></th></tr>
											<tr>
												<th>Qtite</th>
												<th>Désignation</th>
												<th>Total</th>
											</tr>

										</thead><?php

										$totachat=0; 

										foreach ($products as $key => $product) {


											$totachat+=$product->prix_vente*$product->quantite;?>

											<tbody>
												<tr>
													<td><?=$product->quantite;?></td>

													<td><?=ucfirst(strtolower($product->nom)); ?></td>

													<td style="text-align: right; padding-right: 10px;"><?= number_format($product->prix_vente*$product->quantite,0,',',' '); ?></td>
												</tr>
											</tbody><?php 
										}?>

										<tfoot>
											<tr><th colspan="3"><?= number_format(($totalp),0,',',' '); ?></th></tr>
										</tfoot>
									</table><?php 
								}?>
							</div><?php 
						}?>
						
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
