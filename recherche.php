<?php
require 'header1.php';?>

<style type="text/css">

.search{
  display: flex;
  flex-wrap: nowrap;
}
  .history{
    width: 50%;
    margin-top: 30px;
  }
 .ticket{
    margin-right: 50px;
    width: 50%;
    height:100%;
  }

  table {
    width: 100%;
    color: #717375;
    font-family: helvetica;
    line-height: 6mm;
    border-collapse: collapse;
  }
  
  .border th {
    border: 2px solid #CFD1D2;
    padding: 0px;
    font-weight: bold;
    font-size: 16px;
    color: black;
    background: white;
  }
  .border td {
    line-height: 6mm;
    border: 0px solid #CFD1D2;    
    font-size: 16px;
    color: black;
    background: white;
    text-align: center;}
</style><?php

if (isset($_POST['rechercher'])) {
  $_SESSION['numcmd']=$_POST['rechercher'];
}?>

<fieldset style="margin-top: 10px;"><legend>Voulez-vous</legend>
  <div class="choixg">
    <div class="optiong">
      <a href="ticket_pdf.php?ticketrechercher" target="_blank"><div class="descript_optiong">Imprimer le ticket</div></a>
    </div>

    <div class="optiong">
        <a href="choix.php?indexr"><div class="descript_optiong">Allez dans vente</div></a>
    </div> 

    <div class="optiong">
      <a href="choix1.php?ajouterc"><div class="descript_optiong">Accueil</div></a>
    </div>
  </div>
</fieldset>
</body>

<script>
function suivant(enCours, suivant, limite){
  if (enCours.value.length >= limite)
  document.term[suivant].focus();
}

function focus(){
document.getElementById('reccode').focus();
}

function alerteS(){
  return(confirm('Confirmer la suppression?'));
}

function alerteM(){
  return(confirm('Confirmer la modification'));
}

function alerteF(){
  return(confirm('Confirmer la femeture de la caisse'));
}
</script>

