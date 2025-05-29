<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Submitted Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@300;400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <style>
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
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .success-container {
            background: rgba(77, 20, 20, 0.92);
            padding: 3rem;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
            max-width: 500px;
            width: 90%;
            border: 1px solid rgba(253, 222, 84, 0.1);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-icon {
            color: #FDDE54;
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #FDDE54;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        p {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-home {
            background: linear-gradient(135deg, #FDDE54, #F4BB00);
            color: #7F0404;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(253, 222, 84, 0.2);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(253, 222, 84, 0.3);
            background: linear-gradient(135deg, #F4BB00, #FDDE54);
            color: #4D1414;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h2>Thank You for Voting!</h2>
        <p>Your vote has been successfully recorded. Thank you for participating in the election.</p>
        <a href="Homepage.html" class="btn-home">Return to Home</a>
    </div>
</body>
</html> 