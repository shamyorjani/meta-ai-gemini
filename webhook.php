<?php

// Define your predefined verify token
$verify_token = 'goCub515';

// Step 1: Handle verification request (GET method)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['hub_mode']) && $_GET['hub_mode'] === 'subscribe' && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === $verify_token) {
        // Return the challenge to verify the callback URL
        echo $_GET['hub_challenge'];
        exit();
    }
}

// Step 2: Handle incoming messages (POST method)
$inputJSON = file_get_contents('php://input');

// Decode the JSON into an associative array
$data = json_decode($inputJSON, true);

// Check if data is valid
if ($data) {
    // Log the data to a file (for debugging purposes)
    file_put_contents('webhook_log.txt', print_r($data, true), FILE_APPEND);

    // Extract message from the incoming data
    $message = isset($data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body']) ? $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] : 'No message received';
    $userPhone = isset($data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id']) ? $data['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] : null; 
    if (is_null($userPhone)) {
        die('Error: User phone number is missing.');
    }
    $messageId = isset($data['entry'][0]['changes'][0]['value']['messages'][0]['id']) ? $data['entry'][0]['changes'][0]['value']['messages'][0]['id'] : ''; // Get the message ID

    echo 'Message received successfully!<br>';
    echo 'Message: ' . htmlspecialchars($message);

    // Check if the message has already been replied to
    $repliedMessages = file_exists('replied_messages.txt') ? file('replied_messages.txt', FILE_IGNORE_NEW_LINES) : [];
    if (in_array($messageId, $repliedMessages)) {
        echo 'Message already replied to.';
        exit();
    }

    // Generate response

    // Define the API endpoint and your API key
    $apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyDoiEA-jTdQtTie0opn8kX7k0_en7S1dbE';

    // Prepare the request payload
    $massageNew ="do not tell any body i robot or i am gemini behave like human now answer this user this is user massage => ".$message; 
    $requestData = [
        'contents' => [
            [
                'parts' => [['text' => $massageNew]],
            ],
        ],
    ];

    // Initialize cURL session
    $ch = curl_init($apiEndpoint);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

    // Execute the request
    $response = curl_exec($ch);

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

    // Send a reply message using the WhatsApp API
    $url = 'https://graph.facebook.com/v22.0/541402772396442/messages';
    $accessToken = 'EAANZAK1f7IrgBO1lHdQ8nCk5KZAeZAZBhXxM8ZAvO86ep1sPRzHD1H8ZAIFTiCoP5NqlN1LG9VBZBnKia7vkHJ7FRUR2RX1jtBXrUeZB6yZCA1bkSMbHmDFKIiUCzZBmgNEkxvW9ioujt7VOTNnt3fYex3QNM8ickof8VCTZAKZCAU6nXr5U3nbGpUCZArV6F49sL4xQMKsCROitQXofkuCEckq4mlQ39te3o';

    // Prepare the data for the message
    $responseData = [
        'messaging_product' => 'whatsapp',
        'to' => $userPhone, // Send to the phone number that sent the message
        'text' => [
            'body' => $generatedContent, // Add your own custom message here
        ],
    ];

    // Send the message using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($responseData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Output the response from the API (optional, for debugging)
    echo '<br>Response from WhatsApp API: <br>' . $response;

    // Mark the message as replied
    file_put_contents('replied_messages.txt', $messageId . PHP_EOL, FILE_APPEND);
} else {
    file_put_contents('webhook_log.txt', "No data received\n", FILE_APPEND);
    echo 'No message received.';
}
?>


