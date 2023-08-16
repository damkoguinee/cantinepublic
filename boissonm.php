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
					<div>
						<div class="proposition">QUE VOULEZ-VOUS COMME BOISSON ?</div>
						<div class="affiche"><?php
							$products = $DB->query('SELECT * FROM stock WHERE genre="boissons" AND quantity>0 ');
							foreach ($products as $plat){?>

								<div class="affiche_menu"><a href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= 0;?>&addmenub&type=<?=$_SESSION['typemenu'];?>&gratuit">
									<div class="designation"><?= ucwords($plat->nom); ?></div>

									<div class="picture"><img src="css/img/plat/<?= $plat->id; ?>.jpg" alt=" "></div>

									<div class="pricebox">Prix Inclus</div></a>

								</div><?php 
							}

							if ($_SESSION['payant']!='gratuit') {

								$products = $DB->query('SELECT * FROM stock WHERE genre="boissons" AND quantity>0 ');
								foreach ($products as $plat){?>

									<div class="affiche_menu"><a href="<?=$positionmodif;?>?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&addmenub&type=<?=$_SESSION['typemenu'];?>&gratuit">
										<div class="designation"><?= ucwords($plat->nom); ?></div>

										<div class="picture"><img src="css/img/plat/<?= $plat->id; ?>.jpg" alt=" "></div>

										<div class="pricebox"><?=number_format($plat->prix_vente,0,',',' ');?></div></a>

									</div><?php 
								}
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
							<a href="dessert.php?menu=<?='';?>"><div style="margin-top: 30px;" class="proposition">Desserts</div></a>
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
				}else{
					require 'panier.php';
				}?>		
			</div>
		</div>
	</div>
</body>
</html>
