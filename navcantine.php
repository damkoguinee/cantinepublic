<div class="col-sm-12 col-md-2 pb-3" style="background-color: #253553;"> <?php

	if ($products['type']=='admin' or $products['type']=='fondateur' or $products['type']=='comptable' or $products['type']=='informaticien' or $products['type']=='secrétaire' or $products['type']=='bibliothecaire') {?>

		<div class="row mt-3"><div class=" col text-center"><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="accesite.php">Ventes</a></div></div>

		<div class="row mt-3"><div class=" col text-center"><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="accesitepersonnel.php?personnel">Compte Client</a></div></div>

		<div class="row mt-3"><div class=" col text-center"><a style="width: 100%; " class="btn btn-light text-center fw-bold" href="accesitevisiteur.php?visiteur">Visiteurs</a></div></div><?php
	}?>
</div>