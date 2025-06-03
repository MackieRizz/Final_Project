<?php
// Start logging
file_put_contents('save_position_errors.log', "\n[" . date('Y-m-d H:i:s') . "] Script started\n", FILE_APPEND);

include 'db.php';

// Check database connection
if ($conn->connect_error) {
    $error_message = "Database Connection failed: " . $conn->connect_error;
    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] " . $error_message . "\n", FILE_APPEND);
    $response = array('success' => false, 'messages' => array($error_message));
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Database connected\n", FILE_APPEND);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Keep display_errors off in production for security
ini_set('log_errors', 1);
ini_set('error_log', 'save_position_errors.log');

// Initialize response array
$response = array('success' => true, 'messages' => array());

try {
    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Inside try block\n", FILE_APPEND);

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

    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Upload directory checked/created\n", FILE_APPEND);

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
    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Transaction started\n", FILE_APPEND);

    // Prepare statements
    // Prepare the insert statement once before the loop
    $insert_stmt = $conn->prepare("INSERT INTO candidate_positions (id, candidate_id, position_id, position, name, year, program, image, background) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$insert_stmt) {
        throw new Exception("Failed to prepare insert statement: " . $conn->error);
    }
    $update_stmt = $conn->prepare("UPDATE candidate_positions SET name = ?, year = ?, program = ?, image = ?, background = ? WHERE id = ? AND position_id = ?");
    if (!$update_stmt) {
        // Close insert_stmt if update_stmt preparation fails
        $insert_stmt->close();
        throw new Exception("Failed to prepare update statement: " . $conn->error);
    }

    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Statements prepared\n", FILE_APPEND);

    // Track processed candidates
    $processed_ids = array();

    // Process each candidate
    foreach ($names as $index => $name) {
        file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Processing candidate index " . $index . " Name: " . $name . "\n", FILE_APPEND);

        // Skip empty entries
        if (empty($name) && empty($years[$index]) && empty($programs[$index])) {
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Skipping empty entry at index " . $index . "\n", FILE_APPEND);
            continue;
        }

        // Validate required fields
        if (empty($name) || empty($years[$index]) || empty($programs[$index]) || empty($backgrounds[$index])) {
             $error_message = "All fields are required for candidate at position " . ($index + 1);
             file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Validation failed: " . $error_message . "\n", FILE_APPEND);
             throw new Exception($error_message);
         }

        if ($is_new[$index] == '1') {
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Handling new candidate at index " . $index . "\n", FILE_APPEND);
            // Handle new candidate
            if (!isset($_FILES['image']['name'][$index]) || empty($_FILES['image']['name'][$index])) {
                $error_message = "Image is required for new candidate: " . $name;
                file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] " . $error_message . "\n", FILE_APPEND);
                throw new Exception($error_message);
            }

            // Handle image upload
            $file = $_FILES['image']['tmp_name'][$index];
            $file_name = $_FILES['image']['name'][$index];
            $unique_filename = uniqid('candidate_') . '_' . $file_name;
            $image_path = $upload_dir . $unique_filename;

            if (!move_uploaded_file($file, $image_path)) {
                $error_message = "Failed to move uploaded file for candidate: " . $name;
                file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] " . $error_message . "\n", FILE_APPEND);
                throw new Exception($error_message);
            }
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Image moved to " . $image_path . "\n", FILE_APPEND);

            // Insert new candidate
            $max_id++;
            $max_candidate_id++;

            // Bind parameters and execute the prepared insert statement
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
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Insert statement bound for index " . $index . "\n", FILE_APPEND);

            if (!$insert_stmt->execute()) {
                // If insert fails, delete the uploaded image
                if (file_exists($image_path)) {
                    unlink($image_path);
                    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Failed insert, deleted image " . $image_path . "\n", FILE_APPEND);
                }
                $error_message = "Failed to insert candidate: " . $insert_stmt->error;
                file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] " . $error_message . "\n", FILE_APPEND);
                throw new Exception($error_message);
            }

            $processed_ids[] = $max_id;
            $response['messages'][] = "Added new candidate: " . $name;
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Candidate " . $name . " added successfully\n", FILE_APPEND);

        } else {
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Handling existing candidate at index " . $index . "\n", FILE_APPEND);
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
                    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] New image uploaded to " . $new_image_path . "\n", FILE_APPEND);
                    // Delete old image if exists
                    if (!empty($image_path) && file_exists($image_path)) {
                        unlink($image_path);
                        file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Old image deleted " . $image_path . "\n", FILE_APPEND);
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
                $error_message = "Failed to update candidate: " . $update_stmt->error;
                file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] " . $error_message . "\n", FILE_APPEND);
                throw new Exception($error_message);
            }

            $processed_ids[] = $candidate_id;
            $response['messages'][] = "Updated candidate: " . $name;
            file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Candidate " . $name . " updated successfully\n", FILE_APPEND);
        }
    }

    // Commit transaction
    $conn->commit();
    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Transaction committed\n", FILE_APPEND);

    // Close prepared statements
    $insert_stmt->close();
    $update_stmt->close();

} catch (Exception $e) {
    file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Caught exception: " . $e->getMessage() . "\n", FILE_APPEND);
    // Rollback transaction on error
    if ($conn->connect_errno === 0) {
        $conn->rollback();
        file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Transaction rolled back\n", FILE_APPEND);
    }
    $response['success'] = false;
    $response['messages'][] = $e->getMessage();
}

// Close database connection
$conn->close();
file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Database connection closed\n", FILE_APPEND);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

// End logging
file_put_contents('save_position_errors.log', "[" . date('Y-m-d H:i:s') . "] Script finished\n", FILE_APPEND);
?>
