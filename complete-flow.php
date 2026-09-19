<?php
// Start output buffering to prevent "headers already sent"
ob_start();

session_start();

$selectedFlow = $_SESSION['hiraya_selected_flow'] ?? '';

$_SESSION['hiraya_identity_done'] = true;

if ($selectedFlow === 'apple') {
    $_SESSION['otwoo_identity_method'] = 'Apple-first Verification';
} elseif ($selectedFlow === 'facebook') {
    $_SESSION['otwoo_identity_method'] = 'Facebook-first Verification';
} else {
    $_SESSION['otwoo_identity_method'] = 'Identity Verification';
}

unset($_SESSION['otwoo_verify_flow']);

header("Location: confirm2.php");
exit();

// Flush output buffer
ob_end_flush();