<?php
function send($apikey, $number, $message, $expediteur = false, $msg_id = false){ 
   if(!extension_loaded('curl')){
      $response="Extension CURL pas install�e.";
   }else{ 
      $request  = "&apikey=".urlencode($apikey)."&number=".urlencode($number);
      $request .= "&message=".urlencode($message)."&msg_id=".(int)$msg_id;
      $request .= "&expediteur=".urlencode($expediteur);
      
      $url = "https://developer.orange.com/myapps/zIufyQ2bPfAPKVv3";
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $url); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
      curl_setopt($ch, CURLOPT_POST, 1); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, $request); 
      $response = curl_exec($ch); 
      curl_close($ch); 
   }
   return $response; 
} 

$responses = array('OK'     => 'Message envoye avec succes.',
 'ERR_01' => 'APIkey invalide.',
 'ERR_02' => 'Erreur au niveau des param�tres.',
 'ERR_03' => 'Cr�dit insuffisant.',
 'ERR_04' => 'Le num�ro du destinataire est invalide.'
);
             
if (!empty($_POST['envoyer']))
{  $expi='+224628196628';
   $apikey = "zIufyQ2bPfAPKVv3"; # votre APIkey   
   $r=send($apikey,$_POST['number'],$_POST['message'],$expi);   
   echo $responses[$r];
}
?>