<?php

$url = "https://graph.facebook.com/v22.0/541402772396442/messages";
$accessToken = "EAANZAK1f7IrgBO6ZCrlzZAh9GOD9ElhL0DoR22u4PgKJlZBJlFIobDlMvGPmKo0auxv8l4IC2WsZAuTXrDcFbu4v6X2pc24xZBeEC0ygHV1vpWUBbbxpEZBul1mWZAzlYNctiyTZASvoCIg2VoaarzfxmkpEVZAPuCWVJoIn8cvyrseX29ZCYOHg6IaMpeSzGttlZAjk7ErVARBV5lvxCoE18o8jan5IKH0ZD";

$data = [
    "messaging_product" => "whatsapp",
    "to" => "923205404869",
    "type" => "template",
    "template" => [
        "name" => "hello_world",
        "language" => [
            "code" => "en_US"
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $accessToken,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;

?>
