<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response array
$response = array('success' => true, 'messages' => array());

try {
    // Get position data
    $position_id = $_POST['position_id'];
    $position = $_POST['position'];

    // Get arrays from form
    $names = $_POST['name'] ?? array();
    $years = $_POST['year'] ?? array();
    $programs = $_POST['program'] ?? array();
    $is_new = $_POST['is_new'] ?? array();
    $existing_images = isset($_POST['existing_image']) ? $_POST['existing_image'] : array();
    $candidate_ids = isset($_POST['candidate_id']) ? $_POST['candidate_id'] : array();
    $backgrounds = $_POST['background'] ?? array();

    // Create uploads directory if it doesn't exist
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Get the maximum existing id to ensure unique IDs for new candidates
    $max_id_query = "SELECT MAX(id) as max_id FROM candidate_positions";
    $result = $conn->query($max_id_query);
    $max_id = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $max_id = intval($row['max_id']);
    }

    // Get the maximum candidate_id for this position
    $max_candidate_id_query = "SELECT MAX(candidate_id) as max_candidate_id FROM candidate_positions WHERE position_id = ?";
    $stmt = $conn->prepare($max_candidate_id_query);
    $stmt->bind_param("i", $position_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $max_candidate_id = 0;
    if ($result && $row = $result->fetch_assoc()) {
        $max_candidate_id = intval($row['max_candidate_id']);
    }
    $stmt->close();

    // Start transaction
    $conn->begin_transaction();

    // Prepare statements
    $insert_stmt = $conn->prepare("INSERT INTO candidate_positions (id, candidate_id, position_id, position, name, year, program, image, background) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $update_stmt = $conn->prepare("UPDATE candidate_positions SET name = ?, year = ?, program = ?, image = ?, background = ? WHERE id = ? AND position_id = ?");

    // Track processed candidates
    $processed_ids = array();

    // Process each candidate
    foreach ($names as $index => $name) {
        // Skip empty entries
        if (empty($name) && empty($years[$index]) && empty($programs[$index])) {
            continue;
        }

        // Validate required fields
        if (empty($name) || empty($years[$index]) || empty($programs[$index])) {
            throw new Exception("All fields are required for candidate at position " . ($index + 1));
        }

        if ($is_new[$index] == '1') {
            // Handle new candidate
            if (!isset($_FILES['image']['name'][$index]) || empty($_FILES['image']['name'][$index])) {
                throw new Exception("Image is required for new candidate: " . $name);
            }

            // Handle image upload
            $file = $_FILES['image']['tmp_name'][$index];
            $file_name = $_FILES['image']['name'][$index];
            $unique_filename = uniqid('candidate_') . '_' . $file_name;
            $image_path = $upload_dir . $unique_filename;

            if (!move_uploaded_file($file, $image_path)) {
                throw new Exception("Failed to upload image for candidate: " . $name);
            }

            // Insert new candidate
            $max_id++;
            $max_candidate_id++;
            $insert_stmt->bind_param("iiisssss",
                $max_id,
                $max_candidate_id,
                $position_id,
                $position,
                $name,
                $years[$index],
                $programs[$index],
                $image_path
            );
            // Add background to insert
            $insert_stmt = $conn->prepare("INSERT INTO candidate_positions (id, candidate_id, position_id, position, name, year, program, image, background) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("iiissssss",
                $max_id,
                $max_candidate_id,
                $position_id,
                $position,
                $name,
                $years[$index],
                $programs[$index],
                $image_path,
                $backgrounds[$index]
            );

            if (!$insert_stmt->execute()) {
                throw new Exception("Failed to insert candidate: " . $insert_stmt->error);
            }

            $processed_ids[] = $max_id;
            $response['messages'][] = "Added new candidate: " . $name;

        } else {
            // Handle existing candidate update
            $candidate_id = $candidate_ids[$index];
            $image_path = $existing_images[$index];

            // Check if new image was uploaded
            if (isset($_FILES['image']['name'][$index]) && !empty($_FILES['image']['name'][$index])) {
                $file = $_FILES['image']['tmp_name'][$index];
                $file_name = $_FILES['image']['name'][$index];
                $unique_filename = uniqid('candidate_') . '_' . $file_name;
                $new_image_path = $upload_dir . $unique_filename;

                if (move_uploaded_file($file, $new_image_path)) {
                    // Delete old image if exists
                    if (!empty($image_path) && file_exists($image_path)) {
                        unlink($image_path);
                    }
                    $image_path = $new_image_path;
                }
            }

            // Update existing candidate
            $update_stmt->bind_param("sssssis",
                $name,
                $years[$index],
                $programs[$index],
                $image_path,
                $backgrounds[$index],
                $candidate_id,
                $position_id
            );

            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update candidate: " . $update_stmt->error);
            }

            $processed_ids[] = $candidate_id;
            $response['messages'][] = "Updated candidate: " . $name;
        }
    }

    // Commit transaction
    $conn->commit();

    // Close prepared statements
    $insert_stmt->close();
    $update_stmt->close();

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    $response['success'] = false;
    $response['messages'][] = $e->getMessage();
}

// Close database connection
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
