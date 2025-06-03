<?php
include 'db.php';

header('Content-Type: application/json');

try {
    // Query to get students who haven't voted, starting from student_votes table
    $sql = "SELECT s.student_id, s.fullname, s.department, s.program, s.section, s.email, s.gender,
            v.status as voting_status, v.scan_time, v.vote_time
            FROM student_votes v
            INNER JOIN students_registration s ON v.student_id = s.student_id
            WHERE v.status = 'Didn''t vote yet'
            ORDER BY v.scan_time DESC";
    
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