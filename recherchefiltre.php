<?php
require '_header.php';

if (isset($_GET['user'])) {

	if (isset($_GET['ingredient'])) {
		$user=(string) trim($_GET['user']);
		$req=$DB->query('SELECT stock.id as id, stock.nom as nom, taille FROM stock inner join ingredient on ingredient.nom=stock.id where stock.nom LIKE ? LIMIT 10',array("%".$user."%"));	

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="ingredient.php?ingredient=<?=$value->id;?>"><div><?=$value->nom.' '.$value->taille;?></div></a><?php
		}
	}else{
		$user=(string) trim($_GET['user']);
		$req=$DB->query('SELECT *FROM stock where nom LIKE ? LIMIT 10',array("%".$user."%"));	

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="ingredient.php?clientvip=<?=$value->id;?>"><div><?=$value->nom.' '.$value->taille;?></div></a><?php
		}
	}
	
}

//echo "string";