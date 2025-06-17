<?php
session_start();
$scroll_id = $_GET['scroll_id'] ?? '';
$scroll = '';

if ($scroll_id && file_exists("data/orders/" . basename($scroll_id) . ".json")) {
  $data = json_decode(file_get_contents("data/orders/" . basename($scroll_id) . ".json"), true);
  $scroll = $data['scroll'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Prophecy Has Been Forged</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="container">
    <h1>🔥 Your Prophecy Scroll</h1>
    <?php if ($scroll): ?>
      <div style="margin-top:2em; background:#222; padding:1.5em; border-radius:10px; border:2px solid #FFD700;">
        <?= nl2br($scroll) ?>
      </div>
    <?php else: ?>
      <p>No scroll found. Something went wrong.</p>
    <?php endif; ?>
    <p style="margin-top:2em;"><a href="index.php" style="color:#FFD700;">Return for another scroll</a></p>
  </div>
</body>
</html>
