<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_SESSION['otp'])) {
    unset($_SESSION['otp'], $_SESSION['form_data']);
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// PHPMailer setup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; // Adjust path if needed

$qrCodeUrl = '';
$successMessage = '';
$errorMessage = '';
$fullname = "";
$student_id = "";
$update_mode = false;
$qrCodePath = "";
$gender = "";
$section = "";
$email = "";
$otp_verified = false;

// Path to save QR code images
$qrCodeSavePath = "../qr_codes/";

// Create QR code directory if it doesn't exist
if (!file_exists($qrCodeSavePath)) {
    mkdir($qrCodeSavePath, 0777, true);
}

// Check if an ID is passed for updating an existing record
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $update_mode = true;

    // Fetch existing data
    $sql = "SELECT fullname, student_id, qr_code_path, email FROM students_registration WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $fullname = $row['fullname'];
        $student_id = $row['student_id'];
        $qrCodePath = $row['qr_code_path'];
        $email = $row['email'];
    }
}

// OTP verification logic
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'] ?? '';
    if (isset($_SESSION['otp']) && $entered_otp == $_SESSION['otp']) {
        $otp_verified = true;
        // Restore form data from session
        $fullname = $_SESSION['form_data']['fullname'] ?? '';
        $student_id = $_SESSION['form_data']['student_id'] ?? '';
        $department = $_SESSION['form_data']['department'] ?? '';
        $program = $_SESSION['form_data']['program'] ?? '';
        $gender = $_SESSION['form_data']['gender'] ?? '';
        $section = $_SESSION['form_data']['section'] ?? '';
        $email = $_SESSION['form_data']['email'] ?? '';
        unset($_SESSION['otp'], $_SESSION['form_data']);
    } else {
        $errorMessage = "Invalid OTP. Please try again.";
    }
}

// Registration logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['verify_otp'])) {
    // Get all form data
    $fullname = isset($_POST['fullname']) ? $conn->real_escape_string($_POST['fullname']) : '';
    $student_id = isset($_POST['student_id']) ? $conn->real_escape_string($_POST['student_id']) : '';
    $department = isset($_POST['department']) ? $conn->real_escape_string($_POST['department']) : '';
    $program = isset($_POST['program']) ? $conn->real_escape_string($_POST['program']) : '';
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
    $section = isset($_POST['section']) ? $conn->real_escape_string($_POST['section']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

    if (!preg_match('/^[a-zA-Z0-9._%+-]+@evsu\.edu\.ph$/', $email)) {
        $errorMessage = "Only @evsu.edu.ph email addresses are allowed.";
    } else {
        // Check if email or student_id already exists
        $checkEmailSql = "SELECT id FROM students_registration WHERE email = '$email'";
        $checkEmailResult = $conn->query($checkEmailSql);
        $checkIdSql = "SELECT id FROM students_registration WHERE student_id = '$student_id'";
        $checkIdResult = $conn->query($checkIdSql);
        
        if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
            $errorMessage = "Email already exists. Please use a different one.";
            } elseif ($checkIdResult && $checkIdResult->num_rows > 0) {
                $errorMessage = "Student ID already exists. Please use a different one.";
            }else {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['form_data'] = [
                'fullname' => $fullname,
                'student_id' => $student_id,
                'department' => $department,
                'program' => $program,
                'gender' => $gender,
                'section' => $section,
                'email' => $email
            ];

            // Send OTP via PHPMailer
            $mail = new PHPMailer(true);
            try {
                // SMTP config (edit as needed)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'memorawebnote@gmail.com';
                $mail->Password = 'dypl dsxz kweq ejew';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('memorawebnote@gmail.com', 'EVSU Voting System');
                $mail->addAddress($email, $fullname);

                $mail->isHTML(true);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Dear $fullname,<br><br>Your OTP code is: <b>$otp</b><br><br>Thank you!";

                $mail->send();
                $successMessage = "OTP sent to your email. Please check your inbox.";
            } catch (Exception $e) {
                $errorMessage = "Failed to send OTP: " . $mail->ErrorInfo;
            }
        }   
    }
}

if ($otp_verified) {
    // Generate QR code URL
    $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("Name:" . $fullname . ",ID:" . $student_id);
    $qrCodeFileName = "QRCode_" . $student_id . ".png";
    $fullQrCodePath = $qrCodeSavePath . $qrCodeFileName;

    // Create QR code directory if it doesn't exist
    if (!file_exists($qrCodeSavePath)) {
        mkdir($qrCodeSavePath, 0777, true);
        chmod($qrCodeSavePath, 0777);
    }

    // Generate QR code
    try {
        $ch = curl_init($qrCodeUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        if (file_put_contents($fullQrCodePath, $response) === false) {
            throw new Exception("Failed to save QR code image");
        }
        chmod($fullQrCodePath, 0666);

    } catch (Exception $e) {
        $errorMessage = "Error generating QR code: " . $e->getMessage();
        $qrCodeUrl = "";
        $qrCodeFileName = "";
        $fullQrCodePath = "";
    }

    // Check if student_id already exists
    $checkSql = "SELECT id FROM students_registration WHERE student_id = '$student_id'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
        $successMessage = "Registration Failed! Student ID $student_id is already registered.";
        $student_id = "";
        $qrCodeUrl = "";
        $qrCodeFileName = "";
        $fullQrCodePath = "";
    } else {
        $sql = "INSERT INTO students_registration (fullname, student_id, qr_code_path, department, program, gender, section, email)
                VALUES ('$fullname', '$student_id', '$fullQrCodePath', '$department', '$program', '$gender', '$section', '$email')";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Registration Successful!";
            $new_id = $conn->insert_id;
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}
?>


<?php
// Place OTP check here, before any HTML output
if (isset($_SESSION['otp']) && !$otp_verified): ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body.otp-bg {
            background: linear-gradient(135deg, #C46B02 0%, #800000 40%, #7F0404 70%, #4D1414 90%, #000000 100%) !important;
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
        }
        
        body.otp-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(196, 107, 2, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(127, 4, 4, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(253, 222, 84, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .otp-main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .otp-form-section {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 251, 230, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px 40px 40px 40px;
            box-shadow: 
                0 32px 64px rgba(124, 4, 4, 0.15),
                0 16px 32px rgba(196, 107, 2, 0.1),
                0 8px 16px rgba(253, 222, 84, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
            color: #4D1414;
            border: 1px solid rgba(196, 107, 2, 0.3);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .otp-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #C46B02 0%, #7F0404 50%, #C46B02 100%);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes shimmer {
            0%, 100% { background-position: -200% 0; }
            50% { background-position: 200% 0; }
        }
        
        .otp-form-section h2 {
            font-size: 2.25rem;
            margin-bottom: 8px;
            font-weight: 700;
            text-align: center;
            color: #7F0404;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        
        .otp-form-section h2 i {
            color: #C46B02;
            margin-right: 12px;
            font-size: 2rem;
            vertical-align: middle;
            filter: drop-shadow(0 2px 4px rgba(196, 107, 2, 0.3));
        }
        
        .subtitle {
            text-align: center;
            color: #C46B02;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 32px;
            opacity: 0.9;
        }
        
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .otp-form-section label {
            font-weight: 600;
            color: #7F0404;
            font-size: 0.9rem;
            letter-spacing: 0.01em;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .otp-form-section .form-control {
            width: 100%;
            padding: 16px 20px;
            border-radius: 12px;
            border: 2px solid rgba(196, 107, 2, 0.3);
            font-size: 1.1rem;
            font-weight: 600;
            color: #7F0404;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.1em;
            text-align: center;
            font-family: 'Inter', monospace;
            outline: none;
        }
        
        .otp-form-section .form-control:focus {
            border: 2px solid #7F0404;
            box-shadow: 
                0 0 0 4px rgba(127, 4, 4, 0.1),
                0 8px 24px rgba(196, 107, 2, 0.15);
            background: rgba(255, 251, 230, 0.95);
            color: #C46B02;
            transform: translateY(-2px);
        }
        
        .otp-form-section .form-control::placeholder {
            color: rgba(196, 107, 2, 0.5);
            font-weight: 500;
        }
        
        .btn-container {
            margin-top: 32px;
        }
        
        .otp-form-section .btn-primary {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, #C46B02 0%, #7F0404 100%);
            color: #fffbe6;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 12px;
            letter-spacing: 0.02em;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            font-size: 0.95rem;
            box-shadow: 
                0 8px 16px rgba(196, 107, 2, 0.25),
                0 4px 8px rgba(127, 4, 4, 0.15);
        }
        
        .otp-form-section .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .otp-form-section .btn-primary:hover {
            background: linear-gradient(135deg, #7F0404 0%, #C46B02 100%);
            box-shadow: 
                0 12px 24px rgba(127, 4, 4, 0.3),
                0 6px 12px rgba(196, 107, 2, 0.2);
            transform: translateY(-2px);
        }
        
        .otp-form-section .btn-primary:hover::before {
            left: 100%;
        }
        
        .otp-form-section .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                0 4px 8px rgba(127, 4, 4, 0.2),
                0 2px 4px rgba(196, 107, 2, 0.15);
        }
        
        .back-link-container {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(196, 107, 2, 0.2);
        }
        
        .otp-form-section .btn-link {
            color: #C46B02;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
        }
        
        .otp-form-section .btn-link:hover {
            color: #7F0404;
            background: rgba(196, 107, 2, 0.1);
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        .otp-form-section .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(127, 4, 4, 0.1));
            border: 1px solid rgba(127, 4, 4, 0.3);
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 0.95rem;
            margin-top: 20px;
            font-weight: 500;
            color: #7F0404;
            backdrop-filter: blur(10px);
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @media (max-width: 600px) {
            .otp-form-section {
                margin: 10px;
                padding: 32px 24px 24px 24px;
                max-width: calc(100vw - 20px);
                border-radius: 20px;
            }
            
            .otp-form-section h2 {
                font-size: 1.9rem;
            }
            
            .otp-form-section h2 i {
                font-size: 1.7rem;
                margin-right: 8px;
            }
            
            .otp-form-section .form-control {
                padding: 14px 16px;
                font-size: 1rem;
            }
            
            .otp-form-section .btn-primary {
                padding: 14px 20px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 400px) {
            .otp-main-container {
                padding: 10px;
            }
            
            .otp-form-section {
                padding: 24px 20px 20px 20px;
            }
        }
    </style>
    <body class="otp-bg">
        <div class="otp-main-container">
            <div class="otp-form-section">
                <h2>
                    <i class="fas fa-key"></i>
                    OTP Verification
                </h2>
                <p class="subtitle">Secure access verification required</p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="otp">Enter OTP sent to your email</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="otp" 
                            name="otp" 
                            maxlength="6" 
                            required 
                            autocomplete="one-time-code" 
                            inputmode="numeric" 
                            pattern="[0-9]{6}"
                            placeholder="000000"
                        >
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" name="verify_otp" class="btn btn-primary">
                            Verify OTP
                        </button>
                    </div>
                </form>
                
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        <?= $errorMessage ?>
                    </div>
                <?php endif; ?>
                
                <div class="back-link-container">
                    <a href="register.php" class="btn btn-link">
                        <i class="fas fa-arrow-left" style="margin-right: 6px; font-size: 0.8rem;"></i>
                        Back to Registration
                    </a>
                </div>
            </div>
        </div>
    </body>
    <?php exit; ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
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
        min-height: 100vh;
        display: flex;
        flex-direction: column; 
        justify-content: flex-start;
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
    max-width: 1200px;
    margin: 0 auto;
    min-height: 100vh;
    padding: 120px 20px 100px 20px;
    margin-top: -30px;
}

.main-container {
    min-height: auto;
    max-height: none;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 24px;
    background: rgba(77, 20, 20, 0.92);
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.18);
    margin: 0;
    width: 100%;
    max-width: 850px;
    overflow: visible;
}

.form-section {
    width: 100%;
    background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
    border-radius: 14px;
    padding: 32px 32px 24px 32px; 
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
    color: #4D1414;
    font-family: 'Montserrat', 'Karla', sans-serif;
    border: 2px solid #C46B02;
}

.form-section h2 {
    font-size: 2.1rem;
    margin-bottom: 18px;
}    
.form-section form .row {
    margin-left: 0;
    margin-right: 0;
}
@media (max-width: 700px) {
.form-section form .row .col-md-6, .form-section form .row .col-12 {
    flex: 0 0 100%;
    max-width: 100%;
}
}

.form-label {
    font-size: 1rem;
    margin-bottom: 4px;
    font-weight: 600;
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
        padding: 10px 16px;
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
        font-size: 1.2rem;
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
    margin: 0;
    display: <?= $qrCodeUrl ? 'block' : 'none' ?>;
    padding: 24px 8px;
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
            padding: 100px 20px 100px 20px;
        }
        
        .main-container {
            max-width: 98vw;
            width: 95%;
        }
        
        .qr-section {
            width: 95%;
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

    
    #gender-container {
        margin-left: 25%;
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
            position: static;
        }
        #gender-container {
                margin-left: 1%;
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
            <a href="#">
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
        <div class="row gx-3 gy-2">
            <div class="col-md-6">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>
            <div class="col-md-6">
            <label for="student_id" class="form-label">Student ID</label>
            <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" required>
            </div>
             <div class="col-md-6">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?= htmlspecialchars($email ?? '') ?>" required
                        pattern="^[a-zA-Z0-9._%+-]+@evsu\.edu\.ph$"
                        title="Please use your @evsu.edu.ph email address.">
                </div>
            <div class="col-md-6">
            <label for="department" class="form-label">Department</label>
            <select class="form-select" id="department" name="department" required onchange="updateProgramOptions()">
                <option value="">Select Department</option>
                <option value="Teacher Education Department">Teacher Education Department</option>
                <option value="Engineering Department">Engineering Department</option>
                <option value="Computer Studies Department">Computer Studies Department</option>
                <option value="Industrial Technology Department">Industrial Technology Department</option>
                <option value="Business and Management Department">Business and Management Department</option>
            </select>
            </div>
            <div class="col-md-6">
            <label for="program" class="form-label">Program</label>
            <select class="form-select" id="program" name="program" required>
                <option value="">Select Program</option>
            </select>
            </div>
            <div class="col-md-6">
            <label for="section" class="form-label">Year & Section</label>
            <input type="text" name="section" value="<?= htmlspecialchars($section ?? '') ?>" required class="form-control">
            </div>
            <div class="col-md-6"  id="gender-container">
            <label for="gender" class="form-label">Gender</label>
            <select name="gender" required class="form-select">
                <option value="">-- Select Gender --</option>
                <option value="Male" <?= (isset($gender) && $gender === "Male") ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= (isset($gender) && $gender === "Female") ? 'selected' : '' ?>>Female</option>
            </select>
            </div>
            <div class="col-12 d-flex justify-content-center mt-2">
            <?php if ($update_mode): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="btn btn-warning w-50" onclick="return confirm('Are you sure you want to update this record?')">Update</button>
            <?php else: ?>
                <button type="submit" class="btn btn-primary w-50">Register</button>
            <?php endif; ?>
            </div>
        </div>
        </form>
        </div>
        </div>

        <div class="qr-section entrance-animate">
            <div class="qr-box">
                <h4 style="font-weight: bold;">Generated QR Code</h4>
                <?php if ($qrCodeUrl): ?>
                    <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="width: 200px; height: 200px;">
                <?php else: ?>
                    <p style="color: #FDDE54;">No QR code generated yet</p>
                <?php endif; ?>
                <div class="qr-info">
                    <h5><?= htmlspecialchars($fullname) ?></h5>
                    <p>ID: <?= htmlspecialchars($student_id) ?></p>
                    <?php if ($qrCodeUrl): ?>
                        <a href="download_qr.php?filename=<?= urlencode($qrCodeFileName) ?>" class="btn btn-primary btn-block" onclick="showDownloadAlert()">Download QR Code</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($successMessage && !$update_mode): ?>
            <div class="mt-4">
                <a href="scanner.php" class="btn btn-primary w-100">Vote Now</a>
            </div>
            <?php endif; ?>
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
    const programs = {
        'Teacher Education Department': [
            'Bachelor of Elementary Education (BEED)',
            'Bachelor of Secondary Education (BSEd) major in Mathematics',
            'Bachelor of Secondary Education (BSEd) major in Science',
            'Bachelor of Physical Education (BPEd)',
            'Bachelor of Technical-Vocational Teacher Education (BTVTEd)'
        ],
        'Business and Management Department': [
            'Bachelor of Science in Hospitality Management (BSHM)'
        ],
        'Engineering Department': [
            'Bachelor of Science in Civil Engineering (BSCE)',
            'Bachelor of Science in Electrical Engineering (BSEE)',
            'Bachelor of Science in Mechanical Engineering (BSME)'
        ],
        'Computer Studies Department': [
            'Bachelor of Science in Information Technology (BSIT)'
        ],
        'Industrial Technology Department': [
            'Bachelor of Industrial Technology (BIT) with major in Culinary Arts (CA)',
            'Bachelor of Industrial Technology (BIT) with major in Electronics (ET)'
        ]
    };

    function updateProgramOptions() {
        const department = document.getElementById('department').value;
        const programSelect = document.getElementById('program');
        programSelect.innerHTML = '<option value="">Select Program</option>';
        
        if (department && programs[department]) {
            programs[department].forEach(program => {
                const option = document.createElement('option');
                option.value = program;
                option.textContent = program;
                programSelect.appendChild(option);
            });
        }
    }

    function showDownloadAlert() {
        Swal.fire({
            icon: 'success',
            title: 'Download Successful!',
            text: 'QR code downloaded successfully.',
            showConfirmButton: false,
            timer: 1500
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const studentIdInput = document.querySelector('input[name="student_id"]');
        if (studentIdInput) {
            studentIdInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, ''); // Remove non-digits
                if (value.length > 9) value = value.slice(0, 9); // Max 9 digits
                if (value.length > 4) {
                    value = value.slice(0, 4) + '-' + value.slice(4);
                }
                this.value = value;
            });
        }
    });
</script>

</body>
</html>
