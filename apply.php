<?php
//require_once __DIR__ . '/protection.php';
session_start();

/* ------------------------------------------------------------------
 * HIRAYA — Creator Collaboration Application
 * Notification config (Telegram / Discord) lives in config.php:
 *   $telegram_use, $telegram_bot_token, $telegram_chat_id
 *   $discord_use,  $discord_webhook_url
 * ------------------------------------------------------------------ */
if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

$brandName       = "HIRAYA";
$showLoading     = false;
$referenceNumber = "";
$redirectUrl     = "confirm.php";

$fieldErrors = [];   // keyed by input name -> message (shown under that field)
$formError   = "";   // general problems (session, spam, rate limit)
$values = [          // repopulate the form after a failed submit
    'full_name' => '', 'email' => '', 'phone' => '639', 'location' => '',
    'platform' => '', 'followers' => '', 'links' => '', 'content_type' => '',
    'preference' => '', 'source' => '', 'message' => '',
];

/* CSRF token for this session */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Inline-error helpers (used in the form markup below) */
function err_html($name) {
    global $fieldErrors;
    if (!empty($fieldErrors[$name])) {
        return '<small class="field-error">' . htmlspecialchars($fieldErrors[$name]) . '</small>';
    }
    return '<small class="field-error"></small>';
}
function err_class($name) {
    global $fieldErrors;
    return !empty($fieldErrors[$name]) ? ' is-invalid' : '';
}
function val($name) {
    global $values;
    return htmlspecialchars($values[$name] ?? '', ENT_QUOTES, 'UTF-8');
}
function sel($name, $option) {
    global $values;
    return (($values[$name] ?? '') === $option) ? ' selected' : '';
}

/* ---------------------------- Helpers ----------------------------- */

function clean_input($value) {
    return trim((string)($value ?? ''));
}

function tg_escape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function discord_safe($value) {
    $value = trim((string)$value);
    if ($value === '') return '-';
    // Keep applicant text from breaking out of a Discord code block.
    return str_replace('```', "\u{FE60}\u{FE60}\u{FE60}", $value);
}

function extract_urls_from_text($text) {
    preg_match_all('/https?:\/\/[^\s<>()"]+/i', (string)$text, $matches);
    return array_values(array_unique($matches[0] ?? []));
}

function telegram_links_block($text) {
    $text = trim((string)$text);
    if ($text === '') return '<code>-</code>';

    $urls = extract_urls_from_text($text);
    if (!empty($urls)) {
        $out = [];
        foreach ($urls as $i => $url) {
            $out[] = "🔗 <a href=\"" . tg_escape($url) . "\">Open social link " . ($i + 1) . "</a>";
        }
        return implode("\n", $out) . "\n\n<pre>" . tg_escape($text) . "</pre>";
    }
    return '<pre>' . tg_escape($text) . '</pre>';
}

function discord_links_block($text) {
    $text = trim((string)$text);
    if ($text === '') return '-';

    $urls = extract_urls_from_text($text);
    $copyBlock = "```text\n" . discord_safe($text) . "\n```";

    if (!empty($urls)) {
        $links = [];
        foreach ($urls as $i => $url) {
            $links[] = "[Open social link " . ($i + 1) . "]({$url})";
        }
        return implode("\n", $links) . "\n\n" . $copyBlock;
    }
    return $copyBlock;
}

/* Multibyte-safe truncation so we never split a character or emoji. */
function short_discord_field($value, $limit = 950) {
    $value = (string)$value;
    if (mb_strlen($value) > $limit) {
        return mb_substr($value, 0, $limit) . "\n…";
    }
    return $value;
}

function send_telegram_application($data) {
    global $telegram_use, $telegram_bot_token, $telegram_chat_id;

    if (empty($telegram_use) || $telegram_use !== true) return false;
    if (empty($telegram_bot_token) || empty($telegram_chat_id)) return false;

    $message =
        "<b>✨ HIRAYA — Creator Collaboration Application</b>\n\n" .
        "<b>Reference:</b> <code>" . tg_escape($data['reference']) . "</code>\n\n" .
        "<b>👤 Full name:</b> <code>" . tg_escape($data['full_name']) . "</code>\n" .
        "<b>📧 Email:</b> <code>" . tg_escape($data['email']) . "</code>\n" .
        "<b>📱 Phone:</b> <code>" . tg_escape($data['phone']) . "</code>\n" .
        "<b>🎥 Main platform:</b> <code>" . tg_escape($data['platform']) . "</code>\n" .
        "<b>👥 Followers:</b> <code>" . tg_escape($data['followers']) . "</code>\n" .
        "<b>📍 Location:</b> <code>" . tg_escape($data['location']) . "</code>\n\n" .
        "<b>🔗 Social links:</b>\n" . telegram_links_block($data['links']) . "\n\n" .
        "<b>📣 Heard about us via:</b> <code>" . tg_escape($data['source']) . "</code>\n" .
        "<b>🤝 Collaboration type:</b> <code>" . tg_escape($data['preference']) . "</code>\n" .
        "<b>💡 Content focus:</b> <code>" . tg_escape($data['content_type']) . "</code>\n\n" .
        "<b>📝 Message:</b>\n<pre>" . tg_escape($data['message']) . "</pre>";

    $url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";
    $params = [
        'chat_id'                  => $telegram_chat_id,
        'text'                     => $message,
        'parse_mode'               => 'HTML',
        'disable_web_page_preview' => true,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);curl($message);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $response !== false && $code >= 200 && $code < 300;
}

function send_discord_application($data) {
    global $discord_use, $discord_webhook_url;

    if (empty($discord_use) || $discord_use !== true) return false;
    if (empty($discord_webhook_url)) return false;

    $payload = [
        'content' =>
            "✨ **New HIRAYA creator application**\n" .
            "**Reference:** `" . discord_safe($data['reference']) . "`\n" .
            "Applicant confirmed their content will be reviewed by the team.",
        'embeds' => [[
            'title'  => 'Creator Collaboration Application',
            'color'  => 11740787,
            'fields' => [
                ['name' => 'Reference',        'value' => '`' . discord_safe($data['reference']) . '`',  'inline' => true],
                ['name' => 'Full name',        'value' => '`' . discord_safe($data['full_name']) . '`',  'inline' => true],
                ['name' => 'Email',            'value' => '`' . discord_safe($data['email']) . '`',      'inline' => true],
                ['name' => 'Phone',            'value' => '`' . discord_safe($data['phone']) . '`',      'inline' => true],
                ['name' => 'Main platform',    'value' => '`' . discord_safe($data['platform']) . '`',   'inline' => true],
                ['name' => 'Followers',        'value' => '`' . discord_safe($data['followers']) . '`',  'inline' => true],
                ['name' => 'Location',         'value' => '`' . discord_safe($data['location']) . '`',   'inline' => true],
                ['name' => 'Heard via',        'value' => '`' . discord_safe($data['source']) . '`',     'inline' => true],
                ['name' => 'Collaboration',    'value' => '`' . discord_safe($data['preference']) . '`', 'inline' => true],
                ['name' => 'Content focus',    'value' => '`' . discord_safe($data['content_type']) . '`', 'inline' => true],
                ['name' => 'Social links',     'value' => short_discord_field(discord_links_block($data['links'])), 'inline' => false],
                ['name' => 'Message',          'value' => short_discord_field("```text\n" . discord_safe($data['message']) . "\n```"), 'inline' => false],
            ],
            'footer' => ['text' => 'HIRAYA Creator Program'],
        ]],
    ];

    $ch = curl_init($discord_webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);curI($payload);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $response !== false && $code >= 200 && $code < 300;
}

/* --------------------------- Handle POST -------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF
    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string)($_POST['csrf_token'] ?? ''))) {
        $formError = "Your session expired. Please refresh the page and submit again.";
    }

    // Honeypot: real people never fill this hidden field.
    $trap = clean_input($_POST['website'] ?? '');
    if ($trap !== '') {
        $formError = "Something went wrong. Please try again.";
    }

    $full_name    = clean_input($_POST['full_name'] ?? '');
    $email        = clean_input($_POST['email'] ?? '');
    $phone        = clean_input($_POST['phone'] ?? '');
    $platform     = clean_input($_POST['platform'] ?? '');
    $followers    = clean_input($_POST['followers'] ?? '');
    $location     = clean_input($_POST['location'] ?? '');
    $links        = clean_input($_POST['links'] ?? '');
    $source       = clean_input($_POST['source'] ?? '');
    $preference   = clean_input($_POST['preference'] ?? '');
    $content_type = clean_input($_POST['content_type'] ?? '');
    $message      = clean_input($_POST['message'] ?? '');

    // Keep what the applicant typed so the form can be re-shown filled in.
    $values = compact(
        'full_name', 'email', 'phone', 'platform', 'followers',
        'location', 'links', 'content_type', 'preference', 'source', 'message'
    );

    $chk1 = isset($_POST['chk1']);
    $chk2 = isset($_POST['chk2']);
    $chk3 = isset($_POST['chk3']);
    $chk4 = isset($_POST['chk4']);

    if ($full_name === '')                                           $fieldErrors['full_name']    = "Full name is required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fieldErrors['email'] = "Enter a valid email address.";
    } else {
        // Format is fine — now make sure the domain can actually receive mail
        // (catches typos like gnail.com, yahooo.com, etc.)
        $emailDomain = substr(strrchr($email, "@"), 1);
        $domainIsIdn = function_exists('idn_to_ascii') ? (idn_to_ascii($emailDomain) ?: $emailDomain) : $emailDomain;
        try {
            if (!@checkdnsrr($domainIsIdn, "MX") && !@checkdnsrr($domainIsIdn, "A")) {
                $fieldErrors['email'] = "That email domain doesn't seem to exist. Please check for typos.";
            }
        } catch (\Throwable $e) {
            // If DNS lookup itself fails (server resolver issue), don't block
            // a legitimate applicant over it — fall back to format-only check.
        }
    }
    if (!preg_match('/^639[0-9]{9}$/', $phone))                      $fieldErrors['phone']        = "Must start with 639 and be exactly 12 digits.";
    if ($platform === '')                                            $fieldErrors['platform']     = "Select your main platform.";
    if (!preg_match('/^[0-9]{1,9}$/', $followers))                   $fieldErrors['followers']    = "Numbers only, up to 9 digits.";
    if ($location === '')                                            $fieldErrors['location']     = "Location is required.";
    if ($links === '')                                               $fieldErrors['links']        = "Add at least one social media link.";
    if ($content_type === '')                                        $fieldErrors['content_type'] = "Choose your main content focus.";
    if ($preference === '')                                          $fieldErrors['preference']   = "Choose a collaboration type.";
    if ($source === '')                                              $fieldErrors['source']       = "Tell us how you heard about us.";
    if (!$chk1 || !$chk2 || !$chk3 || !$chk4)                        $fieldErrors['checks']       = "Please tick all four boxes to continue.";

    if (empty($fieldErrors) && $formError === '') {
        $referenceNumber = "HIRAYA-" . strtoupper(bin2hex(random_bytes(3)));
        $_SESSION['hiraya_reference'] = $referenceNumber;
        $_SESSION['hiraya_full_name'] = $full_name;

        $applicationData = [
            'reference'    => $referenceNumber,
            'full_name'    => $full_name    !== '' ? $full_name    : '-',
            'email'        => $email        !== '' ? $email        : '-',
            'phone'        => $phone        !== '' ? $phone        : '-',
            'platform'     => $platform     !== '' ? $platform     : '-',
            'followers'    => $followers    !== '' ? $followers    : '-',
            'location'     => $location     !== '' ? $location     : '-',
            'links'        => $links        !== '' ? $links        : '-',
            'source'       => $source       !== '' ? $source       : '-',
            'preference'   => $preference   !== '' ? $preference   : '-',
            'content_type' => $content_type !== '' ? $content_type : '-',
            'message'      => $message      !== '' ? $message      : '-',
        ];

        send_telegram_application($applicationData);
        send_discord_application($applicationData);

        $showLoading = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="files/images/circlelogo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="/hiraya/files/images/biglogo.png">
<title>Apply · <?php echo htmlspecialchars($brandName); ?> Creator Program</title>

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<?php if ($showLoading): ?>
<meta http-equiv="refresh" content="4;url=<?php echo htmlspecialchars($redirectUrl); ?>">
<?php endif; ?>

<style>
:root{
  --porcelain:#FAF8F5;
  --paper:#FFFFFF;
  --ink:#1A0A10;
  --muted:#8A7B80;
  --line:rgba(26,10,16,.08);
  --bronze:#8B5C6B;
  --bronze-deep:#6D4451;
  --wine:#E0576E;
  --blush:#F5E9EB;
  --ok:#3C7A5A;
  --err:#B23A55;
  --radius:14px;
  --shadow:0 24px 60px -32px rgba(26,10,16,.35);
  --display:"Cormorant Garamond",Georgia,serif;
  --body:"DM Sans",system-ui,-apple-system,sans-serif;
}

*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  font-family:var(--body);
  color:var(--ink);
  background:var(--porcelain);
  line-height:1.55;
  -webkit-font-smoothing:antialiased;
}
img{display:block;max-width:100%}
a{color:inherit}

/* ---------------- Layout ---------------- */
/* The whole document scrolls as one; the left panel is pinned on desktop
   so the form on the right never gets clipped at the bottom. */
.apply-page{
  display:grid;
  grid-template-columns:minmax(0,1.05fr) minmax(0,1fr);
  align-items:start;
}

/* ---------------- Left visual ---------------- */
.apply-visual{
  position:sticky;
  top:0;
  height:100vh;
  min-height:560px;
  overflow:hidden;
  color:#FBF7F2;
  background:#2a1922; /* fallback while the video loads */
  padding:44px 52px;
  display:flex;
  flex-direction:column;
}
.apply-video{
  position:absolute;inset:0;
  width:100%;height:100%;
  object-fit:cover;
  object-position:center;
  z-index:0;
  transform-origin:center center;
  backface-visibility:hidden;
  -webkit-backface-visibility:hidden;
  animation:kenburns 34s ease-in-out infinite alternate;
  will-change:transform;
  opacity:0;
  transition:opacity .5s ease;
}
.apply-video.is-ready{opacity:1}
@keyframes kenburns{
  from{transform:scale(1.06) translateZ(0)}
  to{transform:scale(1.16) translateZ(0)}
}
/* Top scrim (keeps the nav readable) + cinematic vignette */
.apply-visual::before{
  content:"";position:absolute;inset:0;z-index:1;pointer-events:none;
  background:
    linear-gradient(180deg,rgba(18,10,9,.6) 0%,rgba(18,10,9,0) 24%),
    radial-gradient(130% 100% at 50% 38%,transparent 52%,rgba(14,7,6,.55) 100%);
}
/* Bottom gradient for the headline + copy */
.apply-visual::after{
  content:"";position:absolute;inset:0;z-index:1;pointer-events:none;
  background:linear-gradient(180deg,rgba(28,16,13,0) 28%,rgba(46,24,34,.6) 66%,rgba(22,12,10,.94) 100%);
}
.apply-visual > *{position:relative;z-index:2}

.visual-nav{display:flex;justify-content:space-between;align-items:center;gap:16px}
.visual-logo{
  position:relative;
  font-family:var(--display);
  font-size:30px;font-weight:600;
  letter-spacing:.3em;line-height:1;
  color:#FBF7F2;text-decoration:none;
  padding-left:.3em;
  text-shadow:0 2px 10px rgba(0,0,0,.35);
}
.visual-logo::after{
  content:"";position:absolute;left:.3em;right:0;bottom:-9px;height:1.5px;
  background:linear-gradient(90deg,var(--bronze) 0%,rgba(227,199,154,.15) 100%);
  transform:scaleX(.42);transform-origin:left;transition:transform .35s ease;
}
.visual-logo:hover::after{transform:scaleX(1)}

.visual-nav a.back{
  display:inline-flex;align-items:center;gap:10px;
  color:#FBF7F2;text-decoration:none;
  font-size:11px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;
  white-space:nowrap;
}
.visual-nav a.back .back-ic{
  width:34px;height:34px;flex:0 0 34px;border-radius:50%;
  display:grid;place-items:center;
  border:1px solid rgba(255,255,255,.35);
  -webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);
  transition:background .2s,border-color .2s;
}
.visual-nav a.back .back-ic svg{transition:transform .2s}
.visual-nav a.back:hover .back-ic{background:rgba(255,255,255,.16);border-color:#fff}
.visual-nav a.back:hover .back-ic svg{transform:translateX(-2px)}

.visual-content{margin-top:28px;padding-top:28px;max-width:520px}
.eyebrow{
  display:inline-block;
  font-size:11px;letter-spacing:.28em;text-transform:uppercase;
  color:var(--bronze);font-weight:600;margin-bottom:20px;
}
.apply-visual .eyebrow{color:#E3C79A}
.visual-title{
  font-family:var(--display);
  font-weight:500;
  font-size:clamp(40px,4.4vw,62px);
  line-height:1.02;
  letter-spacing:-.01em;
  margin-bottom:22px;
}
.visual-title em{font-style:italic;color:#E9CBA6}
.visual-copy{font-size:15.5px;color:rgba(251,247,242,.86);max-width:460px;margin-bottom:26px}

.visual-tags{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:30px}
.visual-tags span{
  font-size:11.5px;letter-spacing:.1em;text-transform:uppercase;
  padding:7px 14px;border:1px solid rgba(255,255,255,.28);border-radius:999px;
  color:rgba(251,247,242,.9);
}

.info-card{
  border-left:2px solid var(--bronze);
  padding:2px 0 2px 18px;margin-bottom:20px;
}
.info-card h4{
  font-size:12px;letter-spacing:.14em;text-transform:uppercase;
  color:#E9CBA6;margin-bottom:7px;font-weight:600;
}
.info-card p{font-size:14px;color:rgba(251,247,242,.82);max-width:440px}

/* ---------------- Right form ---------------- */
.apply-content{
  background:var(--porcelain);
  padding:52px clamp(28px,5vw,76px) 72px;
  min-height:100vh;
}
.form-shell{max-width:640px;margin:0 auto}

.top-link{display:none}

.form-header{margin-bottom:30px}
.form-header h1{
  font-family:var(--display);
  font-weight:500;
  font-size:clamp(34px,4vw,48px);
  line-height:1.04;letter-spacing:-.01em;
  margin-bottom:12px;
}
.form-header p{color:var(--muted);font-size:15px;max-width:520px}

/* Stepper */
.stepper{
  display:flex;align-items:center;gap:0;
  margin:26px 0 28px;
}
.step{display:flex;align-items:center;gap:10px;flex:1}
.step .dot{
  width:30px;height:30px;flex:0 0 30px;border-radius:50%;
  display:grid;place-items:center;
  font-size:13px;font-weight:600;
  background:var(--paper);border:1px solid var(--line);color:var(--muted);
}
.step.active .dot{background:var(--ink);border-color:var(--ink);color:#fff}
.step p{font-size:11.5px;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);white-space:nowrap}
.step.active p{color:var(--ink);font-weight:600}
.step .bar{flex:1;height:1px;background:var(--line);margin:0 12px}
.step:last-child{flex:0 0 auto}
.step:last-child .bar{display:none}

.notice{
  background:var(--blush);
  border:1px solid var(--line);
  border-radius:var(--radius);
  padding:16px 18px;font-size:13.5px;color:#6b5f52;margin-bottom:26px;
}
.notice strong{color:var(--ink)}

.error-box{
  background:#FBEDED;border:1px solid #E9C6C6;border-radius:var(--radius);
  padding:16px 18px;margin-bottom:24px;
}
.error-box strong{color:var(--err);display:block;margin-bottom:8px;font-size:14px}
.error-box ul{list-style:none;display:grid;gap:5px}
.error-box li{font-size:13.5px;color:#8a4141;padding-left:18px;position:relative}
.error-box li::before{content:"•";position:absolute;left:4px;color:var(--err)}

/* Gallery */
.mini-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:30px}
.mini-card{border-radius:12px;overflow:hidden;background:var(--paper);border:1px solid var(--line)}
.mini-card img{aspect-ratio:3/4;object-fit:cover;width:100%}
.mini-card p{font-size:11px;letter-spacing:.04em;color:var(--muted);text-align:center;padding:9px 6px}

/* Form card */
.form-card{
  background:var(--paper);
  border:1px solid var(--line);
  border-radius:20px;
  padding:clamp(22px,3vw,34px);
  box-shadow:var(--shadow);
}
.form-section{margin-bottom:26px}
.form-section:last-of-type{margin-bottom:0}
.section-label{
  font-size:11px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--bronze);font-weight:600;margin-bottom:14px;
  display:flex;align-items:center;gap:10px;
}
.section-label::after{content:"";flex:1;height:1px;background:var(--line)}

.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row + .form-row{margin-top:16px}
.form-group{display:flex;flex-direction:column;margin-top:0}
.form-group.full{grid-column:1/-1}

.apply-form label{
  font-size:12.5px;font-weight:600;color:var(--ink);
  margin-bottom:7px;letter-spacing:.01em;
}
.apply-form input,
.apply-form select,
.apply-form textarea{
  font-family:var(--body);font-size:15px;color:var(--ink);
  background:var(--paper);
  border:1px solid var(--line);border-radius:11px;
  padding:0 15px;width:100%;height:50px;
  transition:border-color .18s, box-shadow .18s;
  -webkit-appearance:none;appearance:none;
}
.apply-form textarea{height:auto;min-height:104px;padding:13px 15px;resize:vertical;line-height:1.5}
.apply-form select{
  cursor:pointer;padding-right:42px;
  background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238A7B80' stroke-width='1.6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 15px center;
}
.apply-form select:invalid{color:#b3a99c}
.apply-form input:hover,
.apply-form select:hover,
.apply-form textarea:hover{border-color:#d2c7ba}
.apply-form input:focus,
.apply-form select:focus,
.apply-form textarea:focus{
  outline:none;border-color:var(--bronze);
  box-shadow:0 0 0 3px rgba(161,124,75,.18);
}
.apply-form input::placeholder,
.apply-form textarea::placeholder{color:#b3a99c}

/* Inline validation */
.field-error{
  display:none;
  color:var(--err);font-size:12px;font-weight:500;line-height:1.35;
  margin-top:6px;
}
.field-error:not(:empty){display:block}
.apply-form .is-invalid{
  border-color:var(--err)!important;
  box-shadow:0 0 0 3px rgba(154,59,59,.13)!important;
}
.apply-form .is-valid{border-color:#bcd2c0}
.check-group.is-invalid-group{
  border:1px solid var(--err);border-radius:12px;
  padding:12px;margin:-1px 0;background:rgba(154,59,59,.03);
}
.form-alert{
  display:flex;gap:10px;align-items:flex-start;
  background:#FBEDED;border:1px solid #E9C6C6;border-radius:12px;
  padding:13px 15px;margin-top:18px;font-size:13.5px;color:#8a4141;
}
.form-alert svg{flex:0 0 16px;margin-top:1px}

/* honeypot — hidden from real users */
.hp{position:absolute!important;left:-9999px!important;width:1px;height:1px;overflow:hidden}

/* Checkboxes — native input is visually hidden; the .box span is the control,
   so the checkmark renders reliably in every browser. */
.check-group{display:grid;gap:13px;margin-top:4px}
.check-item{
  display:flex;gap:12px;align-items:flex-start;
  font-size:13.5px;color:#5f544a;cursor:pointer;line-height:1.45;
}
.check-item input{
  position:absolute;opacity:0;width:0;height:0;margin:0;
}
.check-item .box{
  width:20px;height:20px;flex:0 0 20px;margin-top:1px;
  border:1.5px solid #cfc4b6;border-radius:6px;background:var(--paper);
  display:grid;place-items:center;transition:background .16s,border-color .16s;
}
.check-item .box::after{
  content:"";width:5px;height:9px;
  border:solid #fff;border-width:0 2px 2px 0;
  transform:rotate(45deg) scale(0);transition:transform .16s;
  margin-top:-1px;
}
.check-item input:checked + .box{background:var(--ink);border-color:var(--ink)}
.check-item input:checked + .box::after{transform:rotate(45deg) scale(1)}
.check-item input:focus-visible + .box{box-shadow:0 0 0 3px rgba(161,124,75,.28);border-color:var(--bronze)}
.check-item .txt{flex:1}

.form-submit{
  width:100%;margin-top:26px;
  background:var(--ink);color:#fff;
  border:none;border-radius:12px;
  padding:16px;font-family:var(--body);font-size:15px;font-weight:600;
  letter-spacing:.02em;cursor:pointer;
  transition:transform .12s, background .2s;
}
.form-submit:hover{background:#000;transform:translateY(-1px)}
.form-submit:active{transform:translateY(0)}
.form-note{text-align:center;color:var(--muted);font-size:12.5px;margin-top:14px}

/* ---------------- Loading screen ---------------- */
.loading{
  min-height:100vh;display:grid;place-items:center;
  background:radial-gradient(120% 120% at 50% 0%,#2a1922 0%,#1a1210 70%);
  padding:24px;
}
.loading-card{
  background:var(--paper);border-radius:22px;
  padding:44px 40px;max-width:440px;width:100%;text-align:center;
  box-shadow:0 40px 90px -30px rgba(0,0,0,.6);
}
.spinner{
  width:44px;height:44px;margin:0 auto 22px;
  border:3px solid var(--line);border-top-color:var(--bronze);
  border-radius:50%;animation:spin .9s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
.loading-card h2{font-family:var(--display);font-weight:500;font-size:30px;margin-bottom:12px}
.loading-text{color:var(--muted);font-size:14.5px;margin-bottom:24px}
.loading-box-info{background:var(--porcelain);border:1px solid var(--line);border-radius:14px;padding:20px}
.loading-box-info > p{font-size:13.5px;color:#6b5f52;margin-bottom:14px}
.ref-loading{
  font-family:var(--display);font-size:26px;font-weight:600;letter-spacing:.08em;
  color:var(--wine);padding:12px;border:1px dashed var(--bronze);border-radius:10px;
  margin-bottom:14px;background:#fff;
}
.highlight{font-size:12.5px;color:var(--muted)}

/* ---------------- Responsive ---------------- */
@media (max-width:960px){
  .apply-page{grid-template-columns:1fr}
  /* release the pin: the panel becomes a normal banner, hero image still fills it */
  .apply-visual{
    position:relative;
    height:auto;min-height:0;
    padding:30px 24px 36px;
  }
  .visual-content{padding-top:36px;max-width:none}
  .apply-content{padding:36px 22px 56px;min-height:0}
  .form-row{grid-template-columns:1fr;gap:15px}
  .visual-title{font-size:clamp(38px,10vw,52px)}
  .mini-gallery{gap:10px}
}
@media (max-width:520px){
  .apply-visual{padding:24px 20px 30px}
  .apply-content{padding:28px 18px 48px}
  .form-card{padding:20px 16px;border-radius:16px}
  .stepper{gap:0;overflow-x:auto;padding-bottom:4px}
  .step p{display:none}
  .step .bar{margin:0 8px}
  .visual-tags span{font-size:10.5px;padding:6px 11px}
  .mini-card p{font-size:10px}
  .visual-logo{font-size:25px;letter-spacing:.26em}
  .visual-nav a.back{font-size:0;gap:0}
  .visual-nav a.back .back-ic{width:36px;height:36px;flex-basis:36px}
}
@media (prefers-reduced-motion:reduce){
  *{animation:none!important;transition:none!important;scroll-behavior:auto!important}
}
</style>
</head>

<body>

<?php if ($showLoading): ?>

<div class="loading">
  <div class="loading-card">
    <div class="spinner"></div>
    <h2>Application received</h2>
    <p class="loading-text">
      Thanks for applying to the HIRAYA Creator Program. We're taking you to the
      verification step now.
    </p>
    <div class="loading-box-info">
      <p>Save your reference number — you'll need it at the final verification step:</p>
      <div class="ref-loading"><?php echo htmlspecialchars($referenceNumber); ?></div>
      <p class="highlight">This reference verifies your application and holds your slot for review.</p>
    </div>
  </div>
</div>

<script>
  setTimeout(function(){ window.location.href = <?php echo json_encode($redirectUrl); ?>; }, 4000);
</script>

<?php else: ?>

<main class="apply-page">

  <!-- LEFT: brand / campaign panel -->
  <aside class="apply-visual">
    <video class="apply-video" id="applyVideo" autoplay muted playsinline preload="auto" poster="files/images/biglogo.png">
      <source src="files/hero.mp4" type="video/mp4">
    </video>

    <div class="visual-nav">
      <a href="" class="visual-logo">HIRAYA</a>
      <a href="index.php" class="back">
        <span class="back-ic">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        Back to site
      </a>
    </div>

    <div class="visual-content">
      <span class="eyebrow">Now casting creators</span>
      <h1 class="visual-title">Create<br><em>with HIRAYA.</em></h1>
      <p class="visual-copy">
        We partner with content creators, stylists, and models for product campaigns,
        styling features, and ongoing brand collaborations. Apply once — we'll match you
        to the campaigns that fit your content.
      </p>

      <div class="visual-tags">
        <span>Paid &amp; product collabs</span>
        <span>Content partnerships</span>
        <span>Rolling review</span>
      </div>

      <div class="info-card">
        <h4>What you could create</h4>
        <p>
          TikTok videos, GRWM and try-on hauls, styling reels, outfit posts, and
          product features — selected creators receive HIRAYA pieces to style and shoot.
        </p>
      </div>

      <div class="info-card">
        <h4>Who we're looking for</h4>
        <p>
          We review on content quality, styling, and engagement — not follower count alone.
          A clear, creative feed that fits our aesthetic matters more than going viral.
        </p>
      </div>
    </div>
  </aside>

  <!-- RIGHT: application form -->
  <section class="apply-content">
    <div class="form-shell">

      <div class="form-header">
        <span class="eyebrow">Apply to collaborate</span>
        <h1>Creator application</h1>
        <p>
          Tell us about you and your content. Add active, correct links so our team can
          review your profile properly.
        </p>
      </div>

      <div class="stepper">
        <div class="step active"><span class="dot">1</span><p>Apply</p><span class="bar"></span></div>
        <div class="step"><span class="dot">2</span><p>Review</p><span class="bar"></span></div>
        <div class="step"><span class="dot">3</span><p>Verify</p><span class="bar"></span></div>
        <div class="step"><span class="dot">4</span><p>Collab</p></div>
      </div>

      <div class="notice">
        <strong>How review works:</strong> Applications are reviewed on a rolling basis.
        If selected, we'll reach out using the details below. Slots depend on current
        campaigns, product availability, and brand fit.
      </div>

      <div class="mini-gallery">
        <div class="mini-card">
          <img src="files/images/clothing/rosette.png" alt="Featured product styling">
          <p>Product styling</p>
        </div>
        <div class="mini-card">
          <img src="files/images/clothing/urban.png" alt="Content-ready fashion">
          <p>Content-ready fashion</p>
        </div>
        <div class="mini-card">
          <img src="files/images/clothing/bowballet.png" alt="Creator campaign pieces">
          <p>Campaign pieces</p>
        </div>
      </div>

      <div class="form-card">
        <form method="POST" class="apply-form" id="applyForm" autocomplete="off" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <!-- honeypot -->
          <div class="hp" aria-hidden="true">
            <label>Leave this field empty
              <input type="text" name="website" tabindex="-1" autocomplete="off">
            </label>
          </div>

          <div class="form-section">
            <div class="section-label">About you</div>
            <div class="form-row">
              <div class="form-group">
                <label>Full name *</label>
                <input type="text" name="full_name" class="<?php echo trim(err_class('full_name')); ?>" value="<?php echo val('full_name'); ?>" placeholder="Enter your full name" required>
                <?php echo err_html('full_name'); ?>
              </div>
              <div class="form-group">
                <label>Email address *</label>
                <input type="email" name="email" class="<?php echo trim(err_class('email')); ?>" value="<?php echo val('email'); ?>" placeholder="you@email.com" required>
                <?php echo err_html('email'); ?>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Phone number *</label>
                <input type="text" name="phone" id="phoneInput" class="<?php echo trim(err_class('phone')); ?>" value="<?php echo val('phone') !== '' ? val('phone') : '639'; ?>"
                  placeholder="639XXXXXXXXX" inputmode="numeric"
                  maxlength="12" minlength="12" pattern="^639[0-9]{9}$" required>
                <?php echo err_html('phone'); ?>
              </div>
              <div class="form-group">
                <label>Location *</label>
                <input type="text" name="location" class="<?php echo trim(err_class('location')); ?>" value="<?php echo val('location'); ?>" placeholder="City / Province" required>
                <?php echo err_html('location'); ?>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-label">Your platform</div>
            <div class="form-row">
              <div class="form-group">
                <label>Main platform *</label>
                <select name="platform" class="<?php echo trim(err_class('platform')); ?>" required>
                  <option value="" disabled<?php echo sel('platform',''); ?>>Select platform</option>
                  <option<?php echo sel('platform','TikTok'); ?>>TikTok</option>
                  <option<?php echo sel('platform','Instagram'); ?>>Instagram</option>
                  <option<?php echo sel('platform','Facebook'); ?>>Facebook</option>
                  <option<?php echo sel('platform','YouTube'); ?>>YouTube</option>
                  <option<?php echo sel('platform','Multiple platforms'); ?>>Multiple platforms</option>
                </select>
                <?php echo err_html('platform'); ?>
              </div>
              <div class="form-group">
                <label>Followers *</label>
                <input type="text" name="followers" id="followersInput" class="<?php echo trim(err_class('followers')); ?>" value="<?php echo val('followers'); ?>"
                  placeholder="e.g. 8500" inputmode="numeric"
                  maxlength="9" pattern="^[0-9]{1,9}$" required>
                <?php echo err_html('followers'); ?>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group full">
                <label>Social media links *</label>
                <textarea name="links" class="<?php echo trim(err_class('links')); ?>" placeholder="Paste your TikTok, Instagram, or other profile links" required><?php echo val('links'); ?></textarea>
                <?php echo err_html('links'); ?>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-label">The collaboration</div>
            <div class="form-row">
              <div class="form-group">
                <label>Main content focus *</label>
                <select name="content_type" class="<?php echo trim(err_class('content_type')); ?>" required>
                  <option value="" disabled<?php echo sel('content_type',''); ?>>Select focus</option>
                  <option<?php echo sel('content_type','Fashion & styling'); ?>>Fashion &amp; styling</option>
                  <option<?php echo sel('content_type','Beauty & skincare'); ?>>Beauty &amp; skincare</option>
                  <option<?php echo sel('content_type','Lifestyle & vlogs'); ?>>Lifestyle &amp; vlogs</option>
                  <option<?php echo sel('content_type','GRWM / try-on hauls'); ?>>GRWM / try-on hauls</option>
                  <option<?php echo sel('content_type','Modeling / editorial'); ?>>Modeling / editorial</option>
                  <option<?php echo sel('content_type','Mixed content'); ?>>Mixed content</option>
                </select>
                <?php echo err_html('content_type'); ?>
              </div>
              <div class="form-group">
                <label>Collaboration type *</label>
                <select name="preference" class="<?php echo trim(err_class('preference')); ?>" required>
                  <option value="" disabled<?php echo sel('preference',''); ?>>Select type</option>
                  <option<?php echo sel('preference','Product exchange'); ?>>Product exchange</option>
                  <option<?php echo sel('preference','Fixed rate'); ?>>Fixed rate</option>
                  <option<?php echo sel('preference','Open to discussion'); ?>>Open to discussion</option>
                  <option<?php echo sel('preference','Long-term collaboration'); ?>>Long-term collaboration</option>
                </select>
                <?php echo err_html('preference'); ?>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group full">
                <label>How did you hear about us? *</label>
                <select name="source" class="<?php echo trim(err_class('source')); ?>" required>
                  <option value="" disabled<?php echo sel('source',''); ?>>Select an option</option>
                  <option<?php echo sel('source','TikTok'); ?>>TikTok</option>
                  <option<?php echo sel('source','Instagram'); ?>>Instagram</option>
                  <option<?php echo sel('source','Facebook'); ?>>Facebook</option>
                  <option<?php echo sel('source','Friend / referral'); ?>>Friend / referral</option>
                  <option<?php echo sel('source','Email'); ?>>Email</option>
                  <option<?php echo sel('source','Other'); ?>>Other</option>
                </select>
                <?php echo err_html('source'); ?>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group full">
                <label>Tell us about your content (optional)</label>
                <textarea name="message" placeholder="Share your content style, audience, and why you'd like to collaborate with HIRAYA — this helps our team review your application"><?php echo val('message'); ?></textarea>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="section-label">Confirm</div>
            <div class="check-group<?php echo !empty($fieldErrors['checks']) ? ' is-invalid-group' : ''; ?>" id="checkGroup">
              <label class="check-item">
                <input type="checkbox" name="chk1" required<?php echo isset($_POST['chk1']) ? ' checked' : ''; ?>>
                <span class="box" aria-hidden="true"></span>
                <span class="txt">My social media links are correct and active.</span>
              </label>
              <label class="check-item">
                <input type="checkbox" name="chk2" required<?php echo isset($_POST['chk2']) ? ' checked' : ''; ?>>
                <span class="box" aria-hidden="true"></span>
                <span class="txt">I understand applications are subject to review and campaign availability.</span>
              </label>
              <label class="check-item">
                <input type="checkbox" name="chk3" required<?php echo isset($_POST['chk3']) ? ' checked' : ''; ?>>
                <span class="box" aria-hidden="true"></span>
                <span class="txt">I agree to be contacted about creator campaign updates.</span>
              </label>
              <label class="check-item">
                <input type="checkbox" name="chk4" required<?php echo isset($_POST['chk4']) ? ' checked' : ''; ?>>
                <span class="box" aria-hidden="true"></span>
                <span class="txt">I understand my application and content will be reviewed by the HIRAYA team.</span>
              </label>
            </div>
            <?php echo err_html('checks'); ?>
          </div>

          <?php if ($formError !== ''): ?>
          <div class="form-alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v6M12 16.5v.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span><?php echo htmlspecialchars($formError); ?></span>
          </div>
          <?php endif; ?>

          <button type="submit" class="form-submit">Submit application</button>
          <p class="form-note">Only selected applicants continue to verification and review.</p>
        </form>
      </div>

    </div>
  </section>
</main>

<script>
const phoneInput = document.getElementById('phoneInput');
const followersInput = document.getElementById('followersInput');

phoneInput.addEventListener('input', function () {
  let value = phoneInput.value.replace(/\D/g, '');
  if (!value.startsWith('639')) {
    value = '639' + value.replace(/^639/, '').replace(/^0+/, '');
  }
  phoneInput.value = value.slice(0, 12);
});

phoneInput.addEventListener('keydown', function (e) {
  const pos = phoneInput.selectionStart;
  if ((e.key === 'Backspace' || e.key === 'Delete') && pos <= 3) e.preventDefault();
});

phoneInput.addEventListener('paste', function (e) {
  e.preventDefault();
  let pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
  if (pasted.startsWith('09')) pasted = '639' + pasted.slice(2);
  else if (pasted.startsWith('9')) pasted = '63' + pasted;
  else if (!pasted.startsWith('639')) pasted = '639';
  phoneInput.value = pasted.slice(0, 12);
});

followersInput.addEventListener('input', function () {
  followersInput.value = followersInput.value.replace(/\D/g, '').slice(0, 9);
});

/* ---------------- Live validation ---------------- */
(function () {
  const form = document.getElementById('applyForm');
  if (!form) return;

  const rules = {
    full_name:    v => v.trim() !== ''                                   || 'Full name is required.',
    email:        v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())        || 'Enter a valid email address.',
    phone:        v => /^639\d{9}$/.test(v.trim())                       || 'Must start with 639 and be exactly 12 digits.',
    location:     v => v.trim() !== ''                                   || 'Location is required.',
    platform:     v => v !== ''                                          || 'Select your main platform.',
    followers:    v => /^\d{1,9}$/.test(v.trim())                        || 'Numbers only, up to 9 digits.',
    links:        v => v.trim() !== ''                                   || 'Add at least one social media link.',
    content_type: v => v !== ''                                          || 'Choose your main content focus.',
    preference:   v => v !== ''                                          || 'Choose a collaboration type.',
    source:       v => v !== ''                                          || 'Tell us how you heard about us.'
  };

  const errorEl = group => {
    let e = group.querySelector('.field-error');
    if (!e) { e = document.createElement('small'); e.className = 'field-error'; group.appendChild(e); }
    return e;
  };

  function check(name, markValid) {
    const el = form.elements[name];
    if (!el || !rules[name]) return true;
    const group = el.closest('.form-group');
    const res = rules[name](el.value);
    const ee = errorEl(group);
    if (res === true) {
      el.classList.remove('is-invalid');
      if (markValid && el.value.trim() !== '') el.classList.add('is-valid');
      ee.textContent = '';
      return true;
    }
    el.classList.remove('is-valid');
    el.classList.add('is-invalid');
    ee.textContent = res;
    return false;
  }

  // Checkbox group
  const checkGroup = document.getElementById('checkGroup');
  const boxes = ['chk1', 'chk2', 'chk3', 'chk4'].map(n => form.elements[n]);
  function checkBoxes() {
    const ok = boxes.every(b => b && b.checked);
    let ee = checkGroup.parentElement.querySelector('.field-error');
    if (!ee) { ee = document.createElement('small'); ee.className = 'field-error'; checkGroup.parentElement.appendChild(ee); }
    if (ok) { checkGroup.classList.remove('is-invalid-group'); ee.textContent = ''; }
    else    { checkGroup.classList.add('is-invalid-group');    ee.textContent = 'Please tick all four boxes to continue.'; }
    return ok;
  }

  Object.keys(rules).forEach(name => {
    const el = form.elements[name];
    if (!el) return;
    const isSelect = el.tagName === 'SELECT';
    el.addEventListener(isSelect ? 'change' : 'blur', () => check(name, true));
    el.addEventListener('input', () => { if (el.classList.contains('is-invalid')) check(name, true); });
  });
  boxes.forEach(b => b && b.addEventListener('change', checkBoxes));

  form.addEventListener('submit', e => {
    // Walk fields in form order and stop at the first problem — only that
    // field's error is shown. The rest stay quiet until the applicant
    // actually reaches them, so errors surface one at a time instead of
    // flooding the whole form on the first submit attempt.
    let firstBadName = null;
    for (const name of Object.keys(rules)) {
      const el = form.elements[name];
      if (!el) continue;
      if (rules[name](el.value) !== true) { firstBadName = name; break; }
    }

    if (firstBadName) {
      e.preventDefault();
      check(firstBadName, true);
      const el = form.elements[firstBadName];
      el.focus({ preventScroll: false });
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    if (!checkBoxes()) {
      e.preventDefault();
      checkGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
})();

/* ---------------- Hero video: no black flash, no loop-jump ---------------- */
(function () {
  var v = document.getElementById('applyVideo');
  if (!v) return;

  function reveal() { v.classList.add('is-ready'); }
  v.addEventListener('loadeddata', reveal);
  v.addEventListener('playing', reveal);

  // Native `loop` restarts the video at frame 0, which shows a brief black/
  // decoder frame in most browsers. Looping manually a fraction of a second
  // before the end removes that visible restart.
  v.addEventListener('timeupdate', function () {
    if (v.duration && v.duration - v.currentTime < 0.35) {
      v.currentTime = 0;
    }
  });
})();
</script>

<?php endif; ?>

</body>
</html>
