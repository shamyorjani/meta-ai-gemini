<?php
// Initialize chat data array
$chatData = [];

// Load chat data from webhook_log.txt file
if (file_exists('webhook_log.txt')) {
    $webhookContent = file_get_contents('webhook_log.txt');

    // Fix malformed JSON (if multiple JSON objects exist on separate lines)
    $webhookContent = trim($webhookContent);
    $webhookContent = str_replace("}\n{", "},{", $webhookContent);
    $webhookContent = "[" . $webhookContent . "]"; // Wrap in array

    // Decode JSON data
    $dataArray = json_decode($webhookContent, true);

    // Check for JSON decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("JSON decode error: " . json_last_error_msg() . "<br>Content: " . htmlspecialchars($webhookContent));
    }

    // Debugging: Print the decoded array
    echo '<pre>';
    print_r($dataArray);
    echo '</pre>';

    // Process each webhook entry
    foreach ($dataArray as $data) {
        foreach ($data as $entry) {
            if (!empty($entry['entry'])) {
                foreach ($entry['entry'] as $entryItem) {
                    if (!empty($entryItem['changes'])) {
                        foreach ($entryItem['changes'] as $change) {
                            if (!empty($change['value']['messages'])) {
                                foreach ($change['value']['messages'] as $message) {
                                    $chatData[] = [
                                        'from' => $message['from'],
                                        'text' => $message['text']['body'] ?? 'Media Message',
                                        'timestamp' => date('Y-m-d H:i:s', $message['timestamp'])
                                    ];
                                }
                            } elseif (!empty($change['value']['statuses'])) {
                                foreach ($change['value']['statuses'] as $status) {
                                    $chatData[] = [
                                        'from' => $status['recipient_id'],
                                        'text' => 'Message status: ' . ucfirst($status['status']),
                                        'timestamp' => date('Y-m-d H:i:s', $status['timestamp'])
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Conversations</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .chat-container { max-width: 600px; margin: 20px auto; border: 1px solid #ccc; padding: 10px; border-radius: 5px; }
        .message { padding: 8px; border-bottom: 1px solid #ddd; }
        .timestamp { font-size: 12px; color: gray; }
    </style>
</head>
<body>
    <div class="chat-container">
        <h2>Chat Conversations</h2>
        <?php if (!empty($chatData)): ?>
            <?php foreach ($chatData as $chat): ?>
                <div class="message">
                    <strong><?php echo htmlspecialchars($chat['from']); ?>:</strong>
                    <?php echo htmlspecialchars($chat['text']); ?>
                    <div class="timestamp">(<?php echo $chat['timestamp']; ?>)</div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No conversations found.</p>
        <?php endif; ?>
    </div>
</body>
</html>