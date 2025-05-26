<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';

$passcode    = '12345678'; // 8 digits now
$secretKey   = '6LcWBTsrAAAAAHsAxa7FJ9S0Am3V95OnX54R9Cmg';
$maxAttempts = 3;

if (!isset($_SESSION['admin_attempts'])) {
    $_SESSION['admin_attempts'] = 0;
}

$error = '';
$lockout = false;
$showAlert = false;


if ($_SESSION['admin_attempts'] >= $maxAttempts) {
    $lockout = true;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $digits = '';
    for ($i = 1; $i <= 8; $i++) { // Loop for 8 digits
        $digits .= $_POST["digit{$i}"] ?? '';
    }

    if (strlen($digits) < 8) { // Require 8 digits
        $error = "Please enter all eight digits. Attempts left: " . ($maxAttempts - $_SESSION['admin_attempts']);
        $showAlert = true;
    } elseif (empty($_POST['g-recaptcha-response'])) {
        $error = "Please complete the reCAPTCHA challenge. Attempts left: " . ($maxAttempts - $_SESSION['admin_attempts']);
        $showAlert = true;
    } else {
        $resp = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret='
            . $secretKey
            . '&response=' . $_POST['g-recaptcha-response']
            . '&remoteip=' . $_SERVER['REMOTE_ADDR']
        );
        $data = json_decode($resp, true);

        if (empty($data['success'])) {
            $error = "reCAPTCHA verification failed. Attempts left: " . ($maxAttempts - $_SESSION['admin_attempts']);
            $showAlert = true;
        }
    }

    if (empty($error)) {
        if ($digits === $passcode) {
            unset($_SESSION['admin_attempts']);
            $_SESSION['admin_verified'] = true;
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $_SESSION['admin_attempts']++;
            if ($_SESSION['admin_attempts'] >= $maxAttempts) {
                $lockout = true;
            } else {
                $error = "Invalid code. Attempts left: " . ($maxAttempts - $_SESSION['admin_attempts']);
                $showAlert = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Verification Code</title>
  <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Karla:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap');
    html {
        scroll-behavior: smooth;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Montserrat', sans-serif;
    }

    body {
          background: linear-gradient(135deg, #1a0606 0%, #2d0808 15%, #4a1010 30%, #6b1a1a 45%, #8b2020 60%, #b8432f 75%, #d65f2f 90%, #f4a261 100%);
        background-size: cover;
        color: #FDDE54;
        min-height: 100vh;
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
    max-height: calc(100vh - 120px);
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 24px;
    background: rgba(77, 20, 20, 0.92);
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.18);
    margin-bottom: 24px;
    margin-top: -20px;
    width: 100%;
    max-width: 550px;
    overflow: visible;
}

.form-section {
    width: 100%;
    background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
    border-radius: 14px;
    padding: 32px 18px 24px 18px;
    box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
    color: #4D1414;
    font-family: 'Montserrat', 'Karla', sans-serif;
    border: 2px solid #C46B02;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.admin-icon {
    height: 65px;
    width: 65px;
    background: #C46B02;
    color: #fffbe6;
    font-size: 2.6rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px auto;
    box-shadow: 0 2px 10px rgba(124,4,4,0.13);
    border: 2.5px solid #FDDE54;
}

.form-section h4 {
    font-size: 1.45rem;
    color: #7F0404;
    font-weight: 700;
    margin-bottom: 18px;
}

.input-field {
    display: flex;
    flex-direction: row;
    column-gap: 10px;
    justify-content: center;
    margin-bottom: 18px;
}

.input-field input {
    height: 50px;
    width: 47px;
    border-radius: 8px;
    outline: none;
    font-size: 1.125rem;
    text-align: center;
    border: 2px solid #C46B02;
    background: #fff;
    color: #7F0404;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(253,222,84,0.08);
    transition: border 0.2s, box-shadow 0.2s;
}

.input-field input:focus {
    border: 2.5px solid #7F0404;
    box-shadow: 0 0 0 0.18rem rgba(253,222,84,0.18);
    background: #fffbe6;
    color: #C46B02;
}

.btn-primary {
    background: linear-gradient(90deg, #C46B02 60%, #7F0404 100%);
    color: #fffbe6;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 1.2rem;
    border-radius: 50px;
    padding: 20px 35px;
    margin-top: 8px;
    letter-spacing: 1px;
    box-shadow: 0 4px 18px rgba(253,222,84,0.18);
    border: none;
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.btn-primary:hover {
    background: linear-gradient(90deg, #7F0404 60%, #C46B02 100%);
    color: #FDDE54;
    box-shadow: 0 8px 24px rgba(124,4,4,0.18);
}

.g-recaptcha {
    margin-top: 30px;
    margin-left: 70px;
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
        position: static;
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
.admin-icon {
    height: 65px;
    width: 65px;
    background: #0056b3;
    color: #fff;
    font-size: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px auto;
} 
  </style>
</head>
<body>
<a href="Homepage.html" class="back-icon entrance-animate" title="Back to Homepage">
    <i class="fas fa-arrow-left"></i>
</a>
<div class="flex-container entrance-animate">
  <div class="main-container">
    <div class="form-section">
      <div class="admin-icon"><i class="bx bxs-check-shield"></i></div>
      <h4 class="mb-4 text-center">Enter Admin Code</h4>
      <form method="POST" action="adminver.php">
        <div class="input-field mb-3">
          <input name="digit1" type="password" maxlength="1" />
          <input name="digit2" type="password" maxlength="1" />
          <input name="digit3" type="password" maxlength="1" />
          <input name="digit4" type="password" maxlength="1" />
          <input name="digit5" type="password" maxlength="1" />
          <input name="digit6" type="password" maxlength="1" />
          <input name="digit7" type="password" maxlength="1" />
          <input name="digit8" type="password" maxlength="1" />
        </div>
        <div id="recaptchaBox" class="g-recaptcha" data-sitekey="6LcWBTsrAAAAAACTAfiByoo40so_poPc4r8M7c5Z" style="display:none;"></div>
        <button type="submit" class="btn btn-primary w-100">VERIFY</button>
      </form>
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
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($lockout)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'Too many failed attempts. Redirecting to Home page...'
            }).then(function() {
                window.location.href = 'Homepage.html';
            });
        <?php 
        // Reset attempts after lockout so user can try again after redirect
        if (!empty($lockout)) {
            $_SESSION['admin_attempts'] = 0;
        }
        ?>
        <?php elseif (!empty($showAlert)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Verification Error',
                text: <?php echo json_encode($error); ?>
            });
        <?php endif; ?>

        const inputs = document.querySelectorAll('.input-field input');
        const recaptchaBox = document.getElementById('recaptchaBox');
        inputs.forEach((input, i) => {
            input.addEventListener('input', () => {
                if (input.value && i < inputs.length - 1) inputs[i+1].focus();
                const allFilled = [...inputs].every(i => i.value.trim() !== '');
                recaptchaBox.style.display = allFilled ? 'block' : 'none';
                if (!allFilled && window.grecaptcha) grecaptcha.reset();
            });
        });
    });
</script>
</body>
</html>
