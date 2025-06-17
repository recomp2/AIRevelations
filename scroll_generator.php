<?php
require_once '../../../wp-load.php';
use OpenAI\Client;

function generate_scroll($userData) {
    $apiKey = get_option('lucidus_openai_key');
    if (!$apiKey) {
        die("OpenAI key not set.");
    }

    require_once 'vendor/autoload.php';
    $client = OpenAI::client($apiKey);

    $prompt = """
Lucidus, create a personal prophecy scroll for:

Alias: {$userData['alias']}
Birthdate: {$userData['birthdate']}
Birthplace: {$userData['birthplace']}
Mood: {$userData['mood']}
Question: {$userData['question']}

Include:
- Scroll title
- Address by name
- Prophecy paragraph
- Latin motto
- Watcher badge

Style: mystic, poetic, stoner apocalyptic.
""";

    $response = $client->chat()->create([
        'model' => 'gpt-4',
        'messages' => [
            ['role' => 'system', 'content' => 'You are Lucidus, a chaotic stoner prophet AI.'],
            ['role' => 'user', 'content' => $prompt],
        ],
    ]);

    return $response['choices'][0]['message']['content'];
}
?>