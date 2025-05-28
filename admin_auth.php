<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'Final_Project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT username, password, profile_pic FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_profile_pic'] = $admin['profile_pic'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Admin not found";
    }
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['reg_username']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Handle profile picture upload
        $profile_pic_path = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profile_pics/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_types)) {
                $new_filename = uniqid('profile_') . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
                    $profile_pic_path = $target_path;
                } else {
                    $error = "Error uploading profile picture";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }

        if (!$error) {
            // Check if username/email already exists
            $check_sql = "SELECT id FROM admin WHERE username = ? OR email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $username, $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Username or email already exists";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO admin (email, username, password, profile_pic) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $email, $username, $hashed_password, $profile_pic_path);

                if ($stmt->execute()) {
                    $success = "Registration successful! Please login.";
                } else {
                    $error = "Error registering admin: " . $conn->error;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EVSU VOTING - Admin Authentication</title>
    <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Karla:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Karla', sans-serif;
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
            padding: 20px;
            overflow-y: auto;
        }

        .auth-container {
            background: rgba(77, 20, 20, 0.92);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            width: 100%;
            max-width: 500px;
            padding: 20px;
           
        }

        .admin-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .admin-header h1 {
            color: #FDDE54;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .admin-icon {
            font-size: 2.5rem;
            color: #FDDE54;
            margin-bottom: 10px;
        }

        .form-section {
            background: linear-gradient(135deg, #fffbe6 60%, #FDDE54 100%);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(124, 4, 4, 0.13), 0 1.5px 6px rgba(253,222,84,0.08);
            color: #4D1414;
            border: 2px solid #C46B02;
        }

        .nav-tabs {
            border: none;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .nav-tabs .nav-link {
            border: none;
            background: transparent;
            color: #FDDE54;
            font-size: 1rem;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: #f4bb00;
            transform: translateY(-2px);
        }

        .nav-tabs .nav-link.active {
            background: #FDDE54;
            color: #7F0404;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 3px;
            font-weight: 600;
            color: #7F0404;
        }

        .form-control {
            background: #fff;
            color: #7F0404;
            border-radius: 8px;
            border: 2px solid #C46B02;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 12px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border: 2px solid #7F0404;
            box-shadow: 0 0 0 0.18rem rgba(253,222,84,0.18);
            background: #fffbe6;
            color: #C46B02;
        }

        .btn-primary {
            background: linear-gradient(90deg, #C46B02 60%, #7F0404 100%);
            color: #fffbe6;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 8px;
            padding: 8px 20px;
            border: none;
            box-shadow: 0 4px 18px rgba(253,222,84,0.18);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 8px;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #7F0404 60%, #C46B02 100%);
            color: #FDDE54;
            box-shadow: 0 8px 24px rgba(124,4,4,0.18);
            transform: translateY(-2px);
        }

        .profile-pic-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #C46B02;
            margin: 8px auto;
            display: block;
            object-fit: cover;
            background: #fff;
        }

        .back-icon {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 200;
            background: rgba(253, 222, 84, 0.85);
            border: 2px solid #C46B02;
            border-radius: 45%;
            width: 45px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7F0404;
            font-size: 1.5rem;
            box-shadow: 0 2px 10px rgba(124,4,4,0.13);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-icon:hover {
            background: #C46B02;
            border: 2px solid #FDDE54;
            color: #fffbe6;
            box-shadow: 0 8px 32px rgba(124,4,4,0.28);
            transform: scale(1.1) translateY(-2px);
        }

        .alert {
            margin-bottom: 15px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px;
        }

        .mb-3 {
            margin-bottom: 0.8rem !important;
        }

        @media (max-width: 576px) {
            .auth-container {
                margin: 15px auto;
                padding: 15px;
                max-width: 95%;
            }

            .form-section {
                padding: 15px;
            }

            .admin-header h1 {
                font-size: 1.5rem;
            }

            .nav-tabs .nav-link {
                font-size: 0.9rem;
                padding: 6px 15px;
            }
        }
    </style>
</head>
<body>
    <a href="Homepage.html" class="back-icon entrance-animate" title="Back to Homepage">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="auth-container entrance-animate">
        <div class="admin-header">
            <i class="fas fa-user-shield admin-icon"></i>
            <h1>Admin Authentication</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs" id="authTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Register</button>
            </li>
        </ul>

        <div class="tab-content" id="authTabsContent">
            <!-- Login Form -->
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <div class="form-section">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                <div class="form-section">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="reg_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="reg_username" name="reg_username" required>
                        </div>
                        <div class="mb-3">
                            <label for="reg_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="reg_password" name="reg_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile_pic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control mt-2" id="profile_pic" name="profile_pic" accept="image/*">
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i> Register
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show active tab based on error/success messages
        <?php if ($error && isset($_POST['register'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Tab(document.querySelector('#register-tab')).show();
            });
        <?php endif; ?>

        // Profile picture preview
        document.getElementById('profile_pic').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile_pic_preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 