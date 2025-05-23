<?php

$qrDir = __DIR__ . '/../qr_codes/';
if (isset($_GET['filename'])) {
    $filename = basename($_GET['filename']); // Prevent directory traversal
    $filepath = $qrDir . $filename;
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "No file specified.";
}