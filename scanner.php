<?php
include 'db.php';
session_start();

// Ensure proper timezone is set
date_default_timezone_set('Asia/Manila');

// Get subject_id from URL parameter
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : null;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qrData = $_POST['qr_data'] ?? '';

    if (preg_match('/Name:(.*),ID:([A-Za-z0-9\-\/]+)/', $qrData, $matches)) {
        $fullname = trim($matches[1]);
        $studentId = trim($matches[2]);

        $today = date('Y-m-d');
        $current_datetime = new DateTime();
        $current_time = $current_datetime->format('H:i:s');
        
        // Check if already logged today for this subject
        $stmt = $conn->prepare("SELECT 1 FROM record_attendance WHERE student_id = ? AND DATE(date) = ? AND subject_id = ?");
        $stmt->bind_param("ssi", $studentId, $today, $subject_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<div class='alert alert-warning fw-bold text-center'>Attendance already logged for this subject today!</div>";
        } else {
            // Convert class times to DateTime objects for today
            $start_datetime = new DateTime($today . ' ' . date('H:i:s', strtotime($subject['start_datetime'])));
            $end_datetime = new DateTime($today . ' ' . date('H:i:s', strtotime($subject['end_datetime'])));
            
            // Add grace period (15 minutes) and early window (30 minutes)
            $grace_period = new DateInterval('PT15M');
            $early_window = new DateInterval('PT30M');
            
            $late_cutoff = clone $start_datetime;
            $late_cutoff->add($grace_period);
            
            $early_allowed = clone $start_datetime;
            $early_allowed->sub($early_window);

            // Determine attendance status based on current time
            if ($current_datetime < $early_allowed) {
                $message = "<div class='alert alert-danger fw-bold text-center'>
                    Too early to scan! Scanning will be available from " . $early_allowed->format('h:i A') . "<br>
                    <small>Current Time: " . $current_datetime->format('h:i A') . "</small>
                </div>";
                $can_record = false;
            } else {
                $can_record = true;
                if ($current_datetime <= $late_cutoff) {
                    $attendance_status = 'present';
                } elseif ($current_datetime <= $end_datetime) {
                    $attendance_status = 'late';
                } else {
                    $attendance_status = 'absent';
                }
            }

            if ($can_record) {
                $insert = $conn->prepare("INSERT INTO record_attendance (fullname, student_id, subject_id, attendance_status, date) VALUES (?, ?, ?, ?, ?)");
                $current_timestamp = $current_datetime->format('Y-m-d H:i:s');
                $insert->bind_param("ssiss", $fullname, $studentId, $subject_id, $attendance_status, $current_timestamp);
                
                if ($insert->execute()) {
                    $status_text = ucfirst($attendance_status);
                    $alert_class = ($attendance_status == 'present') ? 'success' : 
                                ($attendance_status == 'late' ? 'warning' : 'danger');
                    $message = "<div class='alert alert-{$alert_class} fw-bold text-center'>
                        Attendance logged as {$status_text}!<br>
                        <small>Time: " . $current_datetime->format('h:i A') . "</small>
                    </div>";
                } else {
                    $message = "<div class='alert alert-danger text-center'>Error: " . $insert->error . "</div>";
                }
            }
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Invalid QR Code format. Use Name:...,ID:2025-XXXX...</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Attendance</title>
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
        width: 45px;           /* adjust as needed */
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
        0%,100% { transform: translateY(0); }
        50%      { transform: translateY(-5px); }
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
        max-height: 300px; /* enough for menu to show */
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


    h2, h4 {
        font-weight: 600;
        font-family: 'Montserrat', sans-serif;
    }
.main-container {
    min-height: unset;
    max-height: calc(100vh - 120px); /* 120px = header + footer + margin */
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 24px 24px 24px 24px;
    background: rgba(77, 20, 20, 0.92);
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.18);
    margin-top: 0px;
    margin-bottom: 24px;
    width: 100%;
    max-width: 770px;
    overflow: visible;
}

.scanner-section {
    width: 100%;
    background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
    border-radius: 14px;
    padding: 24px 16px 18px 16px;
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
    color: #4D1414;
    font-family: 'Montserrat', 'Karla', sans-serif;
    border: 2px solid #C46B02;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.scanner-section h2 {
    font-size: 1.5rem;
    margin-bottom: 18px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    color: #7F0404;
    letter-spacing: 1px;
}

.current-time-info {
    font-size: 1.08rem;
    color: #7F0404;
    margin-bottom: 18px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    background: #fffbe6;
    border-radius: 8px;
    padding: 10px 0 8px 0;
    border: 1.5px solid #C46B02;
    width: 100%;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
}

#reader {
    width: 100%;
    max-width: 540px;
    min-height: 370px;
    aspect-ratio: 4/2;
    margin: 0 auto 20px auto;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
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
    box-shadow: 0 2px 10px rgba(124,4,4,0.13);
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
    box-shadow: 0 8px 32px 0 rgba(124,4,4,0.28), 0 1.5px 6px rgba(253,222,84,0.18);
    transform: scale(1.13) translateY(-2px);
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
    <div class="main-container">
        <div class="scanner-section">
            <h2>QR code Scanner to VOTE </h2>
             <div class="current-time-info">
                Date: <span id="current-date"></span><br>
                Time: <span id="current-time"></span>
            </div>
            <div id="reader"></div>

        </div>
    </div>

  <footer class="entrance-animate">
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
                            // No backend logic, just log to console
                            console.log("Decoded QR:", decodedText);
                        },
                        (err) => console.warn("Scan error:", err)
                    );
                } else {
                    alert("No camera found");
                }
            }).catch(err => alert("Camera init error: " + err));
        }
        window.addEventListener('load', startScanner);
    </script>
</body>
</html>
