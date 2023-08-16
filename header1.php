<?php
require '_header.php';?>
<!DOCTYPE html>
<html>
<head>
    <title>Restaurant</title>
    <meta charset="utf-8">
  <!--  <meta name="viewport" content="width=device-width, initial-scale=1.0"/> -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="css/comptabilite.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="css/client.css" type="text/css" media="screen" charset="utf-8">
</head>
<body><?php 
    $pseudo=$_SESSION['pseudo'];

    $products = $DB->querys('SELECT level, statut FROM personnel WHERE pseudo= :PSEUDO',array('PSEUDO'=>$pseudo));?>

  <div class="principalcompta">

    <?php require 'navges.php';?>

    <div class="comptabilite">