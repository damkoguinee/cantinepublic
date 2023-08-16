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
					<div class="question">QUE VOULEZ-VOUS COMME CAFE ?</div>
					<div class="affiche"><?php
						$products = $DB->query('SELECT * FROM stock WHERE genre="cafe" ');
						foreach ($products as $cafemenu):?>

							<div class="affiche_menu"><a href="addcafemenu.php?id=<?=$cafemenu->id; ?>">
								<div class="designation"><?= $cafemenu->nom; ?></div>

								<div class="picture"><img src="css/img/cafes/<?= $cafemenu->id; ?>.jpg" alt=" "></div>
								<div class="reste"><?= $cafemenu->quantity; ?></div>

								<div class="pricebox">prix inclus</div></a>

							</div>

						<?php endforeach; ?>
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
	<div class="defilement">
		<div class="pub"></div>
	</div>
</body>
</html>
