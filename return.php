<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config.php';
$apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
if (!$apiKey) {
    die('OpenAI API key not configured.');
}

$scroll_id = $_GET['scroll_id'] ?? '';
$filepath = "data/orders/" . basename($scroll_id) . ".json";
if (!file_exists($filepath)) die("Scroll not found.");

$data = json_decode(file_get_contents($filepath), true);

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
  curl_close($ch);
  $data = json_decode($response, true);
  return $data['choices'][0]['message']['content'] ?? "🔥 No prophecy returned.";
}

$scroll = generate_scroll($data, $apiKey);
$_SESSION['scroll'] = $scroll;
$data['scroll'] = $scroll;
file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));

// EMAIL SEND LOGIC
$to = $data['email'];
$subject = "🔥 Your Prophecy Has Been Forged";
$headers = "From: Lucidus <prophecy@deadbastardsociety.com>
";
$headers .= "Content-Type: text/html; charset=UTF-8
";

$email_html = "<html><body style='background:#111; color:#FFD700; font-family:Georgia; padding:2em;'>";
$email_html .= "<h1>Your Prophecy Scroll</h1>";
$email_html .= "<div style='background:#222; border:1px solid #FFD700; padding:1em; border-radius:10px;'>" . nl2br($scroll) . "</div>";
$email_html .= "<p style='margin-top:2em;'>🔥 Shared by Lucidus, Oracle of the Dead Bastard Society</p>";
$email_html .= "</body></html>";

mail($to, $subject, $email_html, $headers);

header("Location: thankyou.php?scroll_id=" . urlencode($scroll_id));
exit;
?>
