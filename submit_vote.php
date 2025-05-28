<?php
session_start();
include 'db.php';

// Check if user is logged in and has scanned QR
if (!isset($_SESSION['student_id']) || !isset($_SESSION['has_scanned'])) {
    header('Location: scanner.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $student_id = $_SESSION['student_id'];
    $votes = $_POST['vote'];
    $current_time = date('Y-m-d H:i:s');
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Insert votes into votes table (you'll need to create this table)
        foreach ($votes as $position_id => $candidate_id) {
            $insert_vote = $conn->prepare("INSERT INTO votes (student_id, position_id, candidate_id, vote_time) VALUES (?, ?, ?, ?)");
            $insert_vote->bind_param("siis", $student_id, $position_id, $candidate_id, $current_time);
            $insert_vote->execute();
        }
        
        // Update student_votes status
        $update_status = $conn->prepare("UPDATE student_votes SET status = 'Voted', vote_time = ? WHERE student_id = ? AND status = 'Didn\'t vote yet'");
        $update_status->bind_param("ss", $current_time, $student_id);
        $update_status->execute();
        
        // Commit transaction
        $conn->commit();
        
        // Clear session and redirect to success page
        session_destroy();
        header('Location: vote_success.php');
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error submitting vote: " . $e->getMessage());
    }
} else {
    // If form wasn't submitted properly, redirect back to voting form
    header('Location: voting_form.php');
    exit;
}
?> 