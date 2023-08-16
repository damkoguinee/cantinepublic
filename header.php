<?php
require '_header.php';

if (isset($_POST['surplace'])) {
	$_SESSION['mange']=$_POST['surplace'];

}else{
}
?>
<!DOCTYPE html>
<html>
	<head>
	    <title>Restaurant</title>
	    <meta charset="utf-8">
	  <!--  <meta name="viewport" content="width=device-width, initial-scale=1.0"/> -->
	    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
	    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	</head>
	<body>
		
		<div class="container-fluid">

			<div class="row m-0 p-0"><?php 
				if (!empty($_SESSION['numcmdmodif'])) {
					require 'navmodif.php';
				}else{
					require 'nav.php';
				}?>