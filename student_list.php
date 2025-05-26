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

    /* Student List Styles */
    .student-list-section {
      padding: 20px;
      margin: 20px;
      background: rgba(0, 0, 0, 0.4);
      border-radius: 10px;
    }

    .controls {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .search-box input {
      padding: 8px 15px;
      border: 2px solid #FDDE54;
      border-radius: 5px;
      background: rgba(0, 0, 0, 0.2);
      color: #fff;
      width: 300px;
    }

    .search-box input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .sort-controls {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .sort-controls select {
      padding: 8px 15px;
      border: 2px solid #FDDE54;
      border-radius: 5px;
      background: rgba(0, 0, 0, 0.2);
      color: #fff;
      cursor: pointer;
    }

    .sort-controls select option {
      background: #2d0808;
      color: #fff;
    }

    .table-container {
      overflow-x: auto;
    }

    #studentTable {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: rgba(0, 0, 0, 0.2);
    }

    #studentTable th,
    #studentTable td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid rgba(253, 222, 84, 0.3);
    }

    #studentTable th {
      background: rgba(253, 222, 84, 0.1);
      color: #FDDE54;
      font-weight: bold;
    }

    #studentTable tbody tr:hover {
      background: rgba(253, 222, 84, 0.1);
    }

    #studentTable tbody tr td {
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
      <div class="profile">
        <i class="fas fa-user-circle"></i>
        <div class="profile-modal">
          <div class="cover-photo"></div>
          <img src="https://i.pinimg.com/564x/b4/ba/ab/b4baab4d57a5d0d4bbb3455ad57bda80.jpg" alt="Profile">
          <br><br>
          <p id="name">SARJAGA</p>
          <p id="role">Administrator</p>
          <button class="edit-passcode-btn" title="Edit Passcode">
            <i class="fas fa-key"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="student-list-section">
      <div class="controls">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search students...">
        </div>
        <div class="sort-controls">
          <select id="departmentSort">
            <option value="">Sort by Department</option>
            <option value="Teacher Education Department">Teacher Education</option>
            <option value="Engineering Department">Engineering</option>
            <option value="Computer Studies Department">Computer Studies</option>
            <option value="Industrial Technology Department">Industrial Technology</option>
            <option value="Business and Management Department">Business & Management</option>
          </select>
          <select id="programSort">
            <option value="">Sort by Program</option>
            <?php
              include 'db.php';
              $sql = "SELECT DISTINCT program FROM students_registration ORDER BY program";
              $result = $conn->query($sql);
              while($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['program']) . "'>" . htmlspecialchars($row['program']) . "</option>";
              }
            ?>
          </select>
          <select id="sectionSort">
            <option value="">Sort by Year & Section</option>
            <?php
              $sql = "SELECT DISTINCT section FROM students_registration ORDER BY section";
              $result = $conn->query($sql);
              while($row = $result->fetch_assoc()) {
                echo "<option value='" . htmlspecialchars($row['section']) . "'>" . htmlspecialchars($row['section']) . "</option>";
              }
              $conn->close();
            ?>
          </select>
        </div>
      </div>

      <button id="deleteSelected" style="background:#ff4d4d;color:#fff;padding:8px 16px;border:none;border-radius:5px;cursor:pointer;margin-bottom:10px;">Delete Selected</button>

      <div class="table-container">
        <table id="studentTable">
          <thead>
            <tr>
              <th><input type="checkbox" id="selectAll"></th>
              <th>Student ID</th>
              <th>Full Name</th>
              <th>Department</th>
              <th>Program</th>
              <th>Year & Section</th>
            </tr>
          </thead>
          <tbody>
            <?php
              include 'db.php';
              $sql = "SELECT student_id, fullname, department, program, section 
                     FROM students_registration 
                     ORDER BY student_id DESC";
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo '<td><input type="checkbox" class="select-student" value="' . htmlspecialchars($row['student_id']) . '"></td>';
                  echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['program']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['section']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='6'>No students found</td></tr>";
              }
              $conn->close();
            ?>
          </tbody>
        </table>
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

    <div id="notificationModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:10000;">
      <div class="modal-overlay"></div>
      <div class="modal-box" style="top:40%;">
        <h2>Success</h2>
        <p id="notificationMessage">Selected students deleted successfully.</p>
        <div class="modal-actions">
          <button class="btn confirm-btn" onclick="closeNotificationModal()">OK</button>
        </div>
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
      document.getElementById('logoutModal').style.display = 'block';
    });

    function closeLogoutModal() {
      document.getElementById('logoutModal').style.display = 'none';
    }

    function confirmLogout() {
      window.location.href = 'logout.php';
    }

    // Make closeNotificationModal globally accessible
    window.closeNotificationModal = function() {
      document.getElementById('notificationModal').style.display = 'none';
    }

    // Student list search and sort functionality
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const departmentSort = document.getElementById('departmentSort');
      const programSort = document.getElementById('programSort');
      const sectionSort = document.getElementById('sectionSort');
      const table = document.getElementById('studentTable');
      const tbody = table.getElementsByTagName('tbody')[0];
      const rows = tbody.getElementsByTagName('tr');

      function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const departmentFilter = departmentSort.value;
        const programFilter = programSort.value;
        const sectionFilter = sectionSort.value;

        for (let row of rows) {
          const cells = row.getElementsByTagName('td');
          if (cells.length < 6) continue; // Skip if not a data row

          // Adjusted indices due to added checkbox column
          const studentId = cells[1].textContent.toLowerCase();
          const fullName = cells[2].textContent.toLowerCase();
          const department = cells[3].textContent;
          const program = cells[4].textContent;
          const section = cells[5].textContent;

          const matchesSearch = studentId.includes(searchTerm) || 
                              fullName.includes(searchTerm) ||
                              department.toLowerCase().includes(searchTerm) ||
                              program.toLowerCase().includes(searchTerm) ||
                              section.toLowerCase().includes(searchTerm);

          const matchesDepartment = !departmentFilter || department === departmentFilter;
          const matchesProgram = !programFilter || program === programFilter;
          const matchesSection = !sectionFilter || section === sectionFilter;

          row.style.display = (matchesSearch && matchesDepartment && matchesProgram && matchesSection) ? '' : 'none';
        }
      }

      // Add event listeners
      searchInput.addEventListener('input', filterTable);
      departmentSort.addEventListener('change', filterTable);
      programSort.addEventListener('change', filterTable);
      sectionSort.addEventListener('change', filterTable);

      // Delete button functionality
      tbody.addEventListener('click', function(e) {
        let btn = e.target;
        // If the icon is clicked, get the parent button
        if (btn.tagName === 'I' && btn.parentElement.classList.contains('delete-btn')) {
          btn = btn.parentElement;
        }
        if (btn.classList.contains('delete-btn')) {
          const studentId = btn.getAttribute('data-id');
          // Get the fullname from the same row (2nd cell)
          const row = btn.closest('tr');
          const fullname = row && row.children[1] ? row.children[1].textContent.trim() : '';
          if (confirm('Are you sure you want to delete ' + fullname + '?')) {
            fetch('delete_student.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'student_id=' + encodeURIComponent(studentId)
            })
            .then(response => response.text())
            .then(data => {
              if (data.trim() === 'success') {
                btn.closest('tr').remove();
              } else {
                alert('Failed to delete student.');
              }
            });
          }
        }
      });

      // Multiple delete functionality
      const selectAll = document.getElementById('selectAll');
      const deleteSelected = document.getElementById('deleteSelected');
      function getSelectedIds() {
        return Array.from(document.querySelectorAll('.select-student:checked')).map(cb => cb.value);
      }
      selectAll.addEventListener('change', function() {
        document.querySelectorAll('.select-student').forEach(cb => cb.checked = selectAll.checked);
      });
      tbody.addEventListener('change', function() {
        const all = document.querySelectorAll('.select-student');
        const checked = document.querySelectorAll('.select-student:checked');
        selectAll.checked = all.length === checked.length;
      });
      deleteSelected.addEventListener('click', function() {
        const ids = getSelectedIds();
        if (ids.length === 0) {
          alert('Please select at least one student to delete.');
          return;
        }
        if (confirm('Are you sure you want to delete the selected students?')) {
          fetch('delete_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'student_id=' + encodeURIComponent(ids.join(','))
          })
          .then(response => response.text())
          .then(data => {
            if (data.trim() === 'success') {
              // Remove deleted rows
              document.querySelectorAll('.select-student:checked').forEach(cb => cb.closest('tr').remove());
              selectAll.checked = false;
              document.getElementById('notificationMessage').textContent = 'Selected students deleted successfully.';
              document.getElementById('notificationModal').style.display = 'block';
            } else {
              alert('Failed to delete selected students.');
            }
          });
        }
      });
    });

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
  </script>
</body>
</html>
