<?php require 'header1.php';?>

<form name="envoyersms" method="post" action="smsgatewayapi.php" id="naissance">
   <ol>

      <li><label>Numero : </label><input type="text" name="number" /></li>
      <li><label>Message : </label><textarea type='text'  name="message"></textarea></li>
   </ol>
      
      <input type="submit" name="envoyer" value="Envoyer" />
</form>