<div class="col-sm-12 col-md-2 pb-3 bg-danger">

	<div class="row">

		<div class="col">
            <a style="text-decoration: none;" href="choix1.php?retour=<?='vider';?>">
                <div class="card m-auto" style="width: 5rem; border-radius: 50px;">
                  <img src="css/img/logo.jpg" class="card-img-top m-auto" alt="..." style="width: 5rem; height: 5rem; border-radius: 50px;">
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-3"><div class=" col text-center"><a style="width: 100%; " class="btn btn-light text-center" href="choix.php?retour=<?='vider';?>">ACCUEIL</a></div></div>

    <div class="row mt-3"> 
		<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="cocktails.php">Cocktails</a></div>
	</div>

	<div class="row mt-3"> 
		<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="alcools.php">Nos Alcools</a></div>
	</div><?php 

	$products = $DB->query('SELECT * FROM menu order by(id)');
	foreach ($products as $menu){
		$menucwords= ucwords(strtolower($menu->nom));
		if ($menu->type=='boissons' or $menu->type=='Bieres') {?>

			<div class="row mt-3"> 
				<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="boisson.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?=$menucwords; ?></a></div>
			</div><?php 
		}elseif ($menu->type=='cafes') {?>

			<div class="row mt-3"> 
				<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="cafe.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?=$menucwords; ?></a></div>
			</div><?php 
		}elseif ($menu->type=='dessert') {?>

			<div class="row mt-3"> 
				<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="dessert.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?= $menucwords; ?></a></div>
			</div><?php 
		}else{?>

			<div class="row mt-3"> 
				<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="platsimple.php?type=<?= $menu->type; ?>&nomq=<?= $menu->nom; ?>"><?=$menucwords; ?></a></div>
			</div><?php 

		}
	}?>

	<div class="row mt-3"> 
		<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="accompagnement.php?accompagnements">Accompagnements</a></div>
	</div>

	<div class="row mt-3"> 
		<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="supplement.php?sup">Supplements</a></div>
	</div>
</div>

