<?php
session_start();
require_once 'config.php';
$ordersDir = 'data/orders';
if (!is_dir($ordersDir)) {
    mkdir($ordersDir, 0777, true);
}
$data = $_POST;
$scroll_id = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $data['alias']);
$logfile = "data/orders/" . $scroll_id . ".json";
file_put_contents($logfile, json_encode($data, JSON_PRETTY_PRINT));
$returnTmpl = $_ENV['PAYPAL_RETURN_URL'] ?? 'return.php?scroll_id={scroll_id}';
$returnUrl = str_replace('{scroll_id}', urlencode($scroll_id), $returnTmpl);
$headerUrl = "https://www.paypal.com/ncp/payment/GSW42DGHQHLBJ?scroll_id={$scroll_id}&return_url=" . urlencode($returnUrl);
header("Location: $headerUrl");
exit;
?>
