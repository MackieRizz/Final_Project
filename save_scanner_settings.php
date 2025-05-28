<?php
// Prevent any output before headers
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

if (!isset($data['start_datetime']) || !isset($data['end_datetime'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    // Convert to PHP DateTime objects for validation
    $start = new DateTime($data['start_datetime']);
    $end = new DateTime($data['end_datetime']);
    $now = new DateTime();

    // Validate dates
    if ($start >= $end) {
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit();
    }

    // Format dates for MySQL
    $start_datetime = $start->format('Y-m-d H:i:s');
    $end_datetime = $end->format('Y-m-d H:i:s');

    // First, delete any existing settings
    if (!$conn->query("DELETE FROM scanner_settings")) {
        throw new Exception("Failed to clear existing settings: " . $conn->error);
    }

    // Insert new settings
    $stmt = $conn->prepare("INSERT INTO scanner_settings (start_datetime, end_datetime) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $start_datetime, $end_datetime);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Scanner settings saved successfully',
        'settings' => [
            'start_datetime' => $start_datetime,
            'end_datetime' => $end_datetime
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error saving settings: ' . $e->getMessage()
    ]);
}

$conn->close(); 