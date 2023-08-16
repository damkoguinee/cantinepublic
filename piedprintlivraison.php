<?php

  $pers1=$DB->querys('SELECT *from personnel where pseudo=:type', array('type'=>$payement['vendeur']));?>

  <div  style="margin-top: 20px; color: grey;">
    <label style="margin-left: 20px; font-style: italic;"><?=ucwords($pers1['statut']);?></label>

    <label style="margin-left: 150px;">Transporteur</label>

    <label style="margin-left: 100px;">Le Client</label>
  </div>
 

  <div class="pied" style="margin-top: 80px; color: grey;">
    <label style="margin-left: 10px;"><?=ucwords($pers1['nom']);?></label>

    <label style="margin-left: 50px;"></label>

    <label style="margin-left: 210px;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
  </div>

   
</page>