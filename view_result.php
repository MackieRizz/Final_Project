<?php
// Keep the basic database connection
include 'db.php';

// Ensure database connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EVSU Dashboard</title>
    <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .evsu-logo-nav img {
        width: 45px;
        height: auto;
        margin-top: 12px;
        margin-left: -5px;
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
      <a href="Homepage.html"><i class="fas fa-sun"></i>SAR<span>JAGA.</span></a>
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

<div class="main-content">
    <!-- Your content for the result page goes here -->
</div>

<footer class="entrance-animate">
    <p>&copy; 2025 EVSU Voting System. All rights reserved.</p>
    <nav>
        <a href="#">Privacy Policy</a> | <a href="#">Terms of Use</a> | <a href="#">Help</a>
    </nav>
</footer>

<script>
    // Menu toggle functionality
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('nav ul').classList.toggle('active');
    });
</script>

</body>
</html>
