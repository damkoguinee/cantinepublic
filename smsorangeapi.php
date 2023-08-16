<?php
//Send an SMS using Gatewayapi.com
$url = "https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B{{dev_phone_number}}/requests";
$api_token = "XQS_j3orTaWO0Bz00dk4yp1nGVUaLbi4C1ACMfuIQL2HJN-GunLAzlhHyUWxDDgu";

//Set SMS recipients and content
$recipients = [224628196628, 33753542292];
$json = [
    'sender' => 'ExampleSMS',
    'message' => 'Hello world',
    'recipients' => [],
];
foreach ($recipients as $msisdn) {
    $json['recipients'][] = ['msisdn' => $msisdn];
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

curl -X POST -H "Authorization: Bearer {{access_token}}" \
-H "Content-Type: application/json" \
-d '{"outboundSMSMessageRequest":{ \
        "address": "tel:+{{recipient_phone_number}}", \
        "senderAddress":"tel:+{{dev_phone_number}}", \
        "outboundSMSTextMessage":{ \
            "message": "Hello!" \
        } \
    } \
}' \
"https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B{{dev_phone_number}}/requests"