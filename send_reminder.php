<?php
session_start();
include 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if student IDs were provided
$selectedIds = [];
if (isset($_POST['student_ids'])) {
    $selectedIds = json_decode($_POST['student_ids'], true);
    if (empty($selectedIds)) {
        echo json_encode(['success' => false, 'message' => 'No students selected']);
        exit();
    }
}

try {
    // Load email configuration
    if (!file_exists('mail_config.php')) {
        throw new Exception('Email configuration file not found. Please configure mail_config.php');
    }
    
    $mail_config = require 'mail_config.php';
    
    // Validate email configuration
    if (empty($mail_config['username']) || 
        $mail_config['username'] === 'your-email@gmail.com' || 
        strpos($mail_config['username'], '@gmail.com') === false) {
        throw new Exception('Please configure a valid Gmail address in mail_config.php');
    }

    if (empty($mail_config['password']) || 
        $mail_config['password'] === 'your-app-password' || 
        strlen($mail_config['password']) !== 16) {
        throw new Exception('Please configure a valid 16-character Gmail App Password in mail_config.php');
    }

    if ($mail_config['username'] !== $mail_config['from_email']) {
        throw new Exception('The username and from_email in mail_config.php must be the same Gmail address');
    }
    
    // First get the voting end time from scanner_settings
    $end_time_query = "SELECT end_datetime FROM scanner_settings ORDER BY id DESC LIMIT 1";
    $end_time_result = $conn->query($end_time_query);
    
    if (!$end_time_result || $end_time_result->num_rows === 0) {
        throw new Exception("No voting end time set. Please configure voting period in settings.");
    }
    
    $voting_end_time = $end_time_result->fetch_assoc()['end_datetime'];
    
    // Get selected students who haven't voted yet
    $placeholders = str_repeat('?,', count($selectedIds) - 1) . '?';
    $sql = "SELECT sr.student_id, sr.fullname, sr.email, sv.scan_time, sv.status
            FROM students_registration sr
            LEFT JOIN student_votes sv ON sr.student_id = sv.student_id
            WHERE sr.student_id IN ($placeholders)
            AND (sv.status IS NULL OR sv.status = 'Didn''t vote yet')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param(str_repeat('s', count($selectedIds)), ...$selectedIds);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No selected students need to vote.']);
        exit();
    }

    $success = true;
    $sent_count = 0;
    $errors = [];

    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = 3;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP DEBUG [$level]: $str");
        };
        
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        // SSL options
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Set default sender
        $mail->setFrom($mail_config['username'], 'EVSU Voting System');
        
        // Set UTF-8 encoding
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        // Test connection before proceeding
        try {
            if (!$mail->smtpConnect()) {
                throw new Exception("SMTP connection failed");
            }
            $mail->smtpClose();
            error_log("SMTP connection test successful");
        } catch (Exception $e) {
            error_log("SMTP connection failed: " . $e->getMessage());
            throw new Exception("Failed to connect to email server. Error: " . $e->getMessage());
        }

        while($row = $result->fetch_assoc()) {
            try {
                $mail->clearAddresses();
                $mail->clearAttachments();
                $mail->addAddress($row['email']);
                $mail->isHTML(true);
                $mail->Subject = "EVSU Election: Please Cast Your Vote";
                
                // Email body with scan time and voting end time info
                $scanTimeInfo = $row['scan_time'] ? "You scanned your QR code at " . date('F j, Y g:i A', strtotime($row['scan_time'])) : "You haven't started the voting process yet";
                $votingEndTime = date('F j, Y g:i A', strtotime($voting_end_time));
                
                $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
                    <h2 style='color: #2d0808;'>Hello " . htmlspecialchars($row['fullname']) . ",</h2>
                    <p>This is a reminder that you haven't cast your vote yet in the EVSU Election.</p>
                    <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                        <p><strong>Your Student ID:</strong> " . htmlspecialchars($row['student_id']) . "</p>
                        <p><strong>Status:</strong> " . $scanTimeInfo . "</p>
                        <p style='color: #ff4d4d;'><strong>⚠️ Important:</strong> Voting will end on " . $votingEndTime . "</p>
                        <p>Please visit the voting system as soon as possible to cast your vote before the deadline.</p>
                    </div>
                    <p style='margin-top: 20px;'>Best regards,<br>EVSU Election Administration</p>
                </div>";

                $mail->AltBody = "Hello " . $row['fullname'] . ",\n\n" .
                               "This is a reminder that you haven't cast your vote yet in the EVSU Election.\n\n" .
                               "Your Student ID: " . $row['student_id'] . "\n" .
                               "Status: " . $scanTimeInfo . "\n" .
                               "IMPORTANT: Voting will end on " . $votingEndTime . "\n\n" .
                               "Please visit the voting system as soon as possible to cast your vote before the deadline.\n\n" .
                               "Best regards,\nEVSU Election Administration";

                $mail->send();
                $sent_count++;
                
                // Small delay between emails to prevent rate limiting
                usleep(100000); // 0.1 second delay
                
            } catch (Exception $e) {
                $errors[] = "Failed to send to {$row['email']}: " . $mail->ErrorInfo;
                $success = false;
            }
        }
    } catch (Exception $e) {
        throw new Exception("SMTP Configuration Error: " . $e->getMessage());
    }

    $response = [
        'success' => $success && $sent_count > 0,
        'message' => $sent_count > 0 
            ? "Successfully sent reminders to {$sent_count} students" . (!empty($errors) ? " (with some errors)" : "")
            : "Failed to send reminders. Please check your email configuration.",
        'count' => $sent_count,
        'errors' => $errors,
        'shouldRedirect' => true
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'count' => 0,
        'errors' => [$e->getMessage()],
        'shouldRedirect' => false
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?> 