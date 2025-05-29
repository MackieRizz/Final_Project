<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Truncate the candidate_positions table
    $query = "TRUNCATE TABLE candidate_positions";
    $result = $conn->query($query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'All candidates deleted successfully']);
    } else {
        throw new Exception("Failed to delete candidates");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 