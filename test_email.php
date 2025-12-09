<?php
require_once 'includes/email_sender.php';

echo "Testing Email Configuration...\n";

$result = sendRegistrationNotification(
    "chandra",
    "672022001",
    "proposal",
    date('d F Y H:i:s')
);

if ($result === true) {
    echo "✅ Email test berhasil dikirim\n";
} else {
    echo "❌ Gagal mengirim email test: $result\n";
}
?>
