<?php
// telegram-bot.php

date_default_timezone_set('Asia/Manila');
require __DIR__ . '/config.php';

$rawUpdate = file_get_contents("php://input");
$update = json_decode($rawUpdate, true);

if (!$update) {
    echo "NO UPDATE";
    exit;
}

function tg_api($method, $params = []) {
    global $telegram_bot_token;

    $url = "https://api.telegram.org/bot{$telegram_bot_token}/{$method}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function safe_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function bot_reply($chatId, $text) {
    tg_api('sendMessage', [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ]);
}

function bot_sessions_dir() {
    $dir = __DIR__ . '/bot_sessions';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    return $dir;
}

function bot_session_file($chatId) {
    return bot_sessions_dir() . '/' . preg_replace('/[^0-9\-]/', '', (string)$chatId) . '.json';
}

function save_reference($chatId, $reference) {
    $data = [
        'reference' => $reference,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    file_put_contents(bot_session_file($chatId), json_encode($data, JSON_PRETTY_PRINT));
}

function get_reference($chatId) {
    $file = bot_session_file($chatId);

    if (!file_exists($file)) {
        return '';
    }

    $data = json_decode(file_get_contents($file), true);

    return $data['reference'] ?? '';
}

function extract_reference($text) {
    $text = strtoupper((string)$text);

    if (preg_match('/HIRA[-_ ]?\d{5}/', $text, $match)) {
        $digits = preg_replace('/[^0-9]/', '', $match[0]);

        if (strlen($digits) === 5) {
            return 'HIRA-' . $digits;
        }
    }

    return '';
}

function get_message_type($message) {
    if (isset($message['photo'])) return 'Photo';
    if (isset($message['document'])) return 'Document';
    if (isset($message['video'])) return 'Video';
    if (isset($message['animation'])) return 'Animation';
    if (isset($message['voice'])) return 'Voice';
    if (isset($message['text'])) return 'Text';
    if (isset($message['caption'])) return 'Captioned File';

    return 'Message';
}

function has_media_or_file($message) {
    return isset($message['photo'])
        || isset($message['document'])
        || isset($message['video'])
        || isset($message['animation'])
        || isset($message['voice']);
}

$message = $update['message'] ?? null;

if (!$message) {
    echo "NO MESSAGE";
    exit;
}

$chatId = $message['chat']['id'];
$messageId = $message['message_id'];

$text = trim($message['text'] ?? '');
$caption = trim($message['caption'] ?? '');
$contentText = trim($text . "\n" . $caption);

$firstName = $message['from']['first_name'] ?? 'Creator';
$lastName = $message['from']['last_name'] ?? '';
$username = $message['from']['username'] ?? '';
$fullName = trim($firstName . ' ' . $lastName);
$messageType = get_message_type($message);

$forwardChatId = $telegram_forward_chat_id ?? ($telegram_chat_id ?? '');

$profileLine = $username
    ? "<a href=\"https://t.me/" . safe_html($username) . "\">@" . safe_html($username) . "</a>"
    : "<code>" . safe_html($fullName) . "</code>";

/* BOT COMMAND TEXTS */
$helpText =
    "✨ <b>Hiraya Vote PH Applicant Bot</b>\n\n" .
    "Welcome to the final creator submission step.\n\n" .
    "<b>Available commands:</b>\n\n" .
    "/start - Start your final submission\n" .
    "/help - View all bot commands\n" .
    "/about - Learn about Hiraya Vote PH\n" .
    "/apply - Application instructions\n" .
    "/requirements - What to send\n" .
    "/status - Review status information\n" .
    "/contact - Contact Hiraya Vote PH\n" .
"/website - Open the official Hiraya Vote PH website\n\n" .
    "To complete your submission, send your Hiraya Vote PH reference number and your best photos or portfolio.";

$aboutText =
    "🌸 <b>About Hiraya Vote PH</b>\n\n" .
    "Hiraya Vote PH is a Filipino beauty, fashion, and creator campaign platform created for aspiring models, creators, and brand storytellers.\n\n" .
    "The campaign highlights confidence, visual identity, content creation, and creative self-expression through fashion and beauty-inspired collaborations.";

$applyText =
    "📝 <b>How to Apply</b>\n\n" .
    "1. Submit your application form on the Hiraya Vote PH website.\n" .
    "2. Complete the verification flow.\n" .
    "3. Copy your reference number.\n" .
    "4. Send your reference number here with your best photos or portfolio.\n\n" .
    "Example reference number:\n<code>HIRA-12345</code>";

$requirementsText =
    "📸 <b>What You Need to Send</b>\n\n" .
    "Please send the following:\n\n" .
    "• Your Hiraya Vote PH reference number\n" .
    "• 2–5 clear photos\n" .
    "• Portfolio or sample content, if available\n" .
    "• Social media profile links, if not yet submitted\n\n" .
    "Accepted files: photos, videos, PDFs, or document files.";

$statusText =
    "⏳ <b>Application Status</b>\n\n" .
    "After your reference number and photos are received, the Hiraya Vote PH team will review your submission.\n\n" .
    "Only shortlisted applicants may be contacted for the next campaign stage.";

$contactText =
    "📩 <b>Contact Hiraya Vote PH</b>\n\n" .
    "For application concerns, send your reference number first.\n\n" .
    "Example:\n<code>HIRA-12345</code>\n\n" .
    "Then send your concern or portfolio details in the same chat.";

$websiteUrl = $official_website_url ?? 'https://dev-Hiraya Vote PHclub.pantheonsite.io/Hiraya Vote PH/';

$websiteText =
    "🌐 <b>Hiraya Vote PH Official Website</b>\n\n" .
    "You may visit the original Hiraya Vote PH website here:\n\n" .
    "🔗 <a href=\"" . safe_html($websiteUrl) . "\">Open Hiraya Vote PH Website</a>\n\n" .
    "<code>" . safe_html($websiteUrl) . "</code>\n\n" .
    "Use this website to view the campaign page, creator application details, collections, gallery, and brand information.";

/* SIMPLE COMMANDS */
if ($text === '/id') {
    bot_reply($chatId, "Your chat ID is:\n\n<code>" . safe_html($chatId) . "</code>");
    echo "OK";
    exit;
}

if ($text === '/help') {
    bot_reply($chatId, $helpText);
    echo "OK";
    exit;
}

if ($text === '/about') {
    bot_reply($chatId, $aboutText);
    echo "OK";
    exit;
}

if ($text === '/apply') {
    bot_reply($chatId, $applyText);
    echo "OK";
    exit;
}

if ($text === '/requirements') {
    bot_reply($chatId, $requirementsText);
    echo "OK";
    exit;
}

if ($text === '/status') {
    bot_reply($chatId, $statusText);
    echo "OK";
    exit;
}

if ($text === '/contact') {
    bot_reply($chatId, $contactText);
    echo "OK";
    exit;
}

if ($text === '/website') {
    bot_reply($chatId, $websiteText);
    echo "OK";
    exit;
}


/* START COMMAND WITH REFERENCE NUMBER */
if (strpos($text, '/start') === 0) {
    $payload = trim(str_replace('/start', '', $text));
    $reference = extract_reference($payload);

    if ($reference !== '') {
        save_reference($chatId, $reference);

        bot_reply(
            $chatId,
            "Hello, <b>" . safe_html($firstName) . "</b>! ✨\n\n" .
            "Your Hiraya Vote PH reference number has been received:\n" .
            "<code>" . safe_html($reference) . "</code>\n\n" .
            "Please send your best pictures, model portfolio, sample content, or social media links here.\n\n" .
            "After sending your files, I will confirm that your submission has been received."
        );

        if (!empty($forwardChatId)) {
            tg_api('sendMessage', [
                'chat_id' => $forwardChatId,
                'text' =>
                    "✨ <b>Hiraya Vote PH Bot Started</b>\n\n" .
                    "<b>Applicant:</b> " . $profileLine . "\n" .
                    "<b>Name:</b> <code>" . safe_html($fullName) . "</code>\n" .
                    "<b>Chat ID:</b> <code>" . safe_html($chatId) . "</code>\n" .
                    "<b>Reference:</b> <code>" . safe_html($reference) . "</code>",
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ]);
        }

        echo "OK";
        exit;
    }

    bot_reply(
        $chatId,
        "Hello, <b>" . safe_html($firstName) . "</b>! ✨\n\n" .
        "Please send your Hiraya Vote PH reference number first, then send your best photos or portfolio.\n\n" .
        "Example:\n<code>HIRA-12345</code>\n\n" .
        "You may also type /help to see all commands."
    );

    echo "OK";
    exit;
}

/* EXTRACT REFERENCE FROM NORMAL MESSAGE */
$referenceFromMessage = extract_reference($contentText);

if ($referenceFromMessage !== '') {
    save_reference($chatId, $referenceFromMessage);
}

$savedReference = get_reference($chatId);

/* NO REFERENCE YET */
if ($savedReference === '') {
    bot_reply(
        $chatId,
        "Thank you for messaging Hiraya Vote PH. ✨\n\n" .
        "Please send your reference number first so we can match your Telegram submission with your application form.\n\n" .
        "Example:\n<code>HIRA-12345</code>\n\n" .
        "Need help? Type /help."
    );

    echo "OK";
    exit;
}

/* IF TEXT ONLY, ASK FOR PHOTO/PORTFOLIO */
if (!has_media_or_file($message)) {
    bot_reply(
        $chatId,
        "Reference number received:\n" .
        "<code>" . safe_html($savedReference) . "</code>\n\n" .
        "Please send your best pictures, portfolio, video, or sample content here to complete your final submission."
    );

    echo "OK";
    exit;
}

/* APPLICANT RECEIVES CONFIRMATION */
bot_reply(
    $chatId,
    "Received, <b>" . safe_html($firstName) . "</b>. Thank you! ✨\n\n" .
    "Your submission has been recorded under this reference number:\n" .
    "<code>" . safe_html($savedReference) . "</code>\n\n" .
    "Your photos or portfolio have been forwarded to the Hiraya Vote PH review team.\n\n" .
    "If you have more files, you may continue sending them here."
);

/* FORWARD TO CHANNEL */
if (!empty($forwardChatId)) {
    tg_api('sendMessage', [
        'chat_id' => $forwardChatId,
        'text' =>
            "📩 <b>New Hiraya Vote PH Applicant Submission</b>\n\n" .
            "<b>Reference:</b> <code>" . safe_html($savedReference) . "</code>\n" .
            "<b>Applicant:</b> " . $profileLine . "\n" .
            "<b>Name:</b> <code>" . safe_html($fullName) . "</code>\n" .
            "<b>Applicant Chat ID:</b> <code>" . safe_html($chatId) . "</code>\n" .
            "<b>Message Type:</b> <code>" . safe_html($messageType) . "</code>\n\n" .
            "<b>Caption / Message:</b>\n<pre>" . safe_html($contentText !== '' ? $contentText : '-') . "</pre>",
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ]);

    tg_api('forwardMessage', [
        'chat_id' => $forwardChatId,
        'from_chat_id' => $chatId,
        'message_id' => $messageId
    ]);
}

echo "OK";
?>