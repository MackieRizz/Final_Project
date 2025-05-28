<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get position data
$position_id = $_POST['position_id'];
$candidate_id = $_POST['candidate_id'];

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    chmod($upload_dir, 0777);
}

// Prepare update statement
$update_query = "UPDATE candidate_positions SET name = ?, year = ?, program = ?, image = ? WHERE id = ? AND position_id = ?";
$update_stmt = $conn->prepare($update_query);

$response = array('success' => true, 'messages' => array());

try {
    $name = $_POST['name'];
    $year = $_POST['year'];
    $program = $_POST['program'];
    $existing_image = $_POST['existing_image'];
    
    // Initialize image path with existing image
    $image_path = $existing_image;
    
    // Handle new image upload if provided
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $file = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        
        // Generate unique filename
        $unique_filename = uniqid('update_') . '_' . $file_name;
        $new_image_path = $upload_dir . $unique_filename;
        
        // Move the uploaded file
        if (move_uploaded_file($file, $new_image_path)) {
            // Delete old image if exists
            if (!empty($existing_image) && file_exists($existing_image)) {
                unlink($existing_image);
            }
            $image_path = $new_image_path;
            chmod($new_image_path, 0644);
        } else {
            throw new Exception("Failed to upload new image");
        }
    }
    
    // Update candidate
    $update_stmt->bind_param("ssssss",
        $name,
        $year,
        $program,
        $image_path,
        $candidate_id,
        $position_id
    );
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update candidate: " . $update_stmt->error);
    }
    
    $response['messages'][] = "Successfully updated candidate ID: $candidate_id";
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['messages'][] = $e->getMessage();
    error_log($e->getMessage());
}

$update_stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 