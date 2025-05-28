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
        border-bottom: 4px solid #FDDE54;
    }

    header.scrolled {
      width: calc(100% - 40px);    /* leave padding space */
        margin: 10px 10px;           /* float away from edges */
        padding: 0px 95px;
        background: rgba(0,0,0,0.8);
        border-radius: 12px;
        border-bottom: none;         /* hide the yellow line */
        box-shadow: 0 8px 20px rgba(0,0,0,0.5);
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
    }


    #back-icon-container {
        position: fixed;
        margin-top: 20px;
        left: 5px;
        z-index: 200;
    }

    #back-icon-container .back-icon {
        position: fixed;
        
        left: 15px;
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
    #back-icon-container .back-icon:hover {
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




     /* Standings Styles */
    .standings-container {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
      width: 95%;
      margin-top: 100px;
      transition: all 0.3s ease;
    }

    .position-standings {
      background: rgba(45, 8, 8, 0.8);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .position-standings:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .position-title {
      color: #FDDE54;
      text-align: center;
      font-size: 1.8em;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid rgba(253, 222, 84, 0.3);
    }

    .candidates-standings {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .candidate-standing {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(74, 16, 16, 0.6);
      padding: 15px;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .candidate-standing:hover {
      transform: translateX(10px);
      background: rgba(74, 16, 16, 0.8);
    }

    .candidate-standing.leading {
      background: linear-gradient(90deg, rgba(74, 16, 16, 0.8) 0%, rgba(253, 222, 84, 0.2) 100%);
      border: 1px solid rgba(253, 222, 84, 0.3);
    }

    .candidate-info {
      display: flex;
      align-items: center;
      gap: 20px;
      flex: 1;
    }

    .candidate-image {
      width: 80px;
      height: 80px;
      border-radius: 10px;
      overflow: hidden;
      background: #2d0808;
    }

    .candidate-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .candidate-details {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .candidate-details h3 {
      color: #fff;
      margin: 0;
      font-size: 1.2em;
    }

    .candidate-details p {
      margin: 0;
      color: #FDDE54;
      font-size: 0.9em;
    }

    .vote-count {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
      min-width: 100px;
    }

    .vote-count .count {
      font-size: 2em;
      font-weight: bold;
      color: #FDDE54;
    }

    .vote-count .label {
      color: #fff;
      font-size: 0.9em;
    }

    .leading-icon {
      color: #FDDE54;
      font-size: 1.5em;
      margin-top: 5px;
    }

    .no-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #FDDE54;
      font-style: italic;
      font-size: 0.8em;
    }
    </style>
</head>
<body>


<header class="entrance-animate">
  <!-- Back Icon Button -->
 <div id="back-icon-container" class="entrance-animate">
<a href="Homepage.html" class="back-icon entrance-animate" id="back" title="Back to Homepage">
    <i class="fas fa-arrow-left"></i>
</a>
</div>
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
 <!-- Standings Display -->
    <div class="standings-container entrance-animate">
      <?php
      include 'db.php';

      // Get all positions
      $positions_query = "SELECT DISTINCT position_id, position FROM candidate_positions ORDER BY position_id";
      $positions_result = $conn->query($positions_query);

      while ($position = $positions_result->fetch_assoc()) {
        $position_id = $position['position_id'];
        $position_name = $position['position'];
        ?>
        <div class="position-standings">
          <h2 class="position-title"><?php echo htmlspecialchars($position_name); ?></h2>
          <div class="candidates-standings">
            <?php
            // Get candidates and their vote counts for this position
            $candidates_query = "
              SELECT 
                cp.id,
                cp.candidate_id,
                cp.name,
                cp.year,
                cp.program,
                cp.image,
                COUNT(v.id) as vote_count
              FROM candidate_positions cp
              LEFT JOIN votes v ON cp.candidate_id = v.candidate_id AND cp.position_id = v.position_id
              WHERE cp.position_id = ?
              GROUP BY cp.id, cp.candidate_id, cp.name, cp.year, cp.program, cp.image
              ORDER BY vote_count DESC
            ";
            
            $stmt = $conn->prepare($candidates_query);
            $stmt->bind_param("i", $position_id);
            $stmt->execute();
            $candidates_result = $stmt->get_result();

            $max_votes = 0;
            $candidates_data = array();

            while ($candidate = $candidates_result->fetch_assoc()) {
              $candidates_data[] = $candidate;
              if ($candidate['vote_count'] > $max_votes) {
                $max_votes = $candidate['vote_count'];
              }
            }

            foreach ($candidates_data as $candidate) {
              $is_leading = $candidate['vote_count'] == $max_votes && $max_votes > 0;
              ?>
              <div class="candidate-standing <?php echo $is_leading ? 'leading' : ''; ?>">
                <div class="candidate-info">
                  <div class="candidate-image">
                    <?php if (!empty($candidate['image'])): ?>
                      <img src="<?php echo htmlspecialchars($candidate['image']); ?>" 
                           alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                    <?php else: ?>
                      <div class="no-image">No Image</div>
                    <?php endif; ?>
                  </div>
                  <div class="candidate-details">
                    <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                    <p class="program"><?php echo htmlspecialchars($candidate['program']); ?></p>
                    <p class="year"><?php echo htmlspecialchars($candidate['year']); ?> Year</p>
                    <p class="candidate-number">Candidate #<?php echo htmlspecialchars($candidate['candidate_id']); ?></p>
                  </div>
                </div>
                <div class="vote-count">
                  <span class="count"><?php echo $candidate['vote_count']; ?></span>
                  <span class="label">votes</span>
                  <?php if ($is_leading): ?>
                    <i class="fas fa-trophy leading-icon"></i>
                  <?php endif; ?>
                </div>
              </div>
              <?php
            }
            $stmt->close();
            ?>
          </div>
        </div>
        <?php
      }
      $conn->close();
      ?>
    </div>

<footer class="entrance-animate">
    <p>&copy; 2025 EVSU Voting System. All rights reserved.</p>
    <nav>
        <a href="#">Privacy Policy</a> | <a href="#">Terms of Use</a> | <a href="#">Help</a>
    </nav>
</footer>
<script>
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    const containers = document.querySelectorAll('.position-standings');
    
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
        containers.forEach(container => {
            container.style.transform = 'translateY(-5px)';
            container.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.3)';
        });
    } else {
        header.classList.remove('scrolled');
        containers.forEach(container => {
            container.style.transform = 'translateY(0)';
            container.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.2)';
        });
    }
});
</script>
</body>
</html>
