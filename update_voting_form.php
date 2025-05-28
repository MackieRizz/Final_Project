<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

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
        $candidates_query = "SELECT id, candidate_id, name, year, program, image FROM candidate_positions 
                           WHERE position_id = ? ORDER BY id";
        $stmt = $conn->prepare($candidates_query);
        $stmt->bind_param("s", $position_id);
        $stmt->execute();
        $candidates_result = $stmt->get_result();
        
        $candidates = array();
        while ($candidate = $candidates_result->fetch_assoc()) {
            $candidates[] = array(
                'id' => $candidate['id'],
                'candidate_id' => $candidate['candidate_id'],
                'name' => $candidate['name'],
                'year' => $candidate['year'],
                'program' => $candidate['program'],
                'image' => $candidate['image']
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
    
    // Write to voting_form_data.php
    if (file_put_contents('voting_form_data.php', $voting_form_content) === false) {
        throw new Exception("Failed to write voting form data file");
    }
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Voting form updated successfully'
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}

$conn->close();
?> 