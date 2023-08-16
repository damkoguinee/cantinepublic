

<table style="margin-top: 30px; font-size: 18px;color: black; background: white;">

  <tr>
    <td style="padding-bottom: 20px"><?="************************************Situation de votre compte************************************"; ?></td>
  </tr>

  <tr><?php
    if ($panier->soldeclient()<0) {?>

      <td style="padding-right: 60px;"><?="Madame/Monsieur, à la date du ".date("d/m/Y").", vous nous devez " .number_format(-($panier->soldeclient()),0,',',' '); ?> GNF</td><?php

    }else{?>

      <td style="padding-right: 60px;"><?="Madame/Monsieur, à la date du ".date("d/m/Y").", nous vous devons " .number_format($panier->soldeclient(),0,',',' '); ?> GNF</td><?php
    }?>
  </tr>

  <tr>
    <td style="padding-top: 20px; font-size: 14px;">************<?=$adress['nom_mag']. " vous souhaite une excellente journée**************"; ?></td>
  </tr>

</table>