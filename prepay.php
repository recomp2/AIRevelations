<?php
session_start();
$data = $_POST;
$scroll_id = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $data['alias']);
$logfile = "data/orders/" . $scroll_id . ".json";
file_put_contents($logfile, json_encode($data, JSON_PRETTY_PRINT));

// Promo code logic
$promo_code  = strtoupper(trim($_POST['promo_code'] ?? ''));
$valid_codes = file_exists('wp-content/dbs-media-hold/promo-codes.safe111') ?
    json_decode(file_get_contents('wp-content/dbs-media-hold/promo-codes.safe111'), true) : [];

if (isset($valid_codes[$promo_code])) {
    if (!is_dir('data/logs')) {
        mkdir('data/logs', 0777, true);
    }
    $log_line = date('c') . " {$scroll_id} {$promo_code}\n";
    file_put_contents('data/logs/promo_usage.log', $log_line, FILE_APPEND);

    $type = $valid_codes[$promo_code]['type'];
    if ($type === 'free') {
        header("Location: return.php?scroll_id={$scroll_id}&promo=1");
        exit;
    } elseif ($type === 'discount') {
        header("Location: " . $valid_codes[$promo_code]['link']);
        exit;
    } elseif ($type === 'twin') {
        header("Location: twin-prophecy.php");
        exit;
    }
}

header("Location: https://www.paypal.com/ncp/payment/GSW42DGHQHLBJ?scroll_id={$scroll_id}");
exit;
?>
