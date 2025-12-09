<?php
$connection = fsockopen("smtp.gmail.com", 587, $errno, $errstr, 10);

if (!$connection) {
    echo "❌ Tidak bisa connect ke gmail smtp: $errstr ($errno)";
} else {
    echo "✔ Berhasil connect ke Gmail SMTP!";
    fclose($connection);
}