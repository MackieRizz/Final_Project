<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = $_POST['candidate_id'];
    
    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM candidate_positions WHERE id = ?");
    $stmt->bind_param("i", $candidate_id);
    
    if ($stmt->execute()) {
        echo "Candidate deleted successfully";
    } else {
        echo "Error deleting candidate: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "Invalid request";
}

$conn->close();
?> 