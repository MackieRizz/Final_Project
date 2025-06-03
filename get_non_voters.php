<?php
include 'db.php';

header('Content-Type: application/json');

try {
    // Query to get students who haven't voted (both statuses)
    $sql = "SELECT s.student_id, s.fullname, s.department, s.program, s.section, s.email, s.gender,
            CASE 
                WHEN v.status = 'Didn''t vote yet' THEN 'Didn''t vote yet'
                ELSE 'Haven''t voted'
            END as voting_status, v.scan_time, v.vote_time
            FROM students_registration s
            LEFT JOIN student_votes v ON s.student_id = v.student_id
            WHERE v.status = 'Didn''t vote yet' OR v.status IS NULL
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