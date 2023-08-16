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
	<div class="principal">
		<?php require 'nav.php';?>
		<div class="choix">
			<?php
			if (!isset($_GET['id'])) {?>
				<div class="bloc">
					<div class="question">Selectionnez les frites ?</div>
					<div class="affiche"><?php
						$products = $DB->query('SELECT * FROM stock WHERE genre="frite" ');
						foreach ($products as $plat){?>

							<div class="affiche_menu"><a href="accueil.php?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&gratuit">
								<div class="designation"><?= ucwords($plat->nom); ?></div>

								<div class="picture"><img src="css/img/plat/<?= $plat->id; ?>.jpg" alt=" "></div>

								<div class="pricebox"><?=number_format($plat->prix_vente,0,',',' ');?></div></a>

							</div><?php
						}?>
					</div>
					<a href="accueil.php?menu=<?='';?>"><div style="width: 30%; margin-top: 30px;" class="question">RETOUR </div></a>
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
</body>
</html>
