<?php
include '../db.php';
session_start();

$studentId = $_GET['student_id'] ?? '';
$studentId = htmlspecialchars(trim($studentId));

if (!$studentId) {
    die("Missing student ID.");
}

// Check if already voted
$stmt = $conn->prepare("SELECT * FROM votes WHERE student_id = ?");
$stmt->bind_param("s", $studentId);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "<div style='text-align:center; color:red; font-size:24px; margin-top:50px;'>You have already voted!</div>";
    exit();
}

// Get student info
$stmt = $conn->prepare("SELECT * FROM students_registration WHERE student_id = ?");
$stmt->bind_param("s", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
if (!$student) {
    die("Student not found.");
}

// Fetch candidates grouped by position
$positions = [];
$res = $conn->query("SELECT * FROM candidate_positions ORDER BY position_id ASC");
while ($row = $res->fetch_assoc()) {
    $positions[$row['position_id']]['position'] = $row['position'];
    $positions[$row['position_id']]['candidates'][] = $row;
}

// Handle voting
$voteSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        foreach ($_POST as $positionId => $candidateId) {
            $positionId = intval($positionId);
            $candidateId = $candidateId === 'abstain' ? null : intval($candidateId);
            $stmt = $conn->prepare("INSERT INTO votes (student_id, position_id, candidate_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $studentId, $positionId, $candidateId);
            $stmt->execute();
        }
        $conn->commit();
        $voteSuccess = true;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div style='color:red;'>Error saving vote: {$e->getMessage()}</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cast Vote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #C46B02, #800000);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-box {
            background: #2d2d2d;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 700px;
            overflow-y: auto;
            max-height: 95vh;
        }
        .form-box h2 {
            color: #F4BB00;
        }
        .candidate {
            background: #444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .candidate img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }
        .btn-vote {
            background: #F4BB00;
            font-weight: bold;
            margin-top: 20px;
        }
        .success-msg {
            color: lightgreen;
            text-align: center;
        }
    </style>
</head>
<body>

<?php if ($voteSuccess): ?>
    <div class="form-box text-center">
        <h2>Vote Submitted</h2>
        <p class="success-msg">Thank you, your vote has been recorded.</p>
        <p id="countdown">Returning to scanner in 5 seconds...</p>
        <audio autoplay>
            <source src="success.mp3" type="audio/mpeg">
        </audio>
    </div>
    <script>
        let seconds = 5;
        const countdown = document.getElementById("countdown");
        const timer = setInterval(() => {
            seconds--;
            countdown.textContent = `Returning to scanner in ${seconds} second${seconds !== 1 ? 's' : ''}...`;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "scanner.php";
            }
        }, 1000);
    </script>
<?php else: ?>
    <div class="form-box">
        <h2>Cast Your Vote</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($student['fullname']) ?></p>
        <p><strong>ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>

        <form method="POST">
            <?php foreach ($positions as $posId => $data): ?>
                <div style="margin-top: 20px;">
                    <h5><?= htmlspecialchars($data['position']) ?></h5>
                    <?php foreach ($data['candidates'] as $cand): ?>
                        <div class="candidate">
                            <input type="radio" name="<?= $posId ?>" id="cand<?= $cand['id'] ?>" value="<?= $cand['id'] ?>" required>
                            <label for="cand<?= $cand['id'] ?>">
                                <img src="<?= htmlspecialchars($cand['image']) ?>" alt="Image">
                                <?= htmlspecialchars($cand['name']) ?> (<?= htmlspecialchars($cand['year']) ?> - <?= htmlspecialchars($cand['program']) ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <div class="candidate">
                        <input type="radio" name="<?= $posId ?>" id="abstain<?= $posId ?>" value="abstain" required>
                        <label for="abstain<?= $posId ?>"><strong>Abstain</strong></label>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-vote btn-block">Submit Vote</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
