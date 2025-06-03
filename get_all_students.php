<?php
include 'db.php';

header('Content-Type: application/json');

try {
    // Query to get all students from students_registration with voting status
    $sql = "SELECT s.student_id, s.fullname, s.department, s.program, s.section, s.email, s.gender,
            COALESCE(v.status, 'Haven''t voted') as voting_status
            FROM students_registration s
            LEFT JOIN student_votes v ON s.student_id = v.student_id
            ORDER BY s.student_id DESC";
    
    $result = $conn->query($sql);
    
    if ($result === false) {
        throw new Exception($conn->error);
    }
    
    $students = array();
    while ($row = $result->fetch_assoc()) {
        // Sanitize data for JSON
        array_walk($row, function(&$value) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        });
        $students[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'students' => $students
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close(); 