<?php

require_once __DIR__.'/vendor/autoload.php';

use informagenie\OrangeSDK;

if(!empty($_POST)){
	$datas = array_map('htmlspecialchars', $_POST);
	
	$credential = [
		'clientId' => 'Xrnh8f0GfbB9vzBOfcPQf8EPCw6Q8GkD',
		'clientSecret' => 'w3joTKyAGqHM7Pv0'
	];

	$osms = new OrangeSDK($credential);
	//$osms = new Osms\Osms($credential);

	$response = $sms->message('Hello world !')
     ->from(24380000000)
     ->as('Informagenie')
     ->to(243970000000)
     ->send();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>Envoie sms via Orange</title>
</head>
<body>
	<div class="container">
		<form method="post">
			<div class="form-group">
				<label for="tel">Phone</label>
				<input type="tel" name="tel" />
			</div>
			<div class="form-group">
				<label for="message">Texte</label>
				<textarea name="content"></textarea>
			</div>
			<button type="submit">Envoyer le mesasge</button>
		</form>
	</div>
</body>
</html>