<?php require 'header1.php';

$prod = $DB->query('SELECT * FROM stock');

foreach ($prod as $key => $value) {

	$id=$value->id;	

	if (!empty($value->qtiteint)) {

		$prodverif = $DB->querys("SELECT * FROM ingredient where nom='{$id}'");

		if (empty($prodverif['id'])) {

			$DB->insert('INSERT INTO ingredient (nom, qtite) VALUES (?, ?)', array($id, 0));
		}
	}
}