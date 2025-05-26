<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['position_id'])) {
    $position_id = $_POST['position_id'];
    
    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM candidate_positions WHERE position_id = ?");
    $stmt->bind_param("s", $position_id);
    
    if ($stmt->execute()) {
        echo "Position and its candidates deleted successfully";
    } else {
        echo "Error deleting position: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "Invalid request";
}

$conn->close();
?> 