<?php
require '_header.php';


$prodenseig=$DB->query('SELECT * from personnel');

foreach ($prodenseig as $value) {

	$mdp=password_hash($value->mdp, PASSWORD_DEFAULT);

	$DB->insert("UPDATE personnel SET mdp='{$mdp}' where id='{$value->id}'")
	;
}