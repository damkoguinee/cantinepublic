<?php
require '_header.php';
require "vendor/autoload.php";
use Twilio\Rest\Client;

if (isset($_POST['envoyer'])) {

   // Your Account SID and Auth Token from twilio.com/console
   $account_sid = 'AC2474e067e505fb200b0fe886c494a9c9';
   $auth_token = '0f3e59da43cae46f1562561d33442135';
   // In production, these should be environment variables. E.g.:
   // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

   // A Twilio number you own with SMS capabilities
   $twilio_number = "+16812525224";

   $client = new Client($account_sid, $auth_token);
    foreach ($panier->phone as $value) {

        $message=$client->messages->create(
           // Where to send a text message (your cell phone?)
           $_POST['number'],
           array(
               'from' => $twilio_number,
               'body' => $_POST['message']
           )
       );
    }
}

header("Location: sms.php");
