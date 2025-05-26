<?php
include '../db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qr_data'] ?? '';

    if (preg_match('/ID:([0-9\-]+)/', $qrData, $matches)) {
        $studentId = trim($matches[1]);

        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        if (!$student) {
            $message = "<div class='alert alert-danger fw-bold text-center'>Student not found!</div>";
        } else {
            $checkVote = $conn->prepare("SELECT * FROM votes WHERE student_id = ?");
            $checkVote->bind_param("s", $studentId);
            $checkVote->execute();
            $voteResult = $checkVote->get_result();

            if ($voteResult->num_rows > 0) {
                $message = "<div class='alert alert-warning fw-bold text-center'>You have already voted!</div>";
            } else {
                header("Location: vote_form.php?student_id=" . urlencode($studentId));
                exit();
            }
        }
    } else {
        $message = "<div class='alert alert-danger fw-bold text-center'>Invalid QR code format. Expected: ID:2025-XXXX</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #C46B02, #800000, #7F0404, #4D1414, #000000);
            color: #FDDE54;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Karla', sans-serif;
            margin: 0;
        }
        .container {
            background: rgba(77, 20, 20, 0.92);
            padding: 30px 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 480px;
            text-align: center;
        }
        input#scannerInput {
            font-size: 1.2rem;
            padding: 15px 10px;
            width: 100%;
            border-radius: 8px;
            border: 2px solid #C46B02;
            text-align: center;
            color: #4D1414;
            font-weight: 700;
            outline: none;
        }
        input#scannerInput:focus {
            border-color: #FDDE54;
            box-shadow: 0 0 8px #FDDE54;
        }
        h2 {
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: #F4BB00;
            letter-spacing: 1px;
        }
        .alert {
            margin-top: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Scan Your QR to Vote</h2>
        <form method="POST" id="scanForm" autocomplete="off">
            <input 
                type="text" 
                name="qr_data" 
                id="scannerInput" 
                placeholder="Place cursor here and scan your QR code..." 
                autofocus 
                autocomplete="off" 
                required 
            />
        </form>

        <?php 
        if (!empty($message)) {
            echo $message;
        }
        ?>
    </div>

    <script>
        const scannerInput = document.getElementById('scannerInput');
        const form = document.getElementById('scanForm');

        scannerInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                form.submit();
            }
        });

        form.addEventListener('submit', () => {
            setTimeout(() => {
                scannerInput.value = '';
                scannerInput.focus();
            }, 500);
        });
    </script>
</body>
</html>
