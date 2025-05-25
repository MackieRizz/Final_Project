<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EVSU Dashboard</title>
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
      background-color: #2d0808;
      transition: width 0.3s ease;
      height: 100vh;
      overflow: hidden;
      position: fixed;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar.collapsed {
      width: 0px;
    }
    .sidebar .menu {
      padding: 20px;
    }
    .sidebar .menu-item {
      padding: 15px 10px;
      color: #FDDE54;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
    }
    .label {
      margin-left: 7px;
    }
    .label1 {
      margin-left: 10px;
    }
    .sidebar .menu-item:hover {
      background: #4a1010;
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
    }
    .logout-btn:hover {
      background: #4a1010;
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
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 10px;
      flex: 1;
      flex: 1 1 500px;
      min-width: 300px;
      height: 300px;
      transition: flex-basis 0.3s ease;
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
      z-index: 9999;
    }

    .modal-overlay {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
    }

    .modal-box {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #2d0808;
      color: #FDDE54;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      width: 320px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
    }

    .modal-box h2 {
      margin-bottom: 10px;
    }

    .modal-box p {
      margin-bottom: 20px;
    }

    .modal-actions {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .modal-actions .btn {
      padding: 10px 20px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .confirm-btn {
      background-color: #FDDE54;
      color: #2d0808;
    }

    .confirm-btn:hover {
      background-color: #ff4d4d;
      color: #fff;
    }

    .cancel-btn {
      background-color: #aaa;
      color: #2d0808;
    }

    .cancel-btn:hover {
      background-color: #666;
      color: #fff;
    }


  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <div>
      <div class="menu">
        <div class="menu-title">Admin Dashboard</div>
        <div class="menu-item" onclick="scrollToSection('analytics')"><i class="fas fa-chart-line"></i><span class="label1">Analytics</span></div>
        <div class="menu-item" onclick="window.location.href='standing.php'"><i class="fas fa-trophy"></i><span class="label1">Standings</span>
      </div>
        <div class="menu-item" onclick="window.location.href='add_candidate_dashboard.html'"><i class="fas fa-users"></i><span class="label">Add Candidates</span></div>
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
          <img src="https://via.placeholder.com/80" alt="Profile">
          <br><br>
          <p id="name">Tirzo Charles Apuya</p>
          <p id="role">Administrator</p>
        </div>
      </div>
    </div>

    <div class="analytics" id="analytics">
      <h1 id="Welcome">Welcome to EVSU Voting Dashboard</h1>
      <br>

      <div class="charts-container">
        <div class="chart-box">
          <h3>Total Number of Students</h3>
          <p style="font-size: 60px; font-weight: bold; text-align: center; color: #fff; margin-top: 80px;" id="totalStudents">0</p>
        </div>

        <div class="chart-box">
          <h3>Number of Students per Department</h3>
          <canvas id="barChart"></canvas>
        </div>
      </div>
      <br>

      <div class="charts-container">
        <div class="chart-box">
          <h3>Voting Participation</h3>
          <canvas id="pieChart"></canvas>
        </div>
      </div>

    </div>
    <div id="logoutModal" style="display: none;">
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

    const studentCounts = [300, 250, 200, 150, 100];
    const total = studentCounts.reduce((a, b) => a + b, 0);
    document.getElementById('totalStudents').textContent = total;

    const barCtx = document.getElementById('barChart').getContext('2d');
      new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: ['Engineering', 'IT', 'Education', 'Business', 'Agriculture'],
          datasets: [{
            label: 'Students',
            data: studentCounts,
            backgroundColor: '#FDDE54'
          }]
        },
        options: {
          responsive: true, 
          plugins: {
            legend: {
              display: false,
              labels: {
                color: '#fff'
              }
            }
          },
          scales: {
           x: {
            ticks: {
              color: '#fff',
              maxRotation: 0,
              minRotation: 0
            }
          },
            y: {
              ticks: {
                color: '#fff'
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
        plugins: {
          legend: {
            labels: {
              color: '#fff'
            }
          }
        }
      }

    });
    
    Chart.helpers.each(Chart.instances, function(instance) {
      instance.resize();
    });


    //logout modal
    document.querySelector('.logout-btn').addEventListener('click', function () {
    document.getElementById('logoutModal').style.display = 'block';
  });

  function closeLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
  }

  function confirmLogout() {
    
    window.location.href = 'logout.php'; // Replace with actual logout logic
  }


  </script>
</body>
</html>
