<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get position data
$position_id = $_POST['position_id'];
$position = $_POST['position'];

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    chmod($upload_dir, 0777); // Ensure directory is writable
}

// Get the maximum existing id to ensure unique IDs for new candidates
$max_candidate_id_query = "SELECT MAX(id) as max_id FROM candidate_positions";
$result = $conn->query($max_candidate_id_query);
$max_candidate_id = 0;
if ($result && $row = $result->fetch_assoc()) {
    $max_candidate_id = intval($row['max_id']);
}

// Prepare insert statement
$insert_query = "INSERT INTO candidate_positions (id, position_id, position, name, year, program, image, background) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_query);

$response = array('success' => true, 'messages' => array());

// Get arrays from form
$names = isset($_POST['name']) ? $_POST['name'] : array();
$years = isset($_POST['year']) ? $_POST['year'] : array();
$programs = isset($_POST['program']) ? $_POST['program'] : array();
$backgrounds = isset($_POST['background']) ? $_POST['background'] : array();

// Validate that we have the same number of entries for each field
$count_names = count($names);
$count_years = count($years);
$count_programs = count($programs);
$count_backgrounds = count($backgrounds);
$count_files = isset($_FILES['image']['name']) ? count($_FILES['image']['name']) : 0;

if ($count_names !== $count_years || $count_names !== $count_programs || $count_names !== $count_files || $count_names !== $count_backgrounds) {
    $response['success'] = false;
    $response['messages'][] = "Mismatch in the number of inputs. Please ensure all fields are filled for each candidate.";
    echo json_encode($response);
    exit;
}

// Loop through each candidate
foreach ($names as $index => $name) {
    try {
        // Skip empty entries
        if (empty($name) && empty($years[$index]) && empty($programs[$index])) {
            continue;
        }

        // Validate required fields
        if (empty($name) || empty($years[$index]) || empty($programs[$index])) {
            throw new Exception("All fields are required for candidate at position " . ($index + 1));
        }

        // Handle image upload
        if (isset($_FILES['image']['name'][$index]) && !empty($_FILES['image']['name'][$index])) {
            // Check for upload errors
            if ($_FILES['image']['error'][$index] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload error for candidate " . $name . ": " . $_FILES['image']['error'][$index]);
            }

            $file = $_FILES['image']['tmp_name'][$index];
            $file_name = $_FILES['image']['name'][$index];
            
            // Validate file type
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
            $file_type = mime_content_type($file);
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid file type for candidate " . $name . ". Only JPG, PNG, and GIF are allowed.");
            }
            
            // Generate unique filename for new candidate
            $unique_filename = uniqid('new_') . '_' . $file_name;
            $image_path = $upload_dir . $unique_filename;
            
            // Move the uploaded file
            if (!move_uploaded_file($file, $image_path)) {
                throw new Exception("Failed to move uploaded file for candidate: " . $name);
            }
            
            chmod($image_path, 0644); // Set proper file permissions
            
            // Generate new unique ID for the candidate
            $max_candidate_id++;
            $new_candidate_id = $max_candidate_id;
            
            // Insert new candidate
            $insert_stmt = $conn->prepare("INSERT INTO candidate_positions (id, position_id, position, name, year, program, image, background) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("iissssss",
                $new_candidate_id,
                $position_id,
                $position,
                $name,
                $years[$index],
                $programs[$index],
                $image_path,
                $backgrounds[$index]
            );
            
            if (!$insert_stmt->execute()) {
                // If insert fails, delete the uploaded image
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                throw new Exception("Failed to insert candidate: " . $insert_stmt->error);
            }
            
            $response['messages'][] = "New candidate added with ID: $new_candidate_id, Name: $name";
        } else {
            throw new Exception("No image uploaded for candidate: " . $name);
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['messages'][] = $e->getMessage();
        error_log($e->getMessage());
    }
}

$insert_stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>