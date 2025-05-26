<?php
// delete_student.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    include 'db.php';
    $ids = explode(',', $_POST['student_id']);
    $ids = array_map(function($id) use ($conn) { return "'" . $conn->real_escape_string($id) . "'"; }, $ids);
    $idList = implode(',', $ids);
    $sql = "DELETE FROM students_registration WHERE student_id IN ($idList)";
    if ($conn->query($sql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
    $conn->close();
} else {
    echo 'invalid';
}
