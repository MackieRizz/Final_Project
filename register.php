
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$qrCodeUrl = '';
$successMessage = '';
$errorMessage = '';
$fullname = "";
$student_id = "";
$update_mode = false;
$qrCodePath = "";
$gender = "";
$section = "";

// Path to save QR code images
$qrCodeSavePath = "../qr_codes/";

// Check if an ID is passed for updating an existing record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $update_mode = true;

    // Fetch existing data
    $sql = "SELECT fullname, student_id, qr_code_path FROM students_registration WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $fullname = $row['fullname'];
    $student_id = $row['student_id'];
    $qrCodePath = $row['qr_code_path'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $studentId = $conn->real_escape_string($_POST['student_id']);
    
    // ✅ NEW FIELDS: sanitize input
    $gender = $conn->real_escape_string($_POST['gender']);
    $section = $conn->real_escape_string($_POST['section']);

    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Name:$fullname,ID:$studentId";
    $qrCodeFileName = "QRCode_" . $studentId . ".png";
    $fullQrCodePath = $qrCodeSavePath . $qrCodeFileName;

    $ch = curl_init($qrCodeUrl);
    $fp = fopen($fullQrCodePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    if ($update_mode) {
        // You can add update logic for gender/section here later if needed.
        $sql = "UPDATE students_registration SET fullname='$fullname', student_id='$studentId', qr_code_path='$fullQrCodePath' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Successful!',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href='view_page.php';
                    });
                }, 100);
            </script>";
        } else {
            $errorMessage = "Error updating record: " . $conn->error;
        }
    } else {
        // ✅ INSERT NEW FIELDS (section and gender)
        $sql = "INSERT INTO students_registration (fullname, student_id, qr_code_path)
                VALUES ('$fullname', '$studentId', '$fullQrCodePath')";
        
        if ($conn->query($sql) === TRUE) {
            // Insert section & gender into separate query or update students_registration structure
            $new_id = $conn->insert_id;
            $conn->query("UPDATE students_registration SET gender='$gender', section='$section' WHERE id=$new_id");

            $successMessage = "Registration Successful!";
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    }
.flex-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 32px;
    width: 100%;
    max-width: 900px;
    margin: 110px auto 0 auto;
    min-height: calc(100vh - 200px);
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
    margin-top: -50px;
    margin-bottom: 24px;
    width: 100%;
    max-width: 420px;
    overflow: visible;
}

.form-section {
    width: 100%;
    background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
    border-radius: 14px;
    padding: 24px 16px 18px 16px;
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
    color: #4D1414;
    font-family: 'Montserrat', 'Karla', sans-serif;
    border: 2px solid #C46B02;
}

.form-section h2 {
    font-size: 1.5rem;
    margin-bottom: 18px;
}

.form-label {
    font-size: 1rem;
    margin-bottom: 4px;
}

    .form-control, .form-select {
        background: #fff;
        color: #7F0404;
        border-radius: 10px;
        border: 2px solid #C46B02;
        font-size: 1rem;
        font-family: 'Karla', sans-serif;
        font-weight: 600;
        margin-bottom: 18px;
        padding: 10px 0px;
        transition: border 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(253,222,84,0.08);
    }

    .form-control:focus, .form-select:focus {
        border: 2.5px solid #7F0404;
        box-shadow: 0 0 0 0.18rem rgba(253,222,84,0.18);
        background: #fffbe6;
        color: #C46B02;
    }

    .btn-primary, .btn-warning {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: 1rem;
        border-radius: 8px;
        padding: 10px 0;
        margin-top: 8px;
        letter-spacing: 1px;
        box-shadow: 0 4px 18px rgba(253,222,84,0.18);
        border: none;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    }

    .btn-primary {
        background: linear-gradient(90deg, #C46B02 60%, #7F0404 100%);
        color: #fffbe6;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #7F0404 60%, #C46B02 100%);
        color: #FDDE54;
        box-shadow: 0 8px 24px rgba(124,4,4,0.18);
    }

    .btn-warning {
        background: linear-gradient(90deg, #FDDE54 60%, #C46B02 100%);
        color: #7F0404;
    }

    .btn-warning:hover {
        background: linear-gradient(90deg, #C46B02 60%, #FDDE54 100%);
        color: #4D1414;
    }

    .alert-success, .alert-danger {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        border-radius: 8px;
        font-size: 1.05rem;
    }

    @media (max-width: 700px) {
        .main-container {
            margin-top: 70px;
            margin-bottom: 16px;
            border-radius: 10px;
            max-width: 98vw;
            padding: 12px 2px 12px 2px;
        }
        .form-section {
            padding: 12px 4px 10px 4px;
            border-radius: 8px;
        }
    }
.qr-section {
    max-width: 420px;
    width: 100%;
    margin: 0 auto 24px auto;
    display: <?= $qrCodeUrl ? 'block' : 'none' ?>;
    padding: 24px 8px 24px 8px;
    background: rgba(77, 20, 20, 0.92);
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.18);

}

.qr-box {
    background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
    border-radius: 14px;
    padding: 24px 16px 18px 16px;
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
    color: #4D1414;
    font-family: 'Montserrat', 'Karla', sans-serif;
    border: 2px solid #C46B02;
    min-height: unset;
    text-align: center;
    margin-top: 0;
}

.qr-box h4 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: #7F0404;
    margin-bottom: 18px;
}

.qr-box img {
    margin-top: 10px;
    border-radius: 12px;
    width: 180px;
    height: 180px;
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.18);
    background: #fff;
    border: 2px solid #C46B02;
}

.qr-info h5, .qr-info p {
    margin: 8px 0 0 0;
    color: #7F0404;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
}

.qr-info a.btn {
    margin-top: 14px;
    width: 100%;
    display: block;
}
    /* Header & Footer from Homepage.html */
    header {
        width: 100%;
        margin-top: -5px;
        position: fixed;
        top: 0;
        left: 0;
        background: transparent;
        z-index: 100;
        transition: all 0.3s ease;
    }

    .logo a {
        font-size: 36px;
        margin-left: 0px;
        text-decoration: none;
        color: #FDDE54;
        font-weight: 600;
    }
    .logo span {
        color: #F4BB00;
    }
    .evsu-logo-nav img {
        width: 45px;
        height: auto;
        margin-top: 17px;
        margin-left: -4px;
        vertical-align: middle;
        border-radius: 4px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .evsu-logo-nav img:hover {
        transform: scale(1.1);
    }
    nav ul li.evsu-logo-nav {
        margin-left: 20px;
        margin-right: -10px;
    }
    nav ul {
        list-style: none;
    }
    nav ul li {
        display: inline-block;
        margin: 0 15px;
    }
    nav ul li a {
        text-decoration: none;
        font-weight: 900;
        font-size: 20px;
        color: #FDDE54;
        transition: transform 0.3s ease, box-shadow 0.3s ease, color 0.3s ease;
    }
    nav ul li a i {
        margin-right: 6px;
    }
    nav ul li a:hover {
        color: rgb(255, 255, 0);
        transform: scale(1.1);
    }
    .menu-toggle {
        display: none;
    }
    @media (max-width: 768px) {
        nav ul {
            flex-direction: column;
            max-height: 0;
            overflow: hidden;
        }
        nav ul.active {
            max-height: 300px;
        }
        nav ul li {
            margin: 10px 0;
        }
        .menu-toggle {
            display: block;
            cursor: pointer;
            font-size: 26px;
            color: #FDDE54;
        }
    }
    @media (max-width: 900px) {
    .flex-container {
        flex-direction: column;
        align-items: center;
        gap: 24px;
        margin-top: 90px;
    }
    .main-container, .qr-section {
        max-width: 98vw;
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
    @media (max-width: 768px) {
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
.entrance-animate {
    opacity: 0;
    transform: translateY(40px) scale(0.98);
    animation: entranceFadeUp 0.8s cubic-bezier(.77,0,.18,1) 0.2s forwards;
}

@keyframes entranceFadeUp {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
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
<div class="flex-container entrance-animate">
    <div class="main-container">
        <div class="form-section">
            <h2 class="mb-4 text-center">Student Registration</h2>

            <?php if ($successMessage): ?>
                <div class="alert alert-success text-center"><?= $successMessage ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger text-center"><?= $errorMessage ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required class="form-control">
                </div>
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>" required class="form-control">
                </div>

                <!-- ✅ NEW: Section Field -->
                <div class="mb-3">
                    <label for="section" class="form-label">Section</label>
                    <input type="text" name="section" value="<?= htmlspecialchars($section ?? '') ?>" required class="form-control">
                </div>

                <!-- ✅ NEW: Gender Field -->
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select name="gender" required class="form-select">
                        <option value="">-- Select Gender --</option>
                        <option value="Male" <?= (isset($gender) && $gender === "Male") ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= (isset($gender) && $gender === "Female") ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>

                <?php if ($update_mode): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to update this record?')">Update</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                <?php endif; ?>
            </form>
        </div>
        </div>

        <div class="qr-section entrance-animate">
            <div class="qr-box">
                <h4 style="font-weight: bold;">Generated QR Code</h4>
                <img src="<?= $qrCodeUrl ?>" alt="QR Code">
                <div class="qr-info">
                    <h5><?= htmlspecialchars($fullname) ?></h5>
                    <p>ID: <?= htmlspecialchars($studentId) ?></p>
                    <a href="download_qr.php?filename=<?= urlencode($qrCodeFileName) ?>" class="btn btn-primary btn-block" onclick="showDownloadAlert()">Download QR Code</a>
                </div>
            </div>
        </div>
    </div>
</div>
  <footer class="entrance-animate">
    <p>&copy; 2025 EVSU Voting System. All rights reserved.</p>
    <nav>
      <a href="#">Privacy Policy </a> | <a href="#">Terms of Use </a> | <a href="#">Help</a>
    </nav>
  </footer>

    <script>
    function showDownloadAlert() {
        Swal.fire({
            icon: 'success',
            title: 'Download Successful!',
            text: 'QR code downloaded successfully.',
            showConfirmButton: false,
            timer: 1500
        });
    }
    </script>

</body>
</html>



