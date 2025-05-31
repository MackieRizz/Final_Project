<?php
include 'db.php';
session_start();

// Ensure proper timezone is set
date_default_timezone_set('Asia/Manila');

// Check scanner availability
$stmt = $conn->prepare("SELECT start_datetime, end_datetime FROM scanner_settings ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$voting_status = '';
$start_time = '';
$end_time = '';

if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
    $now = new DateTime();
    $start = new DateTime($settings['start_datetime']);
    $end = new DateTime($settings['end_datetime']);

    if ($now < $start) {
        // Voting hasn't started yet
        $voting_status = 'not_started';
        $start_time = $start->format('F j, Y g:i A');
        $end_time = $end->format('F j, Y g:i A');
    } elseif ($now > $end) {
        // Voting has ended
        $voting_status = 'ended';
        $start_time = $start->format('F j, Y g:i A');
        $end_time = $end->format('F j, Y g:i A');
    }
} else {
    // No settings found
    $voting_status = 'not_set';
}

// Get subject_id from URL parameter
$subject_id = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : null;

// Verify if subject exists and get its time window
if ($subject_id) {
    $stmt = $conn->prepare("SELECT subject_name, start_datetime, end_datetime FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    if (!$subject) {
        die("Invalid subject");
    }
}

// Add this at the beginning of the file, after session_start();
if (isset($_GET['clear'])) {
    unset($_SESSION['show_voted_modal']);
    header('Location: scanner.php?subject_id=' . $subject_id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qr_data'] ?? '';

    if (preg_match('/Name:(.*),ID:([A-Za-z0-9\-\/]+)/', $qrData, $matches)) {
        $fullname = trim($matches[1]);
        $studentId = trim($matches[2]);

        // Check if student exists in registration
        $checkStudent = $conn->prepare("SELECT id FROM students_registration WHERE student_id = ?");
        $checkStudent->bind_param("s", $studentId);
        $checkStudent->execute();
        $studentResult = $checkStudent->get_result();

        if ($studentResult->num_rows === 0) {
            $message = "<div class='alert alert-danger fw-bold text-center'>Student not registered!</div>";
        } else {
            // Check if student has already voted
            $checkVote = $conn->prepare("SELECT status FROM student_votes WHERE student_id = ? ORDER BY scan_time DESC LIMIT 1");
            $checkVote->bind_param("s", $studentId);
            $checkVote->execute();
            $voteResult = $checkVote->get_result();

            if ($voteResult->num_rows > 0) {
                $voteData = $voteResult->fetch_assoc();
                if ($voteData['status'] === 'Voted') {
                    if (!isset($_SESSION['show_voted_modal'])) {
                        $_SESSION['show_voted_modal'] = true;
                        $message = "<script>
                            Swal.fire({
                                title: 'Already Voted!',
                                text: 'Student ID: " . $studentId . " has already voted',
                                icon: 'warning',
                                confirmButtonColor: '#C46B02',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'scanner.php?subject_id=" . $subject_id . "&clear=1';
                                }
                            });
                        </script>";
                    }
                } else {
                    // Student has scanned but hasn't voted yet
                    $_SESSION['student_id'] = $studentId;
                    $_SESSION['has_scanned'] = true;
                    header('Location: voting_form.php');
                    exit;
                }
            } else {
                // Create new voting record
                $current_time = date('Y-m-d H:i:s');
                $insertVote = $conn->prepare("INSERT INTO student_votes (student_id, scan_time, status) VALUES (?, ?, 'Didn\'t vote yet')");
                $insertVote->bind_param("ss", $studentId, $current_time);

                if ($insertVote->execute()) {
                    $_SESSION['student_id'] = $studentId;
                    $_SESSION['has_scanned'] = true;
                    header('Location: voting_form.php');
                    exit;
                } else {
                    $message = "<div class='alert alert-danger text-center'>Error creating voting record: " . $insertVote->error . "</div>";
                }
            }
        }
    } else {
        $message = "<script>
            Swal.fire({
                title: 'Invalid QR Code',
                text: 'Invalid QR Code format. Use Name:...,ID:2025-XXXX...',
                icon: 'error',
                confirmButtonColor: '#C46B02',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = window.location.pathname + window.location.search;
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>QR Code Voting</title>
    <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Karla:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap');

        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Karla', sans-serif;
        }

        body {
            background: linear-gradient(to bottom right, #C46B02, #800000, #7F0404, #4D1414, #000000);
            background-size: cover;
            color: #FDDE54;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0;
            margin: 0;
        }

        body.fade-in {
            opacity: 0;
            animation: fadeInAnimation 1s forwards;
        }

        @keyframes fadeInAnimation {
            to {
                opacity: 1;
            }
        }

        header {
            width: 100%;
            padding: 2px 8%;
            position: fixed;
            top: 0;
            left: 0;
            background: transparent;
            z-index: 100;
            transition: all 0.3s ease;
        }

        nav {
            padding: 5px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo a {
            font-size: 40px;
            text-decoration: none;
            color: #FDDE54;
            font-weight: 600;

        }

        .logo a i {
            font-size: 1.2em;
            color: #FDDE54;
            vertical-align: middle;
        }

        .logo span {
            color: #F4BB00;
        }

        /* Size and align the EVSU logo */
        .evsu-logo-nav img {
            width: 45px;
            /* adjust as needed */
            height: auto;
            margin-top: 12px;
            margin-left: -5px;
            vertical-align: middle;
            border-radius: 4px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Hover effect on the logo */
        .evsu-logo-nav img:hover {
            transform: scale(1.1);
        }

        /* Make sure the li spacing still feels balanced */
        nav ul li.evsu-logo-nav {
            margin-left: 20px;
            margin-right: -10px;
        }

        /* 3) Float animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        header nav ul {
            transition: max-height 0.3s ease;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                max-height: 0;
                overflow: hidden;
            }

            nav ul.active {
                max-height: 300px;
                /* enough for menu to show */
            }

            nav ul li {
                margin: 10px 0;
            }

            /* Hamburger menu icon */
            .menu-toggle {
                display: block;
                cursor: pointer;
                font-size: 26px;
                color: #FDDE54;
            }
        }

        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }

        nav {
            padding: 5px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo a {
            font-size: 35px;
            text-decoration: none;
            color: #FDDE54;
            font-weight: 600;
            margin-left: 4px;
            margin-bottom: 5px;
        }

        .logo span {
            color: #F4BB00;
        }

        nav ul {
            list-style: none;
        }

        nav ul li {
            display: inline-block;
            margin: 0 15px;
        }


        h2,
        h4 {
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
        }

        .main-container {
            min-height: unset;
            max-height: calc(100vh - 170px);
            /* Increased space for footer */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 12px 8px 12px 8px;
            /* Reduced padding */
            background: rgba(77, 20, 20, 0.92);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
            margin-top: 0px;
            margin-bottom: 12px;
            /* Reduced margin */
            width: 100%;
            max-width: 770px;
            overflow: visible;
        }

        .scanner-section {
            width: 100%;
            background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
            border-radius: 10px;
            padding: 14px 6px 10px 6px;
            /* Reduced padding */
            box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253, 222, 84, 0.08);
            color: #4D1414;
            font-family: 'Montserrat', 'Karla', sans-serif;
            border: 2px solid #C46B02;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .scanner-section h2 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            color: #7F0404;
            letter-spacing: 1px;
        }

        .current-time-info {
            font-size: 1.02rem;
            color: #7F0404;
            margin-bottom: 10px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            background: #fffbe6;
            border-radius: 8px;
            padding: 7px 0 6px 0;
            border: 1.5px solid #C46B02;
            width: 100%;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }

        #reader {
            width: 100%;
            max-width: 480px;
            /* Slightly smaller */
            min-height: 300px;
            /* Slightly smaller */
            aspect-ratio: 4/2;
            margin: 0 auto 12px auto;
            /* Reduced margin-bottom */
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253, 222, 84, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #C46B02;
        }

        @media (max-width: 700px) {
            .main-container {
                margin-top: 70px;
                margin-bottom: 16px;
                border-radius: 10px;
                max-width: 98vw;
                padding: 12px 2px 12px 2px;
            }

            .scanner-section {
                padding: 12px 4px 10px 4px;
                border-radius: 8px;
            }

            #reader {
                max-width: 98vw;
                min-height: 220px;
            }
        }


        footer {
            border-top: 1px solid #FDDE54;
            padding: 18px 8% 10px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #FDDE54;
            font-family: 'Karla', sans-serif;
            background: transparent;
            width: 84%;
            position: fixed;
            bottom: 0;
            left: 8%;
            right: 0;
            z-index: 100;
            box-sizing: border-box;
        }

        footer nav {
            display: flex;
            align-items: center;
            position: fixed;
            right: 110px;
            bottom: 20px;

        }

        footer nav a {
            color: #FDDE54;
            text-decoration: none;
            margin-left: 0;
            margin-right: 0;
            padding: 0 8px;
            transition: color 0.3s ease;
        }

        footer nav a:hover {
            color: #f4bb00;
        }

        footer p {
            margin: 10px;
            font-size: 14px;
            margin-left: -118px;
            color: #FDDE54;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }

            .scanner-section {
                padding: 25px;
            }

            #reader {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .scanner-section {
                padding: 20px;
            }

            footer {
                flex-direction: column;
                text-align: center;
                padding: 14px 4% 8px 4%;
            }

            footer nav {
                margin-top: 6px;
                gap: 10px;
            }
        }

        .back-icon {
            position: fixed;
            top: 20px;
            left: 38px;
            z-index: 200;
            background: rgba(253, 222, 84, 0.85);
            border: 2.5px solid #C46B02;
            border-radius: 45%;
            width: 58px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7F0404;
            font-size: 2.0rem;
            box-shadow: 0 2px 10px rgba(124, 4, 4, 0.13);
            transition:
                background 0.25s,
                border 0.25s,
                box-shadow 0.25s,
                transform 0.25s;
            cursor: pointer;
            text-decoration: none;
        }

        .back-icon:hover {
            background: #C46B02;
            border: 2.5px solid #FDDE54;
            color: #fffbe6;
            box-shadow: 0 8px 32px 0 rgba(124, 4, 4, 0.28), 0 1.5px 6px rgba(253, 222, 84, 0.18);
            transform: scale(1.13) translateY(-2px);
        }

        .entrance-animate {
            opacity: 0;
            transform: translateY(40px) scale(0.98);
            animation: entranceFadeUp 0.8s cubic-bezier(.77, 0, .18, 1) 0.2s forwards;
        }

        @keyframes entranceFadeUp {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Add new styles for unavailable message */
        .unavailable-container {
            min-height: unset;
            max-height: calc(100vh - 120px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            background: rgba(77, 20, 20, 0.92);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
        }

        .unavailable-message {
            background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            color: #7F0404;
            border: 2px solid #C46B02;
            width: 100%;
        }

        .unavailable-message h2 {
            color: #7F0404;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .unavailable-message p {
            font-size: 1.1em;
            margin-bottom: 15px;
            color: #4D1414;
        }

        .time-info {
            background: rgba(196, 107, 2, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #C46B02;
        }

        .back-btn {
            background: #C46B02;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }

        .back-btn:hover {
            background: #7F0404;
            color: #FDDE54;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <!-- Back Icon Button -->
    <a href="Homepage.html" class="back-icon entrance-animate" title="Back to Homepage">
        <i class="fas fa-arrow-left"></i>
    </a>
    <header class="entrance-animate">
        <nav>
            <div class="menu-toggle">&#9776;</div>
            <div class="logo">
                <a href="Homepage.html"><i class="fas fa-sun" style="margin-right: 8px;"></i>SAR<span>JAGA.</span></a>
            </div>
            <ul>
                <li class="evsu-logo-nav">
                    <a href="#"><!-- Padung admindashboard-->
                        <img src="Images/EvsuLogo.png" alt="EVSU Logo">
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <?php if ($voting_status === ''): ?>
        <div class="main-container entrance-animate">
            <div class="scanner-section">
                <h2>QR code Scanner to VOTE</h2>
                <div class="current-time-info">
                    Date: <span id="current-date"></span><br>
                    Time: <span id="current-time"></span>
                </div>
                <div id="reader"></div>
                <div style="width: 100%; display: flex; justify-content: center; margin-top: 18px;">
                    <button id="scan-image-btn" type="button" title="Scan QR from Image" style="background: rgba(253,222,84,0.85); border: 2.5px solid #C46B02; border-radius: 12px; width: 180px; height: 48px; display: flex; align-items: center; justify-content: center; color: #7F0404; font-size: 1.15rem; box-shadow: 0 2px 10px rgba(124,4,4,0.13); cursor: pointer; transition: background 0.25s, border 0.25s, box-shadow 0.25s, transform 0.25s; gap: 10px;">
                        <i class="fas fa-upload"></i>
                        Upload Image
                    </button>
                    <input type="file" id="qr-image-input" accept="image/*" style="display:none;">
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="unavailable-container entrance-animate">
            <div class="unavailable-message">
                <h2>
                    <?php
                    if ($voting_status === 'not_started')
                        echo 'Voting Not Started';
                    elseif ($voting_status === 'ended')
                        echo 'Voting Has Ended';
                    else
                        echo 'Voting Not Available';
                    ?>
                </h2>

                <?php if ($voting_status === 'not_started'): ?>
                    <p>The voting period has not started yet.</p>
                    <div class="time-info">
                        <p><strong>Voting Period:</strong></p>
                        <p>Starts: <?php echo $start_time; ?></p>
                        <p>Ends: <?php echo $end_time; ?></p>
                    </div>
                    <p>Please come back during the scheduled voting period.</p>

                <?php elseif ($voting_status === 'ended'): ?>
                    <p>The voting period has ended.</p>
                    <div class="time-info">
                        <p><strong>Voting Period Was:</strong></p>
                        <p>From: <?php echo $start_time; ?></p>
                        <p>Until: <?php echo $end_time; ?></p>
                    </div>
                    <p>Thank you for your participation.</p>

                <?php else: ?>
                    <p>The voting period has not been set up yet.</p>
                    <p>Please contact the administrator for more information.</p>
                <?php endif; ?>

                <a href="Homepage.html" class="back-btn">Back to Homepage</a>
            </div>
        </div>
    <?php endif; ?>

    <footer class="entrance-animate entrance-animate">
        <p>&copy; 2025 EVSU Voting System. All rights reserved.</p>
        <nav>
            <a href="#">Privacy Policy </a> | <a href="#">Terms of Use </a> | <a href="#">Help</a>
        </nav>
    </footer>

    <script>
        // Update current date and time
        function updateCurrentDateTime() {
            const now = new Date();
            const dateString = now.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
            const timeString = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true });
            document.getElementById('current-date').textContent = dateString;
            document.getElementById('current-time').textContent = timeString;
        }
        setInterval(updateCurrentDateTime, 1000);
        updateCurrentDateTime();

        // Start QR scanner (no backend logic)
        let qrScanner;
        function startScanner() {
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    const cameraId = cameras[0].id;
                    qrScanner = new Html5Qrcode("reader");
                    qrScanner.start(
                        cameraId,
                        { fps: 10, qrbox: 310 },
                        (decodedText) => {
                            // Stop scanning after successful scan
                            qrScanner.stop().then(() => {
                                // Create and submit form with QR data
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = window.location.href;

                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'qr_data';
                                input.value = decodedText;

                                form.appendChild(input);
                                document.body.appendChild(form);
                                form.submit();
                            });
                        },
                        (err) => console.warn("Scan error:", err)
                    );
                } else {
                    alert("No camera found");
                }
            }).catch(err => alert("Camera init error: " + err));
        }
        window.addEventListener('load', startScanner);

        // Scan as Image feature
        const scanImageBtn = document.getElementById('scan-image-btn');
        const qrImageInput = document.getElementById('qr-image-input');
        let html5QrCodeInstance = null;

        scanImageBtn.addEventListener('click', () => {
            qrImageInput.value = '';
            qrImageInput.click();
        });

        qrImageInput.addEventListener('change', async (event) => {
            const file = event.target.files[0];
            if (!file) return;
            if (!html5QrCodeInstance) {
                html5QrCodeInstance = new Html5Qrcode("reader");
            }
            try {
                const result = await html5QrCodeInstance.scanFile(file, true);
                await html5QrCodeInstance.clear();
                html5QrCodeInstance = null;
                // If scan is successful, submit as if camera scanned
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'qr_data';
                input.value = result;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } catch (err) {
                if (html5QrCodeInstance) {
                    await html5QrCodeInstance.clear();
                    html5QrCodeInstance = null;
                }
                Swal.fire({
                    title: 'Invalid or Unreadable QR Code',
                    text: 'The selected image does not contain a valid QR code.',
                    icon: 'error',
                    confirmButtonColor: '#C46B02',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = window.location.pathname + window.location.search;
                });
            }
        });
    </script>
    <?php if (isset($message))
        echo $message; ?>
</body>

</html>