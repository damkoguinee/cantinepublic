<?php
require 'header1.php';
//Send an SMS using Gatewayapi.com
$url = "https://gatewayapi.com/rest/mtsms";
$api_token = "XQS_j3orTaWO0Bz00dk4yp1nGVUaLbi4C1ACMfuIQL2HJN-GunLAzlhHyUWxDDgu";

$prod=array();

//Set SMS recipients and content
//$recipients = [224628196628, 33753542292];

$recipients=$prod;
$json = [
    'sender' => 'damko',
    'message' => 'bonjour, ceci est un test',
    'recipients' => [],
];
foreach ($recipients as $msisdn) {
    if (!empty($msisdn->phone)) {

        $json['recipients'][] = ['msisdn' => $msisdn->code.$msisdn->phone];
    }
}

//Make and execute the http request
//Using the built-in 'curl' library
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch,CURLOPT_USERPWD, $api_token.":");
curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($json));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
print($result);
$json = json_decode($result);
print_r($json->ids);