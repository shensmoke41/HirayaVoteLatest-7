<?php
//require_once __DIR__ . '/protection.php';
session_start();

require __DIR__ . '/config.php';

$referenceNumber = $_SESSION['hiraya_reference'] ?? '';
$fullName = $_SESSION['hiraya_full_name'] ?? '';
$identityMethod = $_SESSION['hiraya_identity_method'] ?? 'Identity Verification';

if ($referenceNumber === '') {
    header("Location: apply.php");
    exit;
}

if ($fullName === '') {
    $fullName = 'Creator Applicant';
}

$telegramBotUsername = ltrim($telegram_bot_username ?? '', '@');

$telegramDirectLink = "https://t.me/" . $telegramBotUsername . "?start=" . rawurlencode($referenceNumber);

$telegramCopyMessage =
    "Hello HIRAYA, I would like to complete my creator verification.\n\n" .
    "Name: " . $fullName . "\n" .
    "Reference Number: " . $referenceNumber . "\n\n" .
    "I will send my best pictures or portfolio here.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="/hiraya/files/images/biglogo.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Final Verification – <?php echo htmlspecialchars($brandName); ?></title>

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="files/css/confirm2.css">
</head>

<body>

<div class="wrapper">
  <div class="container">

    <div class="logo-text">HIRAYA</div>
    <div class="brand">Creator Verification</div>

    <div class="badge">Final Step Ready</div>

    <h1 class="title">Complete Your Final Submission</h1>

    <p class="subtitle">
      Your application and verification progress have been recorded. To complete your creator application, copy your reference number and send it to HIRAYA on Telegram with your best pictures or portfolio.
    </p>

    <div class="ref-card">
      <div class="ref-label">Reference Number</div>
      <div class="ref-number" id="refNumber"><?php echo htmlspecialchars($referenceNumber); ?></div>

      <div class="name-box">
        Applicant: <strong><?php echo htmlspecialchars($fullName); ?></strong>
      </div>
    </div>

    <div class="urgency">
      ⏳ Final step: send your reference number together with your pictures or portfolio so the team can match your application with your submitted materials.
    </div>

    <div class="verification-steps">
      <div class="verification-step done">
        <div class="step-number">✓</div>
        <div class="step-content">
          <span>Step 01 Completed</span>
          <h3>Application submitted</h3>
          <p>Your creator application form has been successfully received and reserved under your reference number.</p>
        </div>
      </div>

      <div class="verification-step done">
        <div class="step-number">✓</div>
        <div class="step-content">
          <span>Step 02 Completed</span>
          <h3><?php echo htmlspecialchars($identityMethod); ?> completed</h3>
          <p>Your selected verification method has been recorded. You are now ready for the final submission step.</p>
        </div>
      </div>

      <div class="verification-step active">
        <div class="step-number">03</div>
        <div class="step-content">
          <span>Final Step</span>
          <h3>Send your reference and photos</h3>
          <p>Copy your reference number, open Telegram, and send it with your best pictures or portfolio.</p>
        </div>
      </div>

      <div class="verification-note">
        Final review will continue only after HIRAYA receives your reference number and photo or portfolio submission through Telegram.
      </div>
    </div>

    <div class="copy-alert" id="copyAlert">
      Message copied. Paste it in Telegram and attach your best pictures or portfolio.
    </div>

    <button class="btn" type="button" onclick="copyReference()">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M8 8h10v12H8V8z" stroke="#241018" stroke-width="2"/>
        <path d="M6 16H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" stroke="#241018" stroke-width="2"/>
      </svg>
      Copy Reference Number
    </button>

    <button class="btn btn-primary" type="button" onclick="openTelegram()">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M21.5 3.8 2.9 11.1c-1.3.5-1.3 1.2-.2 1.5l4.8 1.5 1.8 5.6c.2.7.4 1 .8 1 .5 0 .7-.2 1-.5l2.4-2.3 5 3.7c.9.5 1.5.3 1.8-.9l3.2-15.2c.3-1.3-.5-1.9-1.9-1.3z" fill="#fff"/>
      </svg>
      Copy Message & Open Telegram
    </button>

  

    <div class="footer">
      🔐 Secure creator verification • HIRAYA Philippines Campaign
    </div>

  </div>
</div>

<div id="loading" class="loading">
  <div class="spinner"></div>
  <p>Opening Telegram verification...</p>
</div>

<script>
const referenceNumber = <?php echo json_encode($referenceNumber); ?>;
const fullName = <?php echo json_encode($fullName); ?>;
const telegramDirectLink = <?php echo json_encode($telegramDirectLink); ?>;

const telegramMessage =
  "Hello HIRAYA, I would like to complete my creator verification.\n\n" +
  "Name: " + fullName + "\n" +
  "Reference Number: " + referenceNumber + "\n\n" +
  "I will send my best pictures or portfolio here.";

function copyText(text) {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text);
  }

  const textarea = document.createElement("textarea");
  textarea.value = text;
  textarea.style.position = "fixed";
  textarea.style.left = "-9999px";
  document.body.appendChild(textarea);
  textarea.focus();
  textarea.select();

  return new Promise((resolve, reject) => {
    try {
      document.execCommand("copy");
      resolve();
    } catch (err) {
      reject(err);
    } finally {
      document.body.removeChild(textarea);
    }
  });
}

function showCopyAlert(text) {
  const alertBox = document.getElementById("copyAlert");
  alertBox.textContent = text;
  alertBox.style.display = "block";

  setTimeout(() => {
    alertBox.style.display = "none";
  }, 3500);
}

function copyReference() {
  copyText(referenceNumber).then(() => {
    showCopyAlert("Reference number copied. Send it on Telegram with your pictures or portfolio.");
  });
}

function openTelegram() {
  copyText(telegramMessage).then(() => {
    showCopyAlert("Message copied. Paste it in Telegram and attach your pictures or portfolio.");

    document.getElementById("loading").style.display = "flex";

    setTimeout(() => {
      window.location.href = telegramDirectLink;
    }, 900);
  });
}


</script>

</body>
</html>
