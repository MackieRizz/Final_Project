<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get candidate data
$candidate_id = $_POST['candidate_id'];
$position_id = $_POST['position_id'];

$response = array('success' => true, 'messages' => array());

try {
    // First get the image path to delete the image file
    $select_query = "SELECT image FROM candidate_positions WHERE id = ? AND position_id = ?";
    $select_stmt = $conn->prepare($select_query);
    $select_stmt->bind_param("ss", $candidate_id, $position_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image'];
        
        // Delete the image file if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $select_stmt->close();
    
    // Now delete the candidate record
    $delete_query = "DELETE FROM candidate_positions WHERE id = ? AND position_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ss", $candidate_id, $position_id);
    
    if (!$delete_stmt->execute()) {
        throw new Exception("Failed to delete candidate: " . $delete_stmt->error);
    }
    
    if ($delete_stmt->affected_rows > 0) {
        $response['messages'][] = "Successfully deleted candidate ID: $candidate_id";
    } else {
        throw new Exception("No candidate found with ID: $candidate_id");
    }
    
    $delete_stmt->close();
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['messages'][] = $e->getMessage();
    error_log($e->getMessage());
}

$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 