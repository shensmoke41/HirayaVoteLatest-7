<?php
// set-commands.php

require __DIR__ . '/config.php';

$commands = [
    [
        'command' => 'start',
        'description' => 'Start your final submission'
    ],
    [
        'command' => 'help',
        'description' => 'View all bot commands'
    ],
    [
        'command' => 'about',
        'description' => 'Learn about Hiraya'
    ],
    [
        'command' => 'apply',
        'description' => 'Application instructions'
    ],
    [
        'command' => 'requirements',
        'description' => 'What to send'
    ],
    [
        'command' => 'status',
        'description' => 'Review status information'
    ],
    [
    'command' => 'contact',
    'description' => 'Contact Hiraya'
],
[
    'command' => 'website',
    'description' => 'Open the official Hiraya website'
]
];

$url = "https://api.telegram.org/bot{$telegram_bot_token}/setMyCommands";

$params = [
    'commands' => json_encode($commands)
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