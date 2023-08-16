<?php
require '_header.php';

$json = array('error' => true);
if(isset($_GET['nom'])){
	$product = $DB->query('SELECT id FROM stock WHERE genre="plat" AND nom=:id', array('id' => $_GET['nom']));
	if(empty($product)){
		$json['message'] = "Ce produit n'existe pas";
	}else{
		$panier->addp($product[0]->id);
		$json['error']  = false;
		$json['total']  = number_format($panier->total(),2,',',' ');
		$json['count']  = $panier->count();
		$json['message'] = 'Le produit a bien été ajouté à votre panier';
	}
}else{
	$json['message'] = "Vous n'avez pas sélectionné de produit à ajouter au panier";
}
echo "string";
echo json_encode($json);
//header('Location: boisson.php');
?>