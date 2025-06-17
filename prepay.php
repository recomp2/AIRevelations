<?php
session_start();
$data = $_POST;
$scroll_id = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $data['alias']);
$logfile = "data/orders/" . $scroll_id . ".json";
file_put_contents($logfile, json_encode($data, JSON_PRETTY_PRINT));
header("Location: https://www.paypal.com/ncp/payment/GSW42DGHQHLBJ?scroll_id={$scroll_id}");
exit;
?>
