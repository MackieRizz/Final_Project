<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: adminver.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
$profile_pic = $_SESSION['admin_profile_pic'] ?? 'https://i.pinimg.com/564x/b4/ba/ab/b4baab4d57a5d0d4bbb3455ad57bda80.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EVSU Dashboard</title>
  <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    .refresh-btn {
      margin-left: 20px;
      cursor: pointer;
      color: #FDDE54;
      font-size: 20px;
      transition: transform 0.3s ease;
    }
    .refresh-btn:hover {
      transform: rotate(180deg);
    }
    .refresh-btn.loading {
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
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
      z-index: 1000;
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

    #logoutModal {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
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

    .edit-passcode-btn {
      position: absolute;
      bottom: 15px;
      right: 15px;
      background: transparent;
      border: 1px solid #FDDE54;
      color: #FDDE54;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .edit-passcode-btn:hover {
      background: rgba(253, 222, 84, 0.1);
      box-shadow: 0 0 10px rgba(253, 222, 84, 0.2);
      transform: scale(1.1);
    }

    .edit-passcode-btn i {
      font-size: 14px;
      color: #FDDE54;
    }

    .passcode-modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 2000;
    }

    .passcode-modal-container {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #2d0808;
      padding: 35px;
      border-radius: 15px;
      width: 100%;
      max-width: 500px;
      z-index: 2001;
      border: 1px solid rgba(253, 222, 84, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .passcode-modal-container h3 {
      color: #FDDE54;
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.5em;
    }

    .passcode-input-group {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-bottom: 25px;
    }

    .passcode-input-group input {
      width: 40px;
      height: 40px;
      text-align: center;
      border: 2px solid #FDDE54;
      background: rgba(253, 222, 84, 0.1);
      border-radius: 8px;
      color: #FDDE54;
      font-size: 1.2em;
      outline: none;
    }

    .passcode-input-group input:focus {
      border-color: #fff;
      background: rgba(255, 255, 255, 0.1);
    }

    .update-passcode-btn {
      width: 100%;
      padding: 12px;
      background: #FDDE54;
      border: none;
      border-radius: 8px;
      color: #2d0808;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .update-passcode-btn:hover {
      background: #fff;
      transform: translateY(-2px);
    }

    /* Standings Styles */
    .standings-container {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .position-standings {
      background: rgba(45, 8, 8, 0.8);
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
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
  <div class="sidebar" id="sidebar">
    <div>
      <div class="menu">
        <div class="menu-title">Admin Dashboard</div>
        <div class="menu-item" onclick="window.location.href='admin_dashboard.php'"><i class="fas fa-chart-line"></i><span class="label1">Analytics</span></div>
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
      <div class="refresh-btn" onclick="refreshStandings()" title="Refresh Results">
        <i class="fas fa-sync-alt"></i>
      </div>
      <div class="profile">
        <i class="fas fa-user-circle"></i>
        <div class="profile-modal">
          <div class="cover-photo"></div>
          <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile">
          <br><br>
          <p id="name"><?php echo htmlspecialchars($admin_username); ?></p>
          <p id="role">Administrator</p>
          <button class="edit-passcode-btn" title="Edit Passcode">
            <i class="fas fa-key"></i>
          </button>
        </div>
      </div>
    </div>


    <div class="main-container">
    <!-- Standings Display -->
    <div class="standings-container">
      <?php
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
    </div>

    <!-- Logout Modal -->
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

    <!-- Passcode Update Modal -->
    <div id="passcodeModal" class="passcode-modal-overlay">
      <div class="passcode-modal-container">
        <h3>Update Admin Passcode</h3>
        <div class="passcode-input-group">
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
          <input type="text" maxlength="1" class="passcode-input" />
        </div>
        <button class="update-passcode-btn" onclick="updatePasscode()">Update Passcode</button>
      </div>
    </div>

<script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      sidebar.classList.toggle('collapsed');
      main.classList.toggle('collapsed');
    }

    //logout modal
    document.querySelector('.logout-btn').addEventListener('click', function () {
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

    // Profile Modal Toggle
    document.addEventListener('DOMContentLoaded', function() {
      const profileIcon = document.querySelector('.profile i');
      const profileModal = document.querySelector('.profile-modal');
      
      // Toggle modal on profile icon click
      profileIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        profileModal.style.display = profileModal.style.display === 'block' ? 'none' : 'block';
      });

      // Close modal when clicking outside
      document.addEventListener('click', function(e) {
        if (!profileModal.contains(e.target) && e.target !== profileIcon) {
          profileModal.style.display = 'none';
        }
      });

      // Prevent modal from closing when clicking inside it
      profileModal.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });

    // Add this to your existing script
    document.querySelector('.edit-passcode-btn').addEventListener('click', function(e) {
      e.stopPropagation();
      Swal.fire({
        title: 'Change Admin Passcode',
        text: 'Are you sure you want to change the admin passcode?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FDDE54',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('passcodeModal').style.display = 'block';
        }
      });
    });

    // Handle input navigation for passcode update
    const passcodeInputs = document.querySelectorAll('.passcode-input');
    passcodeInputs.forEach((input, index) => {
      input.addEventListener('input', function() {
        if (this.value.length === 1 && index < passcodeInputs.length - 1) {
          passcodeInputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && index > 0) {
          passcodeInputs[index - 1].focus();
        }
      });
    });

    // Close modal when clicking outside
    document.getElementById('passcodeModal').addEventListener('click', function(e) {
      if (e.target === this) {
        this.style.display = 'none';
        // Clear inputs
        passcodeInputs.forEach(input => input.value = '');
      }
    });

    function updatePasscode() {
      let newPasscode = '';
      passcodeInputs.forEach(input => {
        newPasscode += input.value;
      });

      if (newPasscode.length !== 8) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid Passcode',
          text: 'Please enter all 8 digits of the new passcode.'
        });
        return;
      }

      // Send the update request to the server
      fetch('update_passcode.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'new_passcode=' + newPasscode
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('passcodeModal').style.display = 'none';
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Admin passcode has been updated successfully.'
          }).then(() => {
            document.getElementById('passcodeModal').style.display = 'none';
            passcodeInputs.forEach(input => input.value = '');
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Failed to update passcode. Please try again.'
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred. Please try again.'
        });
      });
    }

    function refreshStandings() {
      const refreshBtn = document.querySelector('.refresh-btn i');
      refreshBtn.classList.add('loading');
      
      // Fetch the latest standings
      fetch('get_standings.php')
        .then(response => response.text())
        .then(html => {
          document.querySelector('.standings-container').innerHTML = html;
          refreshBtn.classList.remove('loading');
          
          // Show success message
          Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Standings have been refreshed',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
          });
        })
        .catch(error => {
          console.error('Error:', error);
          refreshBtn.classList.remove('loading');
          
          // Show error message
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to refresh standings',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
        });
    }
  </script>
</body>
</html>
