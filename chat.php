<?php
// Define the API endpoint and your API key
$apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyDoiEA-jTdQtTie0opn8kX7k0_en7S1dbE';

// Prepare the request payload
$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'who is elon mask.']
            ]
        ]
    ]
];

// Initialize cURL session
$ch = curl_init($apiEndpoint);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);
// echo $response;

// Check for errors
if ($response === false) {
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$decodedResponse = json_decode($response, true);

// Extract the generated content
$generatedContent = $decodedResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from model';
echo "check final res" , $generatedContent;

?>
