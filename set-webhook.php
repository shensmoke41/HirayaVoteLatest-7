<?php
// set-webhook.php

require __DIR__ . '/config.php';

$webhookUrl = rtrim($site_url, '/') . "/telegram-bot.php";

$url = "https://api.telegram.org/bot{$telegram_bot_token}/setWebhook";

$params = [
    'url' => $webhookUrl,
    'allowed_updates' => json_encode(['message'])
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;
?>