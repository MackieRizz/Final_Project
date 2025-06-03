<?php
// Prevent any output before headers
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    // Get all positions and their candidates
    $query = "SELECT DISTINCT position_id, position FROM candidate_positions ORDER BY position_id";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Error fetching positions: " . $conn->error);
    }

    $positions = array();
    
    while ($row = $result->fetch_assoc()) {
        $position_id = $row['position_id'];
        $position = $row['position'];
        
        // Get candidates for this position
        $candidates_query = "SELECT id, candidate_id, name, year, program, image, background FROM candidate_positions 
                           WHERE position_id = ? ORDER BY id";
        $stmt = $conn->prepare($candidates_query);
        if (!$stmt) {
            throw new Exception("Error preparing candidate query: " . $conn->error);
        }
        
        $stmt->bind_param("s", $position_id);
        if (!$stmt->execute()) {
            throw new Exception("Error executing candidate query: " . $stmt->error);
        }
        
        $candidates_result = $stmt->get_result();
        
        $candidates = array();
        while ($candidate = $candidates_result->fetch_assoc()) {
            $candidates[] = array(
                'id' => $candidate['id'],
                'candidate_id' => $candidate['candidate_id'],
                'name' => $candidate['name'],
                'year' => $candidate['year'],
                'program' => $candidate['program'],
                'image' => $candidate['image'],
                'background' => $candidate['background']
            );
        }
        
        $positions[] = array(
            'position_id' => $position_id,
            'position' => $position,
            'candidates' => $candidates
        );
        
        $stmt->close();
    }
    
    // Create or update the voting form file
    $voting_form_content = "<?php\n";
    $voting_form_content .= "// This file is auto-generated. Do not edit manually.\n";
    $voting_form_content .= "\$positions = " . var_export($positions, true) . ";\n";
    $voting_form_content .= "?>\n";
    
    // Define the file path
    $file_path = __DIR__ . '/voting_form_data.php';
    
    // Check if directory is writable
    if (!is_writable(__DIR__)) {
        throw new Exception("Directory is not writable. Please check permissions.");
    }
    
    // If file exists, check if it's writable
    if (file_exists($file_path) && !is_writable($file_path)) {
        throw new Exception("Existing file is not writable. Please check file permissions.");
    }
    
    // Try to write the file
    $write_result = file_put_contents($file_path, $voting_form_content);
    if ($write_result === false) {
        throw new Exception("Failed to write file. Path: " . $file_path);
    }
    
    // Verify the file was written correctly
    if (!file_exists($file_path)) {
        throw new Exception("File was not created successfully.");
    }
    
    // Set proper permissions (readable by web server, writable by owner)
    chmod($file_path, 0644);
    
    echo json_encode([
        'success' => true,
        'message' => 'Voting form updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating voting form: ' . $e->getMessage()
    ]);
}

$conn->close();
?>