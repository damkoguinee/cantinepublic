<div class="menu">
	<div class="logo"><a href="choix1.php?retour=<?='vider';?>"><img src="css/img/logo.jpg"></a></div>
	<div class="nav"><a href="choix.php?retour=<?='vider';?>">ACCUEIL</a></div><?php /*
	<div class="nav"><a href="accueil.php?menu">Menu</a></div>*/;?>
	<div class="nav"><a href="modifventeprod.php?menu">Nos Menus</a></div>
	<div class="nav"><a href="accompagnement.php?accompagnements">Accompagnements</a></div>

	<div class="nav"><a href="supplement.php?sup">Supplements</a></div>
	<?php $products = $DB->query('SELECT * FROM menu order by(id)');
	foreach ($products as $menu){
		$menucwords= ucwords(strtolower($menu->nom));

		if ($menu->type=='boissons') {?>
			<div class="nav"><a href="boisson.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?=$menucwords; ?></a></div><?php
		}elseif ($menu->type=='cafes') {?>
			<div class="nav"><a href="cafe.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?='Nos '.$menucwords; ?></a></div><?php
		}elseif ($menu->type=='dessert') {?>
			<div class="nav"><a href="dessert.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?= $menucwords; ?></a></div><?php
		}else{?>
			<div class="nav"><a href="platsimple.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?=$menucwords; ?></a></div><?php
		}
	} ?>

	<div class="nav"><a href="fritem.php?menu">Frites</a></div>

	
</div>