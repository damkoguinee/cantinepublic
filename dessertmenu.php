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
</head>
<body>
	<div class="principal"><?php 
		if (!empty($_SESSION['numcmdmodif'])) {
			require 'navmodif.php';
			$positionmodif="modifventeprod.php";
		}else{
			require 'nav.php';
			$positionmodif="accueil.php";
		}?>
		<div class="choix">
			
			<?php 
			if (!isset($_GET['id'])) {?>
				<div class="bloc">
					<div  class="question">QUE VOULEZ-VOUS COMME DESSERT ?</div>
					<div class="affiche"><?php
						$products = $DB->query('SELECT * FROM stock WHERE genre="dessert" ');
						foreach ($products as $dessert):?>

							<div class="affiche_menu"><a href="adddessertmenu.php?id=<?=$dessert->id; ?>">
								<div class="designation"><?= $dessert->nom; ?></div>

								<div class="picture"><img src="css/img/dessert/<?= $dessert->id; ?>.jpg" alt=" "></div>
								<div class="reste"><?= $dessert->quantity; ?></div>

								<div class="pricebox">prix inclus</div></a>

							</div>

						<?php endforeach; ?>
					</div>
					<a href="boissonm.php"><div style="width: 30%; margin-top: 30px;" class="question">RETOUR </div></a>
				</div><?php
			}else{
				require 'plat.php';
				
			}?>
		</div>
		<div class="panier">
			<div>
				<?php require 'panier.php' ?>;	
			</div>
		</div>
	</div>
	<div class="defilement">
		<div class="pub"></div>
	</div>
</body>
</html>
