<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get position data
$position_id = $_POST['position_id'];
$position = $_POST['position'];

// First, delete existing candidates for this position
$delete_query = "DELETE FROM candidate_positions WHERE position_id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param("s", $position_id);
$stmt->execute();
$stmt->close();

// Prepare insert statement
$insert_query = "INSERT INTO candidate_positions (position_id, position, name, year, program, image) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);

// Get arrays from form
$names = $_POST['name'];
$years = $_POST['year'];
$programs = $_POST['program'];
$existing_images = $_POST['existing_image'];

// Loop through candidates and insert/update
foreach ($names as $index => $name) {
    $image_path = $existing_images[$index]; // Use existing image by default
    
    // Check if new image was uploaded
    if (isset($_FILES['image']['name'][$index]) && $_FILES['image']['name'][$index] != '') {
        $file = $_FILES['image']['tmp_name'][$index];
        $file_name = $_FILES['image']['name'][$index];
        $upload_dir = 'uploads/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $image_path = $upload_dir . uniqid() . '_' . $file_name;
        
        // Move uploaded file
        if (move_uploaded_file($file, $image_path)) {
            // Delete old image if it exists
            if (!empty($existing_images[$index]) && file_exists($existing_images[$index])) {
                unlink($existing_images[$index]);
            }
        }
    }
    
    // Insert new record
    $stmt->bind_param("ssssss", 
        $position_id,
        $position,
        $name,
        $years[$index],
        $programs[$index],
        $image_path
    );
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "Success";
?>
   