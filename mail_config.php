<?php
/*
 * Email Configuration for EVSU Voting System
 * 
 * IMPORTANT: Your Gmail App Password must:
 * - Be exactly 16 characters long
 * - Include spaces between each 4 characters
 * - Look like this format: xxxx xxxx xxxx xxxx
 * - Be generated from Google Account > Security > App passwords
 */

$config = [
    'host' => 'smtp.gmail.com',
    'username' => 'rubytinunga@gmail.com',
    'password' => 'geolpcadojbdszok',
    'smtp_secure' => 'ssl',  // Use SSL encryption
    'port' => 465,          // SSL port
    'from_email' => 'rubytinunga@gmail.com',
    'from_name' => 'EVSU Voting System',
    'smtp_options' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]
];

// Validate configuration
if (empty($config['username']) || !filter_var($config['username'], FILTER_VALIDATE_EMAIL)) {
    error_log('Invalid Gmail address in configuration');
    throw new Exception('Invalid Gmail address configuration');
}

// Simple length check for the App Password
$password = str_replace(' ', '', $config['password']); // Remove spaces
if (strlen($password) !== 16) {
    error_log('Gmail App Password must be exactly 16 characters (excluding spaces)');
    throw new Exception('Invalid Gmail App Password length');
}

// Validate matching email addresses
if ($config['username'] !== $config['from_email']) {
    error_log('Username and from_email must match in configuration');
    throw new Exception('Mismatched email addresses in configuration');
}

return $config;
?> 

