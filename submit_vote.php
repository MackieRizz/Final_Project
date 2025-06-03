<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'db.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        // Fetch student email and name
        $stmt = $conn->prepare("SELECT fullname, email FROM students_registration WHERE student_id = ? LIMIT 1");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($fullname, $email);
        $stmt->fetch();
        $stmt->close();

        // Prepare vote summary
        require_once 'voting_form_data.php';
        $summary = "<h2>EVSU Student Council Elections - Vote Receipt</h2>";
        $summary .= "<p>Dear <b>" . htmlspecialchars($fullname) . "</b>,<br>Thank you for voting! Here is your vote receipt:</p>";
        $summary .= "<p><b>Date & Time of Voting:</b> " . date('F j, Y \a\t g:i A', strtotime($current_time)) . "</p>";
        $summary .= "<ul>";
        foreach ($positions as $position) {
            $pos_id = $position['position_id'];
            $pos_name = $position['position'];
            $selected_cand_id = isset($votes[$pos_id]) ? $votes[$pos_id] : null;
            $cand_name = $cand_program = $cand_year = '';
            if ($selected_cand_id) {
                foreach ($position['candidates'] as $candidate) {
                    if ($candidate['candidate_id'] == $selected_cand_id) {
                        $cand_name = $candidate['name'];
                        $cand_program = $candidate['program'];
                        $cand_year = $candidate['year'];
                        break;
                    }
                }
            }
            $summary .= "<li><b>" . htmlspecialchars($pos_name) . ":</b> ";
            if ($cand_name) {
                $summary .= htmlspecialchars($cand_name) . " (" . htmlspecialchars($cand_program) . ", " . htmlspecialchars($cand_year) . ")";
            } else {
                $summary .= "No vote recorded.";
            }
            $summary .= "</li>";
        }
        $summary .= "</ul><p style='color:#888;font-size:0.95em;'>This is an official voting receipt. If you did not vote, please contact the election committee immediately.</p>";

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'memorawebnote@gmail.com'; // Use your sender email
            $mail->Password = 'dypl dsxz kweq ejew'; // Use your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('memorawebnote@gmail.com', 'EVSU Voting System');
            $mail->addAddress($email, $fullname);
            $mail->isHTML(true);
            $mail->Subject = 'Your EVSU Vote Receipt';
            $mail->Body = $summary;
            $mail->send();
        } catch (Exception $e) {
            // Optionally log or ignore email errors
        }

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