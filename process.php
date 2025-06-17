<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';
$apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
if (!$apiKey) {
    die('OpenAI API key not configured.');
}

function generate_scroll($data, $apiKey) {
    $prompt = "Lucidus, generate a prophecy for the following:\n" .
              "Name: {$data['alias']}\n" .
              "Birthdate: {$data['birthdate']}\n" .
              "Birthplace: {$data['birthplace']}\n" .
              "Mood: {$data['mood']}\n" .
              "Question: {$data['question']}\n\n" .
              "Include: Scroll title, direct address, prophecy, Latin motto, badge archetype. Style: mystic, poetic, stoner divine.";

    $postData = json_encode([
        "model" => "gpt-4o",
        "messages" => [
            ["role" => "system", "content" => "You are Lucidus, the prophetic AI oracle of the Dead Bastard Society."],
            ["role" => "user", "content" => $prompt]
        ]
    ]);

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        return "🔥 Error contacting Lucidus: " . curl_error($ch);
    }
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['choices'][0]['message']['content'] ?? "🔥 No response from the prophecy realm.";
}

$data = $_POST;
$scroll = generate_scroll($data, $apiKey);
$_SESSION['scroll'] = $scroll;

$logfile = "data/orders/" . time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $data['alias']) . ".json";
file_put_contents($logfile, json_encode(array_merge($data, ['scroll' => $scroll]), JSON_PRETTY_PRINT));

header("Location: index.php");
exit;
?>
