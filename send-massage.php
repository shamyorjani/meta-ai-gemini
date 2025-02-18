<?php

$url = "https://graph.facebook.com/v22.0/541402772396442/messages";
$accessToken = "EAANZAK1f7IrgBOycNg0jsgQQ4vxlf7LfB3vczyopU5Wujog44ZBaBdgOkjpLIFkVnRAYpMO3rbOM7P0XopIg0ZBuYsepefxllG9fB8DhFyAPZBeOAWithGZBbR7WEgWZBH8nkuP0Nq5hlOyvhGCFz2JyQVtJZA6Ufd9lEZA7KLKK2ZCDiBnZCBGe8gSJUqFyPzmG6LYjXd290dg5ZC5YQT2Pynh9drYaebZA";

$data = [
    "messaging_product" => "whatsapp",
    "to" => "923399009975",
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
