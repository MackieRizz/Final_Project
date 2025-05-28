<?php

// Set headers to prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$qrDir = __DIR__ . '/../qr_codes/';
if (isset($_GET['filename'])) {
    $filename = basename($_GET['filename']); // Prevent directory traversal
    $filepath = $qrDir . $filename;
    
    if (file_exists($filepath)) {
        // Get file size
        $filesize = filesize($filepath);
        
        // Set headers
        header('Content-Type: image/png');
        header('Content-Length: ' . $filesize);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read and output file
        readfile($filepath);
        exit;
    } else {
        header('HTTP/1.0 404 Not Found');
        echo "File not found.";
        exit;
    }
} else {
    header('HTTP/1.0 400 Bad Request');
    echo "No file specified.";
    exit;
}