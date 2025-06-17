<?php
// 🔐 Lucidus Config Loader – Reads from .env for safer Codex or Raspberry use

function load_env_vars($path = '.env') {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

load_env_vars();
?>
