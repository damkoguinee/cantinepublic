<?php $productsm = $DB->query('SELECT nom FROM menu WHERE type=:id', array('id' => $_GET['type']));

if (empty($productsm)) {

	$productsm=array();

}else{

	$_SESSION['menu']=$productsm;

}

$products = $DB->query('SELECT * FROM stock WHERE genre="menu" AND type=:id', array('id' => $_GET['type']));

foreach ($products as $plat){?>

	<div class="col pb-2">
    	<a style="text-decoration: none" href="accueil.php?nom=<?=$plat->nom; ?>&idc=<?=$plat->id;?>&pv=<?= $plat->prix_vente;?>&addmenu&type=<?=$_SESSION['typemenu'];?>">
        	<div class="card m-auto border-10 border-primary" style="width: 9rem; height: 180px;">

          		<div class="card-bod m-auto text-center fw-bold fs-7"><?= ucwords(strtolower($plat->nom)); ?>
            	</div>

            	<img src="css/img/plat/<?= $plat->id; ?>.jpg" class="card-img-top m-auto" alt=" " style="width: 6rem; height: 6rem">

            	<div class="card-bod m-auto">
              		<h5 class="card-title text-center text-danger"><?= number_format($plat->prix_vente,0,',',' '); ?></h5>
            	</div>

        	</div>
      	</a>
    </div><?php
} ?>