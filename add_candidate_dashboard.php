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
      background: rgba(0, 0, 0, 0.6);
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

    #logoutModal {
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
    }

    .modal-overlayy {
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
    }

    .modal-boxx {
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

    .modal-boxx h2 {
      margin-bottom: 10px;
    }

    .modal-boxx p {
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

    .cancel-btnn {
      background-color: #aaa;
      color: #2d0808;
    }

    .cancel-btnn:hover {
      background-color: #666;
      color: #fff;
    }


    .add-icon-container {
      text-align: right;
      margin: 10px 25px;
      font-size: 20px;
    }

    .add-icon-btn {
      background: none;
      border: none;
      cursor: pointer;
      color: #FDDE54;
      font-size: 24px;
    }

    .modal-container {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 10000;
    }

    .modal-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
    }

    .modal-box {
      position: relative;
      margin: 5% auto;
      background: #fff;
      padding: 20px;
      width: 90%;
      max-width: 500px;
      z-index: 10001;
      border-radius: 10px;
    }

    .position-group {
      margin-bottom: 15px;
    }

    .position-input-row {
      display: flex;
      align-items: center;
      gap: 5px;
      margin-bottom: 5px;
      margin-bottom: 15px;
    }




    .modal-input {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      box-sizing: border-box;
    }



    #addCandidateModal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
    }

    .modal-content {
      background: #2d0808;
      color: #FDDE54;
      width: 400px;
      max-height: 80vh;
      overflow-y: auto;
      padding: 25px;
      border-radius: 12px;
      position: relative;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.6);
      margin: 0;
    }

    .modal-content h3 {
      text-align: center;
      margin-bottom: 20px;
    }

    .input-group {
      margin-bottom: 15px;
      position: relative;
    }

    .input-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .input-group input[type="text"],
    .input-group input[type="file"] {
      width: 100%;
      padding: 8px;
      background: #662d2d;
      border: none;
      border-radius: 5px;
      color: #FDDE54;
      margin-bottom: 8px;
    }

    .input-group input[type="file"] {
      background: #4a1010;
      padding: 5px;
      cursor: pointer;
    }

    .input-group input[type="file"]::-webkit-file-upload-button {
      background: #FDDE54;
      color: #2d0808;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-right: 10px;
    }

    .input-group input[type="file"]::-webkit-file-upload-button:hover {
      background: #ffd700;
    }

    .add-field {
      background: #662d2d;
      color: #FDDE54;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      margin: 10px 0;
      font-weight: bold;
    }

    .add-field:hover {
      background: #4a1010;
    }

    #nameFieldsContainer .input-group {
      border: 1px solid #662d2d;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 15px;
    }

    #nameFieldsContainer label {
      color: #FDDE54;
      margin-bottom: 8px;
    }

    .modal-content {
      max-width: 500px;
      width: 90%;
    }

    .save-btn {
      background: #FDDE54;
      color: #2d0808;
      font-weight: bold;
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .save-btn:hover {
      background: #ff4d4d;
      color: #fff;
    }

    .cancel-btn {
      background: #aaa;
      color: #2d0808;
      font-weight: bold;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      margin-top: 5px;
    }

    .cancel-btn:hover {
      background: #666;
      color: #fff;
    }

    .position-container {
      margin: 20px;
      background: #4a1010;
      border-radius: 10px;
      color: #fafaf9;
      overflow: hidden;
    }

    .position-header {
      background: #FDDE54;
      padding: 8px 20px;
      display: flex;
      align-items: center;
      position: relative;
      min-height: 40px;
    }

    .candidate-list {
      padding: 10px;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .candidate-list div {

      padding: 8px;
      border-radius: 6px;
    }

    .name-input {
      width: 100%;
      margin-bottom: 10px;
      padding: 8px;
      box-sizing: border-box;
    }

    .modal-content {
      max-height: 80vh;
      overflow-y: auto;
    }

    .modal-content::-webkit-scrollbar {
      width: 8px;
    }

    .modal-content::-webkit-scrollbar-track {
      background: #2d0808;
    }

    .modal-content::-webkit-scrollbar-thumb {
      background: #FDDE54;
      border-radius: 4px;
    }

    .positions-container {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      max-width: 1300px;
      margin: 0 auto;
    }

    .position-block {
      background: #4a1010;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .position-id {
      position: absolute;
      left: 20px;
      color: #7a3535;
      font-weight: bold;
      font-size: 1em;
    }

    .position-title {
      color: #7a3535;
      font-size: 1.2em;
      font-weight: bold;
      width: 100%;
      text-align: center;
    }

    .candidates-section {
      padding: 10px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .candidate-card {
      background: #662d2d;
      border-radius: 8px;
      overflow: hidden;
      transition: transform 0.2s;
      display: flex;
      padding: 8px;
      gap: 12px;
      align-items: center;
    }

    .candidate-card:hover {
      transform: translateX(10px);
      background: #7a3535;
    }

    .candidate-image {
      width: 70px;
      height: 70px;
      min-width: 60px;
      overflow: hidden;
      background: #4a1010;
      border-radius: 6px;
    }

    .candidate-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .no-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #FDDE54;
      font-style: italic;
      font-size: 0.7em;
    }

    .candidate-info {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      gap: 2px;
    }

    .candidate-info h2 {
      color: #FFF;
      margin: 0;
      margin-bottom: 6px;
      font-size: 1.2em;
      font-weight: bold;
      line-height: 1;
    }

    .candidate-info p {
      margin: 0;
      color: #fff;
      font-size: 0.9em;
      line-height: 1.2;
    }

    .candidate-info .program {
      margin-bottom: 2px;
    }

    .no-positions {
      text-align: center;
      color: #FDDE54;
      font-size: 1.2em;
      padding: 40px;
      background: #4a1010;
      border-radius: 10px;
    }

    .year {
      color: #FDDE54 !important;

    }

    .program {
      color: #FDDE54 !important;
    }
  </style>
</head>

<body>
  <div class="sidebar" id="sidebar">
    <div>
      <div class="menu">
        <div class="menu-title">Admin Dashboard</div>
        <div class="menu-item" onclick="window.location.href='admin_dashboard.php'"><i
            class="fas fa-chart-line"></i><span class="label1">Analytics</span></div>
        <div class="menu-item" onclick="window.location.href='standing.php'"><i class="fas fa-trophy"></i><span
            class="label1">Standings</span></div>
        <div class="menu-item" onclick="window.location.href='student_list.php'"><i
            class="fas fa-address-card"></i><span class="label1">Student List</span></div>
        <div class="menu-item"><i class="fas fa-users"></i><span class="label">Add Candidates</span></div>
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

    <!-- Plus Icon Trigger -->
    <div class="add-icon-container">
      <i class="fas fa-plus-circle" onclick="openAddModal()"></i>
    </div>


    <!-- Modal -->
    <div id="addCandidateModal" style="display: none;">
      <div class="modal-content">
        <h3>Add Candidates</h3>
        <form method="post" action="save_position.php" id="candidateForm">
          <div class="input-group">
            <label>Position</label>
            <input type="text" id="positionInput" name="position_id[]" placeholder="Position ID" required>
            <input type="text" id="positionInput" name="position[]" placeholder="Role" required>
          </div>
          <div id="nameFieldsContainer">
            <div class="input-group">
              <label>1.</label>
              <input type="text" name="name[]" placeholder="Name" required>
              <input type="text" name="year[]" placeholder="Year" required>
              <input type="text" name="program[]" placeholder="Program" required>
              <input type="file" name="image[]" accept="image/*" required>
            </div>
          </div>
          <button type="button" class="add-field" onclick="addNameField()">Add Candidate</button>
          <button type="submit" class="save-btn">Save</button>
          <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Display Container -->
    <div class="positions-container">
      <?php
      include 'db.php';

      // Get unique positions
      $query = "SELECT DISTINCT position_id, position FROM candidate_positions ORDER BY position_id";
      $result = $conn->query($query);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $position_id = $row['position_id'];
          $position = $row['position'];
          ?>
          <div class="position-block">
            <div class="position-header">
              <span class="position-id"><?php echo htmlspecialchars($position_id); ?></span>
              <span class="position-title"><?php echo htmlspecialchars($position); ?></span>
              <i class="fas fa-edit" style="position: absolute; right: 20px; color: #7a3535; cursor: pointer;"
                onclick="openEditModal('<?php echo htmlspecialchars($position_id); ?>', '<?php echo htmlspecialchars($position); ?>')"></i>
            </div>
            <div class="candidates-section">
              <?php
              // Get candidates for this position
              $candidates_query = "SELECT * FROM candidate_positions WHERE position_id = ? ORDER BY name";
              $stmt = $conn->prepare($candidates_query);
              $stmt->bind_param("s", $position_id);
              $stmt->execute();
              $candidates_result = $stmt->get_result();

              while ($candidate = $candidates_result->fetch_assoc()) {
                ?>
                <div class="candidate-card">
                  <div class="candidate-image">
                    <?php if (!empty($candidate['image'])): ?>
                      <img src="<?php echo htmlspecialchars($candidate['image']); ?>"
                        alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                    <?php else: ?>
                      <div class="no-image">No Image</div>
                    <?php endif; ?>
                  </div>
                  <div class="candidate-info">
                    <h2><?php echo htmlspecialchars($candidate['name']); ?></h2>
                    <p class="program"><?php echo htmlspecialchars($candidate['program']); ?></p>
                    <p class="year"><?php echo htmlspecialchars($candidate['year']); ?> Year</p>
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
      } else {
        echo '<div class="no-positions">No positions added yet</div>';
      }
      ?>
    </div>

  </div>
  <div id="logoutModal" style="display: none;">
    <div class="modal-overlayy"></div>
    <div class="modal-boxx">
      <h2>Confirm Logout</h2>
      <p>Are you sure you want to log out?</p>
      <div class="modal-actions">
        <button class="btn cancel-btnn" onclick="closeLogoutModal()">Cancel</button>
        <button class="btn confirm-btn" onclick="confirmLogout()">Logout</button>
      </div>
    </div>
  </div>

  <script>

    //Sidebar
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('main');
      const barChartCanvas = document.getElementById('barChart');

      sidebar.classList.toggle('collapsed');
      main.classList.toggle('collapsed');

    }


    function scrollToSection(id) {
      const element = document.getElementById(id);
      if (element) {
        window.scrollTo({ top: element.offsetTop, behavior: 'smooth' });
      }

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


    //new


    let nameCount = 1;

    function openAddModal() {
      const modal = document.getElementById("addCandidateModal");
      modal.style.display = "flex";
      document.getElementById("nameFieldsContainer").innerHTML = `
    <div class="input-group">
      <label>1.</label>
      <input type="text" name="name[]" placeholder="Name" required>
      <input type="text" name="year[]" placeholder="Year" required>
      <input type="text" name="program[]" placeholder="Program" required>
      <input type="file" name="image[]" accept="image/*" required>
      <button type="button" class="remove-field" onclick="removeNameField(this)" style="position:absolute;top:5px;right:5px;background:none;border:none;color:#FDDE54;font-size:18px;cursor:pointer;">&times;</button>
    </div>
  `;
      nameCount = 1;
    }

    function closeModal() {
      document.getElementById('addCandidateModal').style.display = 'none';
      // Reset form
      document.getElementById('candidateForm').reset();
      document.getElementById("nameFieldsContainer").innerHTML = `
    <div class="input-group">
      <label>1.</label>
      <input type="text" name="name[]" placeholder="Name" required>
      <input type="text" name="year[]" placeholder="Year" required>
      <input type="text" name="program[]" placeholder="Program" required>
      <input type="file" name="image[]" accept="image/*" required>
      <button type="button" class="remove-field" onclick="removeNameField(this)" style="position:absolute;top:5px;right:5px;background:none;border:none;color:#FDDE54;font-size:18px;cursor:pointer;">&times;</button>
    </div>
  `;
      nameCount = 1;
    }

    function addNameField() {
      nameCount++;
      const container = document.getElementById("nameFieldsContainer");
      const group = document.createElement("div");
      group.classList.add("input-group");
      group.innerHTML = `
    <label>${nameCount}.</label>
    <input type="text" name="name[]" placeholder="Name" required>
    <input type="text" name="year[]" placeholder="Year" required>
    <input type="text" name="program[]" placeholder="Program" required>
    <input type="file" name="image[]" accept="image/*" required>
    <button type="button" class="remove-field" onclick="removeNameField(this)" style="position:absolute;top:5px;right:5px;background:none;border:none;color:#FDDE54;font-size:18px;cursor:pointer;">&times;</button>
  `;
      container.appendChild(group);
      updateCandidateLabels();
    }

    function removeNameField(btn) {
      const container = document.getElementById("nameFieldsContainer");
      if (container.children.length > 1) {
        btn.parentElement.remove();
        nameCount = container.children.length;
        updateCandidateLabels();
      }
    }

    function updateCandidateLabels() {
      const groups = document.querySelectorAll('#nameFieldsContainer .input-group label');
      groups.forEach((label, idx) => {
        label.textContent = (idx + 1) + '.';
      });
    }

    function openEditModal(positionId, position) {
      const modal = document.getElementById("addCandidateModal");
      modal.style.display = "flex";

      // Update form title
      modal.querySelector('h3').textContent = 'Edit Position';

      // Fill in position details
      document.querySelector('input[name="position_id[]"]').value = positionId;
      document.querySelector('input[name="position[]"]').value = position;

      // Fetch candidates for this position
      fetch(`get_candidates.php?position_id=${positionId}`)
        .then(response => response.json())
        .then(candidates => {
          const container = document.getElementById("nameFieldsContainer");
          container.innerHTML = ''; // Clear existing fields

          candidates.forEach((candidate, index) => {
            const group = document.createElement("div");
            group.classList.add("input-group");
            group.innerHTML = `
          <label>${index + 1}.</label>
          <input type="text" name="name[]" placeholder="Name" value="${candidate.name}" required>
          <input type="text" name="year[]" placeholder="Year" value="${candidate.year}" required>
          <input type="text" name="program[]" placeholder="Program" value="${candidate.program}" required>
          <input type="file" name="image[]" accept="image/*">
          ${candidate.image ? `<small>Current image: ${candidate.image}</small>` : ''}
          <input type="hidden" name="existing_image[]" value="${candidate.image || ''}">
        `;
            container.appendChild(group);
          });
          nameCount = candidates.length;
        });
    }

    function validateAndSubmit(event) {
      event.preventDefault();

      const form = document.getElementById('candidateForm');
      const formData = new FormData();

      // Add position data
      const positionId = document.querySelector('input[name="position_id[]"]').value;
      const position = document.querySelector('input[name="position[]"]').value;

      // Append position data
      formData.append('position_id', positionId);
      formData.append('position', position);

      // Get all input arrays
      const names = Array.from(document.getElementsByName('name[]')).map(input => input.value);
      const years = Array.from(document.getElementsByName('year[]')).map(input => input.value);
      const programs = Array.from(document.getElementsByName('program[]')).map(input => input.value);
      const images = Array.from(document.getElementsByName('image[]'));
      const existingImages = Array.from(document.getElementsByName('existing_image[]')).map(input => input.value);

      // Add all data to FormData
      names.forEach((name, index) => {
        formData.append('name[]', name);
        formData.append('year[]', years[index]);
        formData.append('program[]', programs[index]);
        formData.append('existing_image[]', existingImages[index] || '');
        if (images[index].files[0]) {
          formData.append('image[]', images[index].files[0]);
        } else {
          formData.append('image[]', '');
        }
      });

      // Send form data using fetch
      fetch('save_position.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          console.log('Server response:', data);
          closeModal();
          window.location.reload();
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Update the form submission
    document.getElementById('candidateForm').addEventListener('submit', validateAndSubmit);







  </script>
</body>

</html>