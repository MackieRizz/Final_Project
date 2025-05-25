<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the form data
$position_id = isset($_POST['position_id']) ? $_POST['position_id'] : '';
$position = isset($_POST['position']) ? $_POST['position'] : '';
$names = isset($_POST['name']) ? $_POST['name'] : array();
$years = isset($_POST['year']) ? $_POST['year'] : array();
$programs = isset($_POST['program']) ? $_POST['program'] : array();
$images = isset($_FILES['image']) ? $_FILES['image'] : array();

// Debug output
error_log("Position ID: " . print_r($position_id, true));
error_log("Position: " . print_r($position, true));

// Validate that we have all required data
if (empty($position_id) || empty($position)) {
    die("Error: Position ID and Position are required");
}

if (count($names) !== count($years) || count($names) !== count($programs)) {
    die("Error: Mismatch between number of inputs");
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/candidates/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Prepare the statement once
    $stmt = $conn->prepare("INSERT INTO candidate_positions (position, position_id, name, year, program, image) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    // Bind and execute for each candidate
    for ($i = 0; $i < count($names); $i++) {
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']['name'][$i]) && $_FILES['image']['error'][$i] === UPLOAD_ERR_OK) {
            $file_extension = pathinfo($_FILES['image']['name'][$i], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $upload_path)) {
                $image_path = $upload_path;
            } else {
                throw new Exception("Failed to upload image for " . $names[$i]);
            }
        }

        // Debug output for each iteration
        error_log("Inserting record $i:");
        error_log("Position: $position");
        error_log("Position ID: $position_id");
        error_log("Name: " . $names[$i]);
        error_log("Year: " . $years[$i]);
        error_log("Program: " . $programs[$i]);
        error_log("Image Path: $image_path");

        $stmt->bind_param("ssssss", $position, $position_id, $names[$i], $years[$i], $programs[$i], $image_path);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert record for " . $names[$i] . ": " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();
    $stmt->close();

    // Return success
    header("Location: add_candidate_dashboard.php?success=1");
    exit();

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    // Delete any uploaded images if there was an error
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    die("Error: " . $e->getMessage());
}
?>
   