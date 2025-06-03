<?php
include 'db.php';

if (isset($_GET['position_id'])) {
    $position_id = $_GET['position_id'];
    
    $query = "SELECT * FROM candidate_positions WHERE position_id = ? ORDER BY candidate_id";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $position_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $candidates = array();
    while ($row = $result->fetch_assoc()) {
        $candidates[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'year' => $row['year'],
            'program' => $row['program'],
            'image' => $row['image'],
            'background' => $row['background']
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($candidates);
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(array('error' => 'Position ID not provided'));
}

$conn->close();
?>