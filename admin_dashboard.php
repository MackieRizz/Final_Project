<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EVSU Dashboard</title>
  <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Karla', sans-serif;
    }
    body {
      background: linear-gradient(135deg, #1a0606, #f79f56);
      color: #FDDE54;
      min-height: 100vh;
      display: flex;
    }
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #2d0808 0%, #461212 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      height: 100vh;
      overflow: hidden;
      position: fixed;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      border-right: 1px solid rgba(253, 222, 84, 0.1);
      z-index: 1000;
    }
    .sidebar.collapsed {
      width: 0px;
    }
    .sidebar .menu {
      padding: 20px;
    }
    .sidebar .menu-item {
      padding: 15px 20px;
      color: #FDDE54;
      display: flex;
      align-items: center;
      gap: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      border-radius: 0 30px 30px 0;
      margin: 5px 0;
      position: relative;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .label {
      margin-left: 7px;
    }
    .label1 {
      margin-left: 10px;
    }
    .sidebar .menu-item i {
      font-size: 20px;
      transition: all 0.3s ease;
      width: 30px;
      text-align: center;
    }
    .sidebar .menu-item:hover {
      background: linear-gradient(90deg, #4a1010 0%, rgba(74, 16, 16, 0.8) 100%);
      transform: translateX(10px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    .sidebar .menu-item:hover i {
      transform: scale(1.2);
      color: #fff;
    }
    .logout-btn {
      padding: 15px 10px;
      color: #FDDE54;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      margin-bottom: 10px;
      margin-left: 20px;
      width: 87%;
      transition: all 0.3s ease;
      border-radius: 10px;
      position: relative;
      overflow: hidden;
    }
    .logout-btn:hover {
      background: #4a1010;
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    .main-content {
      margin-left: 250px;
      transition: margin-left 0.3s ease;
      width: 100%;
    }
    .main-content.collapsed {
      margin-left: 0;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 25px 20px;
      background: rgba(0,0,0,0.6);
      position: sticky;
      top: 0;
      z-index: 10;
      width: 99.3%;
      height: 40px;
      transition: none;
      margin: 7px 5px 5px 5px;
      border-radius: 5px;
    }
    .toggle-btn {
      font-size: 20px;
      cursor: pointer;
      color: #FDDE54;
    }
    .profile {
      position: relative;
      margin-left: auto;
    }
    .profile i {
      font-size: 24px;
      cursor: pointer;
    }
    .profile-modal {
      display: none;
      position: absolute;
      right: 0;
      top: 40px;
      background: #4a1010;
      border-radius: 10px;
      padding: 20px;
      width: 250px;
      height: 300px;
      text-align: center;
      color: #FDDE54;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    .profile:hover .profile-modal {
      display: block;
    }
    .profile-modal .cover-photo {
      width: 100%;
      height: 120px;
      background: url('https://ormoc.evsu.edu.ph/wp-content/uploads/2023/12/IMG_8354_2-1-1-1024x575.jpg') no-repeat center center;
      background-size: 150%;
      filter: blur(1px);
      border-radius: 10px 10px 0 0;
      margin-bottom: 10px;
      position: relative;
    }
    .profile-modal img {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      border: 2px solid #FDDE54;
      position: absolute;
      top: 70px;
      left: 50%;
      transform: translateX(-50%);
      background: #fff;
    }
    .profile-modal #role {
      margin-top: 35px;
      font-size: 12px;
      color: #fff;
      
    }
    .profile-modal #name {
      position: relative;
      top: 30px;
      text-align: center;
      font-weight: bold;
      
      
    }

    .menu-title {
      font-weight: bold;
      font-size: 18px;
      padding: 15px 10px;
      color: #fff;
    }
    #Welcome {
      margin-left: 20px;
    }
    .analytics {
      padding: 20px;
    }
    .charts-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      
    }
    .chart-box {
      background: linear-gradient(145deg, rgba(43, 8, 8, 0.9), rgba(74, 16, 16, 0.8));
      border: 1px solid rgba(253, 222, 84, 0.1);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(4px);
      border-radius: 20px;
      padding: 15px;
      position: relative;
      overflow: hidden;
      flex: 1;
      flex: 1 1 500px;
      min-width: 300px;
      height: 300px;
      transition: flex-basis 0.3s ease;
      transition: all 0.3s ease;
    }

    .chart-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
      border: 1px solid rgba(253, 222, 84, 0.2);
      background: linear-gradient(145deg, rgba(53, 10, 10, 0.95), rgba(84, 18, 18, 0.85));
    }

    .chart-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(253, 222, 84, 0.3), transparent);
    }

    .chart-box h3 {
      color: #FDDE54;
      font-size: 1.5rem;
      margin-bottom: 20px;
      text-shadow: 0 0 10px rgba(253, 222, 84, 0.3);
      position: relative;
      display: inline-block;
    }

    .chart-box h4 {
      color: rgba(255, 255, 255, 0.9) !important;
      font-size: 1.1rem;
      text-shadow: 0 0 8px rgba(255, 255, 255, 0.2);
      position: relative;
    }

    .chart-box canvas {
      display: block;
      margin: 0 auto;
      height: 250px !important;
      transition: max-width 0.3s ease;
    }

    #logoutModal {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 99999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    #logoutModal.show {
      opacity: 1;
      visibility: visible;
    }

    .modal-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(2px);
    }

    .modal-box {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0.8);
      background: #2d0808;
      color: #FDDE54;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      width: 320px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.8);
      transition: transform 0.3s ease;
    }

    #logoutModal.show .modal-box {
      transform: translate(-50%, -50%) scale(1);
    }

    .modal-box h2 {
      margin-bottom: 15px;
      font-size: 1.5em;
    }

    .modal-box p {
      margin-bottom: 25px;
      font-size: 1.1em;
    }

    .modal-actions {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .modal-actions .btn {
      padding: 12px 25px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1em;
      min-width: 100px;
    }

    .confirm-btn {
      background-color: #FDDE54;
      color: #2d0808;
    }

    .confirm-btn:hover {
      background-color: #ff4d4d;
      color: #fff;
      transform: translateY(-2px);
    }

    .cancel-btn {
      background-color: #aaa;
      color: #2d0808;
    }

    .cancel-btn:hover {
      background-color: #666;
      color: #fff;
      transform: translateY(-2px);
    }

    /* Style for the total number container */
    .total-number {
      background: linear-gradient(145deg, rgba(43, 8, 8, 0.9), rgba(74, 16, 16, 0.8));
      border: 1px solid rgba(253, 222, 84, 0.1);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(4px);
      border-radius: 20px;
      padding: 15px;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .total-number:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
      border: 1px solid rgba(253, 222, 84, 0.2);
      background: linear-gradient(145deg, rgba(53, 10, 10, 0.95), rgba(84, 18, 18, 0.85));
    }

    .total-number:hover .number-display {
      text-shadow: 0 0 25px rgba(253, 222, 84, 0.6);
      color: #FFE584;
    }

    .total-number:hover h3, .chart-box:hover h3 {
      text-shadow: 0 0 15px rgba(253, 222, 84, 0.4);
      color: #FFE584;
    }

    .total-number:hover canvas {
      filter: drop-shadow(0 0 15px rgba(253, 222, 84, 0.15));
    }

    .total-number::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100px;
      height: 100px;
      background: radial-gradient(circle, rgba(253, 222, 84, 0.1) 0%, transparent 70%);
      border-radius: 50%;
      transform: translate(30%, -30%);
    }

    .total-number p {
      position: relative;
      z-index: 1;
    }

    /* Modern styling for numbers */
    .number-display {
      font-size: 60px;
      font-weight: bold;
      text-align: center;
      color: #FDDE54;
      text-shadow: 0 0 15px rgba(253, 222, 84, 0.4);
      margin-top: 80px;
      font-family: 'Arial', sans-serif;
      letter-spacing: 2px;
    }

    /* Add glow effect to charts */
    canvas {
      filter: drop-shadow(0 0 10px rgba(253, 222, 84, 0.1));
    }

    /* Modernize the Welcome title */
    #Welcome {
      font-size: 2rem;
      background: linear-gradient(90deg, #FDDE54, #FFA07A);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: 0 0 20px rgba(253, 222, 84, 0.2);
      font-weight: bold;
      letter-spacing: 1px;
    }

  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <div>
      <div class="menu">
        <div class="menu-title">Admin Dashboard</div>
        <div class="menu-item" onclick="scrollToSection('analytics')"><i class="fas fa-chart-line"></i><span class="label1">Analytics</span></div>
        <div class="menu-item" onclick="window.location.href='standing.php'"><i class="fas fa-trophy fa-solid fa-address-card"></i><span class="label1">Standings</span></div>
        <div class="menu-item" onclick="window.location.href='student_list.php'"><i class="fas fa-address-card"></i><span class="label1">Student List</span></div>
        <div class="menu-item" onclick="window.location.href='add_candidate_dashboard.php'"><i class="fas fa-users"></i><span class="label">Add Candidates</span></div>
      </div>
    </div>
    <div class="logout-btn"><i class="fas fa-sign-out-alt"></i><span class="label">Logout</span></div>
  </div>

  <div class="main-content" id="main">
    <div class="topbar">
      <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
      <div class="profile">
        <i class="fas fa-user-circle"></i>
        <div class="profile-modal">
          <div class="cover-photo"></div>
          <img src="https://i.pinimg.com/564x/b4/ba/ab/b4baab4d57a5d0d4bbb3455ad57bda80.jpg" alt="Profile">
          <br><br>
          <p id="name">SARJAGA</p>
          <p id="role">Administrator</p>
        </div>
      </div>
    </div>

    <div class="analytics" id="analytics">
      <h1 id="Welcome">Welcome to EVSU Voting Dashboard</h1>
      <br>

       <div class="charts-container">
        <div class="total-number">
          <h3>Total Number of Students</h3>
           <p class="number-display">
           <?php
              include 'db.php';

              $sql = "SELECT COUNT(student_id) AS total FROM students_registration";
              $result = $conn->query($sql);

              if (!$result) {
                  die("Query failed: " . $conn->error);
              }

              $row = $result->fetch_assoc();
              echo $row['total'];

              $conn->close();
              ?>
          </p>
        </div>
        
        <div class="chart-box">
          <h3>Number of Students per Department</h3>
            <ul style="font-size: 20px; color: #fff;">
            <?php
              include 'db.php';

              $departments = [
                'Teacher Education Department',
                'Engineering Department',
                'Computer Studies Department',
                'Industrial Technology Department',
                'Business and Management Department'
              ];

              $counts = [];

              foreach ($departments as $dept) {
                  $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM students_registration WHERE department = ?");
                  $stmt->bind_param("s", $dept);
                  $stmt->execute();
                  $stmt->bind_result($count);
                  $stmt->fetch();
                  $counts[] = $count;
                  $stmt->close();
              }

              $conn->close();
              ?>
          </ul>
          <canvas id="barChart" style="max-height: 220px; max-width: 90%; width: 100%;"></canvas>
          <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </div>
      <br>

      <div class="charts-container" style="width: 100%;">
        <div class="chart-box">
          <h3>Voting Statistics</h3>
          <div style="display: flex; justify-content: space-between; gap: 20px;">
            <div style="flex: 1; max-width: 25%;">
              <h4 style="text-align: center; color: #fff; margin-bottom: -15px; ">Overall Participation</h4>
              <div style="position: relative; width: 80%; margin: auto; padding-bottom: 10px;">
                <canvas id="pieChart"></canvas>
              </div>
            </div>
            <div style="flex: 1; max-width: 25%;">
              <h4 style="text-align: center; color: #fff; margin-bottom: -15px; ">Votes per Department</h4>
              <div style="position: relative; width: 80%; margin: auto; padding-bottom: 10px;">
                <canvas id="departmentPieChart"></canvas>
              </div>
            </div>
            <div style="flex: 1; max-width: 25%;">
              <h4 style="text-align: center; color: #fff; margin-bottom: -15px; ">Gender-based Voting</h4>
              <div style="position: relative; width: 80%; margin: auto; padding-bottom: 10px;">
                <canvas id="genderPieChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div id="logoutModal">
      <div class="modal-overlay"></div>
      <div class="modal-box">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <div class="modal-actions">
          <button class="btn cancel-btn" onclick="closeLogoutModal()">Cancel</button>
          <button class="btn confirm-btn" onclick="confirmLogout()">Logout</button>
        </div>
      </div>
    </div>

<script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      const barChartCanvas = document.getElementById('barChart');

      sidebar.classList.toggle('collapsed');
      main.classList.toggle('collapsed');

      
      if (sidebar.classList.contains('collapsed')) {
        barChartCanvas.style.maxWidth = '300%';
      } else {
        barChartCanvas.style.maxWidth = '200%';
      }

      Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
      });
    }

    function scrollToSection(id) {
      const element = document.getElementById(id);
      if (element) {
        window.scrollTo({ top: element.offsetTop, behavior: 'smooth' });
      }
    }

  const studentCounts = <?php echo json_encode($counts); ?>;

  const barCtx = document.getElementById('barChart').getContext('2d');
  new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: [
        'Teacher Education',
        'Engineering',
        'Computer Studies',
        'Industrial Tech',
        'Business & Mgmt'
      ],
      datasets: [{
        label: 'Students',
        data: studentCounts, 
        backgroundColor: '#FDDE54'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          enabled: true,
          callbacks: {
            label: function(context) {
              console.log(context); 
              const count = context.parsed.y ?? context.raw ?? 0;
              return count + ' students';
            }
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: '#fff'
          }
        },
        y: {
          min: 0,
          max: 200,
          ticks: {
            color: '#fff',
            stepSize: 10
          }
        }
      }
    }
  });


    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: ['Voted', 'Did Not Vote'],
        datasets: [{
          data: [800, 400],
          backgroundColor: ['#FDDE54', '#4a1010']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#fff',
              font: {
                size: 11
              },
              padding: 10,
              boxWidth: 15
            }
          }
        },
        layout: {
          padding: {
            bottom: 35,
            top: 25
          }
        },
        radius: '80%'
      }
    });
    
    Chart.helpers.each(Chart.instances, function(instance) {
      instance.resize();
    });

    <?php
      include 'db.php';
      
      // Fetch department data
      $deptQuery = "SELECT department, COUNT(*) as count FROM students_registration GROUP BY department";
      $deptResult = $conn->query($deptQuery);
      $deptLabels = [];
      $deptData = [];
      
      while($row = $deptResult->fetch_assoc()) {
          $deptLabels[] = $row['department'];
          $deptData[] = $row['count'];
      }
      
      // Fetch gender data
      $genderQuery = "SELECT gender, COUNT(*) as count FROM students_registration GROUP BY gender";
      $genderResult = $conn->query($genderQuery);
      $genderLabels = [];
      $genderData = [];
      
      while($row = $genderResult->fetch_assoc()) {
          $genderLabels[] = $row['gender'];
          $genderData[] = $row['count'];
      }
      
      $conn->close();
    ?>

    // Department Pie Chart
    const deptPieCtx = document.getElementById('departmentPieChart').getContext('2d');
    new Chart(deptPieCtx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($deptLabels); ?>,
        datasets: [{
          data: <?php echo json_encode($deptData); ?>,
          backgroundColor: ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#fff',
              font: {
                size: 11
              },
              padding: 10,
              boxWidth: 15
            }
          }
        },
        layout: {
          padding: {
            bottom: 35,
            top: 25
          }
        },
        radius: '80%'
      }
    });

    // Gender Pie Chart
    const genderPieCtx = document.getElementById('genderPieChart').getContext('2d');
    new Chart(genderPieCtx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($genderLabels); ?>,
        datasets: [{
          data: <?php echo json_encode($genderData); ?>,
          backgroundColor: ['#007BFF', '#FF69B4']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#fff',
              font: {
                size: 11
              },
              padding: 10,
              boxWidth: 15
            }
          }
        },
        layout: {
          padding: {
            bottom: 35,
            top: 25
          }
        },
        radius: '80%'
      }
    });

    //logout modal
    document.querySelector('.logout-btn').addEventListener('click', function() {
      const modal = document.getElementById('logoutModal');
      modal.classList.add('show');
    });

    // Close modal when clicking overlay
    document.querySelector('.modal-overlay').addEventListener('click', closeLogoutModal);

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeLogoutModal();
      }
    });

    function closeLogoutModal() {
      const modal = document.getElementById('logoutModal');
      modal.classList.remove('show');
    }

    function confirmLogout() {
      window.location.href = 'logout.php';
    }


  </script>
</body>
</html>
