<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_verified']) || $_SESSION['admin_verified'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$newPasscode = $_POST['new_passcode'] ?? '';

if (strlen($newPasscode) !== 8) {
    echo json_encode(['success' => false, 'message' => 'Passcode must be 8 digits']);
    exit;
}

try {
    // Update the passcode in the database
    $stmt = $conn->prepare("UPDATE admin_passcode SET passcode = ? WHERE id = 1");
    $stmt->bind_param("s", $newPasscode);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update passcode']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?> 