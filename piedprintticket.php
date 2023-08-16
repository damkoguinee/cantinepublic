<?php

  $pers1=$DB->querys('SELECT *from personnel where pseudo=:type', array('type'=>$payement['vendeur']));?>
  

  <div  style="margin-top: 20px; color: grey;">
    <label style="margin-left: 90px;"><?=strtoupper($pers1['statut']);?></label>

    <label style="margin-left: 280px;">Le Client</label>
  </div>

  <div class="pied" style="margin-top: 80px; color: grey;">
    <label style="margin-left: 80px;"><?=ucwords($pers1['nom']);?></label>

    <label style="margin-left: 180px;"><?=$panier->adClient($_SESSION['reclient'])[0]; ?></label>
  </div>
</page>