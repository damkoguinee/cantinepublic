<div class="menuges">
	<div class="logo"><a href="choix1.php?retour=<?='vider';?>"><img src="css/img/logo.jpg"></a></div>
	<div class="nav"><a href="choix1.php?retour=<?='vider';?>">Accueil</a></div>
	<div class="nav"><a href="comptasemaine.php?bilan">Comptabilité</a></div>
	<?php if ($_SESSION['level']>=6) {?><div class="nav"><a href="dec.php">Sorties</a></div><?php }?>
	<div class="nav"><a href="stockgeneral.php">Stock</a></div>
</div>