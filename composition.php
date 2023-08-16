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
		<?php 
		if (!empty($_SESSION['numcmdmodif'])) {
			require 'navmodif.php';
			$positionmodif="modifventeprod.php";
		}else{
			require 'nav.php';
			$positionmodif="accueil.php";
		}?>
		<div class="choix">
			<?php
			if (!isset($_GET['id'])) {
				if ($_SESSION['type']=='nos tacos') {
					$description='tacos';
				}?>
				<div class="bloc">

					<div>
						<div class="proposition">Composez votre <?=$_SESSION['description'];?></div>
						<div class="affiche"><?php

							if ($_SESSION['type']=='nos tacos') {
								$products = $DB->query('SELECT * FROM stock WHERE type="supplements"  and (nom="viande" or nom="poulet" or nom="merguez" )');
							}else{

								$products = $DB->query('SELECT * FROM stock WHERE type="supplements"');

							}
							foreach ($products as $plat){?>

								<div class="affiche_menu"><a href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?=0;?>&addplat&type=<?='nos tacos';?>">
									<div class="designation"><?= ucwords($plat->nom); ?></div>

									<div class="picture"><img src="css/img/plat/<?= $plat->id; ?>.jpg" alt=" "></div>

									<div class="pricebox"></div></a>

								</div><?php 
							}?>
						</div>
					</div>

					<div style="display: flex; width: 100%;">
						<div style="width: 50%;">
							<a href="accompagnement.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Accompagnements</div></a>
						</div>

						<div style="width: 50%;">
							<a href="supplement.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Supplements</div></a>
						</div>

						<div style="width: 50%;">
							<a href="boisson.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Boissons</div></a>
						</div>
					</div>
				</div><?php
			}else{
				require 'plat.php';
			}?>
		</div>
		<div class="panier">
			<div>
				<?php 
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
</body>
</html>
