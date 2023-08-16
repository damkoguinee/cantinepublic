<?php
require '_header.php';

if (isset($_GET['user'])) {
	$user=(string) trim($_GET['user']);
	$genre='boissons';
	$genre1='aromes';
	$taille='bouteille';
	if (isset($_GET['stockboisson'])){
		$req=$DB->query('SELECT id, nom as designation, taille FROM stock where nom LIKE ? and genre LIKE ? LIMIT 20',array("%".$user."%",$genre));

	}elseif (isset($_GET['appro'])){
		$req=$DB->query('SELECT id, nom as designation, taille FROM stock where nom LIKE ? and genre LIKE ? and taille LIKE ? LIMIT 20',array("%".$user."%",$genre, $taille));

	}elseif (isset($_GET['approaromes'])){
		$req=$DB->query('SELECT id, nom as designation, taille FROM stock where nom LIKE ? and genre LIKE ? LIMIT 20',array("%".$user."%",$genre1));

	}elseif (isset($_GET['stockgeneral'])) {
		$req=$DB->query('SELECT id, nom as designation, taille FROM stock where nom LIKE ? and genre !=? LIMIT 20',array("%".$user."%",$genre));
	}else{
		$req=$DB->query("SELECT * FROM stock where nom LIKE ?  LIMIT 20",array("%".$user."%"));
	}

	if (isset($_GET['ajout'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="ajout.php?resultidprod=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['stockgeneral'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="stockgeneral.php?recherchgen=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['stockboisson'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="stockboisson.php?recherchgen=<?=$value->id;?>"><div><?=$value->designation.' '.$value->taille;?></div></a><?php
		}
	}elseif (isset($_GET['stockmouv'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="stockmouv.php?desig=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['transfert'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="commandetrans.php?termeliste=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['appro'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="approvisiondebut.php?termeliste=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['approaromes'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="approvisionaromes.php?termeliste=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}elseif (isset($_GET['repartition'])) {

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="repartitioncmd.php?termeliste=<?=$value->id;?>"><div><?=$value->designation;?></div></a><?php
		}
	}else{

		foreach ($req as $key => $value) {?>

			<a style="font-weight: bold; color: white;" href="accueil.php?nom=<?=$value->nom; ?>&idc=<?=$value->id;?>&pv=<?= $value->prix_vente;?>&addplat&type"><div><?=$value->nom;?></div></a><?php
		}
	}
}