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

    <div class="row mt-3"><?php 
		if ($_SESSION['level']>0) {?>
			<div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="table.php?ajouttable">Ajouter une table</a></div><?php 
		}?>
	</div>

    <div class="row mt-3"><?php 
        if ($_SESSION['level']>0) {?>
            <div class="col" ><a style="width: 100%; " class="btn btn-light text-center" href="table.php?liste">Liste des tables</a></div><?php 
        }?>
    </div>

</div>