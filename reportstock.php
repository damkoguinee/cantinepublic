<?php
require 'header.php';

$quantite=0;
$qtiteint=0;
$nbre=0;

$DB->delete('DELETE FROM stockmouv');

$products=$DB->query('SELECT id, quantite from products');

foreach ($products as $key => $value) {

	$DB->insert('INSERT INTO stockmouv (idstock, numeromouv, libelle, quantitemouv, dateop) VALUES(?, ?, ?, ?, now())', array($value->id, 'report stock', 'entree', $value->quantite));
}



