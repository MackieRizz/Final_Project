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
    <title>EVSU Election Results - Official Tally</title>
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
      width: calc(100% - 40px);
        margin: 10px 10px;
        padding: 0px 95px;
        background: rgba(0,0,0,0.8);
        border-radius: 12px;
        border-bottom: none;
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

   /* Official Results Header */
.results-header {
    width: 100%;
    background: rgba(0, 0, 0, 0.85);
    border: 2px solid #FDDE54;
    margin-top: 120px;
    margin-bottom: 20px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.results-header h1 {
    color: #FDDE54;
    font-size: 1.6em;
    font-weight: 700;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.results-header .official-seal {
    color: #C46B02;
    font-size: 0.9em;
    font-weight: 600;
    margin-bottom: 3px;
}

.results-header .disclaimer {
    color: #FDDE54;
    font-size: 0.8em;
    font-style: italic;
    opacity: 0.9;
}

/* Results Container */
.results-container {
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 25px;
    max-width: 1200px;
    margin: 0 auto;
    width: 95%;
}

/* Position Results Table */
.position-results {
    background: rgba(0, 0, 0, 0.9);
    border: 2px solid #FDDE54;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
}

.position-header {
    background: linear-gradient(135deg, #C46B02 0%, #800000 100%);
    padding: 12px 15px;
    border-bottom: 2px solid #FDDE54;
}

.position-header h2 {
    color: #FDDE54;
    font-size: 1.3em;
    font-weight: 700;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Results Table */
.results-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(0, 0, 0, 0.8);
}

.results-table thead {
    background: rgba(196, 107, 2, 0.3);
}

.results-table th {
    padding: 12px 10px;
    color: #FDDE54;
    font-weight: 700;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 2px solid rgba(253, 222, 84, 0.3);
    text-align: left;
}

.results-table th:last-child {
    text-align: center;
    width: 100px;
}

.results-table tbody tr {
    border-bottom: 1px solid rgba(253, 222, 84, 0.2);
    transition: background 0.3s ease;
}

.results-table tbody tr:hover {
    background: rgba(253, 222, 84, 0.1);
}

.results-table tbody tr.leading {
    background: rgba(253, 222, 84, 0.15);
    border-left: 4px solid #FDDE54;
}

.results-table td {
    padding: 12px 10px;
    color: #fff;
    vertical-align: middle;
}

/* Candidate Info Cell */
.candidate-info-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.candidate-photo {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    overflow: hidden;
    background: rgba(45, 8, 8, 0.8);
    border: 2px solid rgba(253, 222, 84, 0.3);
    flex-shrink: 0;
}

.candidate-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.candidate-photo .no-photo {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FDDE54;
    font-size: 0.7em;
}

.candidate-details {
    flex: 1;
}

.candidate-name {
    font-size: 1.1em;
    font-weight: 700;
    color: #FDDE54;
    margin-bottom: 2px;
}

.candidate-program {
    color: #C46B02;
    font-weight: 600;
    font-size: 0.85em;
    margin-bottom: 1px;
}

.candidate-year {
    color: rgba(253, 222, 84, 0.8);
    font-size: 0.8em;
}

.candidate-id {
    color: rgba(253, 222, 84, 0.6);
    font-size: 0.75em;
    font-style: italic;
}

/* Vote Count Cell */
.vote-count-cell {
    text-align: center;
    position: relative;
}

.vote-number {
    font-size: 1.6em;
    font-weight: 800;
    color: #FDDE54;
    display: block;
    margin-bottom: 2px;
}

.vote-label {
    color: rgba(253, 222, 84, 0.8);
    font-size: 0.75em;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.leading-indicator {
    position: absolute;
    top: 5px;
    right: 39px;
    color: #FDDE54;
    font-size: 1.2em;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}


/* Responsive Design */
@media (max-width: 1024px) {
    .results-table th,
    .results-table td {
        padding: 10px 8px;
    }
    
    .candidate-info-cell {
        gap: 10px;
    }
    
    .candidate-photo {
        width: 45px;
        height: 45px;
    }
    
    .vote-number {
        font-size: 1.4em;
    }
}

@media (max-width: 768px) {
    .results-header h1 {
        font-size: 1.4em;
    }
    
    .results-table {
        font-size: 0.85em;
    }
    
    .candidate-info-cell {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
    
    .candidate-photo {
        width: 40px;
        height: 40px;
    }
    
    .vote-number {
        font-size: 1.2em;
    }
    
    .status-bar {
        flex-direction: column;
        gap: 8px;
        padding: 8px 15px;
    }

    .leading-indicator{
        top: 20px;
    }
}
    </style>
</head>
<body>

<header class="entrance-animate">
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

<!-- Official Results Header -->
<div class="results-header entrance-animate">
    <div class="official-seal">
        <i class="fas fa-certificate"></i> OFFICIAL ELECTION RESULTS
    </div>
    <h1>EVSU Student Government Election</h1>
    <div class="disclaimer">
        Real-time results - Final certification pending
    </div>
</div>

<!-- Results Display -->
<div class="results-container entrance-animate">
  <?php
  include 'db.php';

  // Get all positions
  $positions_query = "SELECT DISTINCT position_id, position FROM candidate_positions ORDER BY position_id";
  $positions_result = $conn->query($positions_query);

  while ($position = $positions_result->fetch_assoc()) {
    $position_id = $position['position_id'];
    $position_name = $position['position'];
    ?>
    <div class="position-results">
      <div class="position-header">
        <h2><?php echo htmlspecialchars($position_name); ?></h2>
      </div>
      
      <table class="results-table">
        <thead>
          <tr>
            <th><i class="fas fa-user"></i> Candidate Information</th>
            <th><i class="fas fa-vote-yea"></i> Vote Count</th>
          </tr>
        </thead>
        <tbody>
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
            <tr class="<?php echo $is_leading ? 'leading' : ''; ?>">
              <td>
                <div class="candidate-info-cell">
                  <div class="candidate-photo">
                    <?php if (!empty($candidate['image'])): ?>
                      <img src="<?php echo htmlspecialchars($candidate['image']); ?>" 
                           alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                    <?php else: ?>
                      <div class="no-photo">
                        <i class="fas fa-user"></i>
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="candidate-details">
                    <div class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></div>
                    <div class="candidate-program"><?php echo htmlspecialchars($candidate['program']); ?></div>
                    <div class="candidate-year"><?php echo htmlspecialchars($candidate['year']); ?> Year</div>
                    <div class="candidate-id">Candidate #<?php echo htmlspecialchars($candidate['candidate_id']); ?></div>
                  </div>
                </div>
              </td>
              <td class="vote-count-cell">
                <span class="vote-number"><?php echo number_format($candidate['vote_count']); ?></span>
                <span class="vote-label">Votes</span>
                <?php if ($is_leading): ?>
                  <i class="fas fa-crown leading-indicator" title="Leading"></i>
                <?php endif; ?>
              </td>
            </tr>
            <?php
          }
          $stmt->close();
          ?>
        </tbody>
      </table>
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
    
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Auto-refresh timestamp (visual only - no actual refresh)
setInterval(function() {
    const now = new Date();
    const timeString = now.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    }) + ' - ' + now.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit', 
        hour12: true 
    });
    document.getElementById('last-update').textContent = timeString;
}, 60000); // Update every minute
</script>
</body>
</html>