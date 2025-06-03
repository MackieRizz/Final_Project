<?php
session_start();
include 'db.php';

// Check if admin is logged in and has a valid session
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
      z-index: 1000;
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
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
    }

    .modal-overlayy {
      position: absolute;
      top: 0;
      left: 0;
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
      display: flex;
      justify-content: flex-end;
      gap: 15px;
    }

    .save-voting-form-btn {
      background: #FDDE54;
      color: #2d0808;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .save-voting-form-btn:hover {
      background: #ffd700;
      transform: translateY(-2px);
    }

    .save-voting-form-btn i {
      font-size: 16px;
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

    .input-group textarea[name="background[]"] {
      width: 100%;
      padding: 8px;
      background: #662d2d;
      border: none;
      border-radius: 5px;
      color: #FDDE54;
      margin-bottom: 8px;
      font-size: 1em;
      resize: vertical;
      min-height: 38px;
      box-sizing: border-box;
      transition: background 0.2s, color 0.2s;
      autocomplete="off";
      autocorrect="off";
      autocapitalize="off";
      spellcheck="false";
    }

    /* Remove the clear (X) button from textarea in Microsoft Edge/IE */
    .input-group textarea[name="background[]"]::-ms-clear {
      display: none;
    }
    /* Remove the clear (X) button from textarea in Chrome (if any) */
    .input-group textarea[name="background[]"]::-webkit-search-cancel-button {
      display: none;
    }
    /* Remove the clear (X) button from all textareas (universal fallback) */
    .input-group textarea[name="background[]"]::-webkit-clear-button {
      display: none;
    }
    .input-group textarea[name="background[]"]::-webkit-input-clear-button {
      display: none;
    }
    .input-group textarea[name="background[]"]::-webkit-input-placeholder {
      color: #FDDE54;
    }

    .input-group textarea[name="background[]"]:focus {
      outline: none;
      background: #4a1010;
      color: #fff;
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

    .position-header .icons {
      position: absolute;
      right: 20px;
      display: flex;
      gap: 10px;
    }

    .position-header .icons i {
      color: #7a3535;
      cursor: pointer;
    }

    .position-header .icons i:hover {
      color: #4a1010;
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
      position: relative;
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
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .candidate-image:hover {
      transform: scale(1.05);
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

    .candidate-info {
      margin-top: 4px;
    }

    .candidate-card .delete-icon {
      position: absolute;
      right: 15px;
      color: #FDDE54;
      cursor: pointer;
    }

    .candidate-card .delete-icon:hover {
      color: #ff4d4d;
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

    /* Scanner Settings Modal */
    .scanner-settings-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 2000;
      justify-content: center;
      align-items: center;
    }

    .scanner-settings-container {
      background: #2d0808;
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 500px;
      color: #FDDE54;
      border: 1px solid rgba(253, 222, 84, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .scanner-settings-container h3 {
      margin-bottom: 20px;
      text-align: center;
      font-size: 1.5em;
    }

    .datetime-inputs {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 20px;
    }

    .datetime-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .datetime-group label {
      font-weight: bold;
      color: #FDDE54;
    }

    .datetime-group input {
      padding: 10px;
      border: 2px solid #FDDE54;
      background: rgba(253, 222, 84, 0.1);
      border-radius: 8px;
      color: #FDDE54;
      font-size: 1em;
    }

    .scanner-settings-container button {
      width: 100%;
      padding: 12px;
      background: #FDDE54;
      border: none;
      border-radius: 8px;
      color: #2d0808;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .scanner-settings-container button:hover {
      background: #fff;
      transform: translateY(-2px);
    }

    .scanner-icon-btn {
      position: absolute;
      bottom: 60px;
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

    .scanner-icon-btn:hover {
      background: rgba(253, 222, 84, 0.1);
      box-shadow: 0 0 10px rgba(253, 222, 84, 0.2);
      transform: scale(1.1);
    }

    .scanner-icon-btn i {
      font-size: 14px;
      color: #FDDE54;
    }

    /* View Scanner Settings Modal */
    .view-scanner-settings-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      z-index: 2000;
      justify-content: center;
      align-items: center;
    }

    .view-settings-container {
      background: #2d0808;
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 500px;
      color: #FDDE54;
      border: 1px solid rgba(253, 222, 84, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    .view-settings-container h3 {
      margin-bottom: 20px;
      text-align: center;
      font-size: 1.5em;
    }

    .settings-info {
      margin: 20px 0;
      padding: 15px;
      background: rgba(253, 222, 84, 0.1);
      border-radius: 8px;
    }

    .settings-info p {
      margin: 10px 0;
      font-size: 1.1em;
    }

    .edit-settings-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: transparent;
      border: none;
      color: #FDDE54;
      cursor: pointer;
      font-size: 1.2em;
    }

    .edit-settings-btn:hover {
      color: #fff;
    }

    .close-view-settings-btn {
      width: 100%;
      padding: 12px;
      background: #FDDE54;
      border: none;
      border-radius: 8px;
      color: #2d0808;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 20px;
    }

    .close-view-settings-btn:hover {
      background: #fff;
      transform: translateY(-2px);
    }

    .scanner-settings-btn:hover {
      background: rgba(253, 222, 84, 0.1);
      box-shadow: 0 0 10px rgba(253, 222, 84, 0.2);
      transform: scale(1.1);
    }

    .fa-plus-circle {
      color: #FDDE54;
      transition: transform 0.3s ease;
    }

    .fa-plus-circle:hover {
      transform: scale(1.1);
    }

    /* Image Modal Styles */
    .image-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      padding: 20px;
      box-sizing: border-box;
    }

    .modal-content {
      position: relative;
      max-width: 90%;
      max-height: 90vh;
      margin: auto;
      display: block;
      top: 50%;
      transform: translateY(-50%);
      object-fit: contain;
      width: auto;
      height: auto;
    }

    .modal-close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #FDDE54;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
      z-index: 1001;
    }

    .modal-close:hover {
      color: #C46B02;
    }

    .modal-caption {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      color: #FDDE54;
      font-size: 1.2em;
      text-align: center;
      width: 100%;
      padding: 10px;
      background: rgba(0, 0, 0, 0.7);
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
        <div class="menu-item" onclick="window.location.href='standing.php'"><i class="fas fa-trophy"></i><span class="label1">Standings</span></div>
        <div class="menu-item" onclick="window.location.href='student_list.php'"><i
            class="fas fa-address-card"></i><span class="label1">Student List</span></div>
        <div class="menu-item"><i class="fas fa-users"></i><span class="label">Add Candidates</span></div>
        <div class="menu-item" onclick="openVotingFormPreview()"><i class="fas fa-eye"></i><span class="label1">View Voting Form</span></div>
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

    <!-- Plus Icon Trigger -->
    <div class="add-icon-container">
      <button class="save-voting-form-btn" onclick="saveVotingForm()">
        <i class="fas fa-save"></i>
        Save Voting Form
      </button>
      <button class="delete-all-btn" onclick="confirmDeleteAll()" style="background: #ff4d4d; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
        <i class="fas fa-trash"></i>
        Delete All
      </button>
      <button class="scanner-settings-btn" onclick="openScannerSettings()" style="background: transparent; border: 1px solid #FDDE54; color: #FDDE54; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
        <i class="fas fa-qrcode"></i>
      </button>
      <i class="fas fa-plus-circle" onclick="openAddModal()" style="font-size: 40px; cursor: pointer;"></i>
    </div>


    <!-- Modal -->
    <div id="addCandidateModal" style="display: none;">
      <div class="modal-content">
        <h3>Add Candidates</h3>
        <form method="post" action="save_position.php" id="candidateForm" enctype="multipart/form-data">
          <div class="input-group">
            <label>Position</label>
            <input type="text" id="positionInput" name="position_id" placeholder="Position ID" required>
            <input type="text" id="positionInput" name="position" placeholder="Role" required>
          </div>
          <div id="nameFieldsContainer">
            <div class="input-group">
              <label>1.</label>
              <input type="text" name="name[]" placeholder="Name" required>
              <input type="text" name="year[]" placeholder="Year" required>
              <input type="text" name="program[]" placeholder="Program" required>
              <input type="file" name="image[]" accept="image/*" required>
              <textarea name="background[]" placeholder="Background and Achievements" rows="2" style="width:100%;margin-bottom:8px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required></textarea>
              <input type="hidden" name="is_new[]" value="1">
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
              <div class="icons">
                <i class="fas fa-edit"
                  onclick="openEditModal('<?php echo htmlspecialchars($position_id); ?>', '<?php echo htmlspecialchars($position); ?>')"></i>
                <i class="fas fa-minus-circle"
                  onclick="deletePosition('<?php echo htmlspecialchars($position_id); ?>')"></i>
              </div>
            </div>
            <div class="candidates-section">
              <?php
              // Get candidates for this position
              $candidates_query = "SELECT * FROM candidate_positions WHERE position_id = ? ORDER BY candidate_id";
              $stmt = $conn->prepare($candidates_query);
              $stmt->bind_param("s", $position_id);
              $stmt->execute();
              $candidates_result = $stmt->get_result();

              while ($candidate = $candidates_result->fetch_assoc()) {
                ?>
                <div class="candidate-card">
                  <div class="candidate-image" onclick="openImageModal(this)">
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
                    <?php if (!empty($candidate['background'])): ?>
                      <p class="candidate-info" style="color:#FDDE54;background:#4a1010;padding:6px 10px;border-radius:6px;margin-top:4px;max-width:400px;white-space:pre-line;"><strong>Background:</strong> <?php echo nl2br(htmlspecialchars($candidate['background'])); ?></p>
                    <?php endif; ?>
                    <p class="candidate-number">Candidate #<?php echo htmlspecialchars($candidate['candidate_id']); ?></p>
                  </div>
                  <i class="fas fa-trash delete-icon"
                    onclick="deleteCandidate('<?php echo htmlspecialchars($candidate['id']); ?>', '<?php echo htmlspecialchars($candidate['name']); ?>', '<?php echo htmlspecialchars($candidate['position_id']); ?>')"></i>
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

  <!-- Scanner Settings Modal -->
  <div id="scannerSettingsModal" class="scanner-settings-modal">
    <div class="scanner-settings-container">
      <h3>Set Scanner Availability</h3>
      <div class="datetime-inputs">
        <div class="datetime-group">
          <label>Start Date & Time:</label>
          <input type="datetime-local" id="startDateTime" required>
        </div>
        <div class="datetime-group">
          <label>End Date & Time:</label>
          <input type="datetime-local" id="endDateTime" required>
        </div>
      </div>
      <button onclick="saveSettingsAndForm()">Set Scan & Save Voting Form</button>
      <button onclick="closeScannerSettings()" style="background: #666;">Cancel</button>
    </div>
  </div>

  <!-- View Scanner Settings Modal -->
  <div id="viewScannerSettingsModal" class="view-scanner-settings-modal">
    <div class="view-settings-container">
      <h3>Scanner Settings</h3>
      <button class="edit-settings-btn" onclick="editScannerSettings()">
        <i class="fas fa-edit"></i>
      </button>
      <div class="settings-info">
        <p><strong>Start:</strong> <span id="viewStartDateTime">Not set</span></p>
        <p><strong>End:</strong> <span id="viewEndDateTime">Not set</span></p>
      </div>
      <button class="close-view-settings-btn" onclick="closeViewSettings()">Close</button>
    </div>
  </div>

  <!-- Voting Form Preview Modal -->
  <div id="votingFormPreviewModal" class="scanner-settings-modal">
    <div class="scanner-settings-container" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
      <h3>Voting Form Preview</h3>
      <div id="votingFormPreview" style="background: #fff; padding: 20px; border-radius: 10px; margin-top: 20px;">
        <!-- Content will be loaded dynamically -->
      </div>
      <button onclick="closeVotingFormPreview()" style="background: #666; margin-top: 20px;">Close</button>
    </div>
  </div>

  <!-- Image Modal -->
  <div id="imageModal" class="image-modal">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="modalCaption" class="modal-caption"></div>
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

      // Show the Add Candidate button
      document.querySelector('.add-field').style.display = 'block';

      // Update form title
      modal.querySelector('h3').textContent = 'Add Candidates';

      document.getElementById("nameFieldsContainer").innerHTML = `
    <div class="input-group">
      <label>1.</label>
      <input type="text" name="name[]" placeholder="Name" required>
      <input type="text" name="year[]" placeholder="Year" required>
      <input type="text" name="program[]" placeholder="Program" required>
      <input type="file" name="image[]" accept="image/*" required>
      <textarea name="background[]" placeholder="Background and Achievements" rows="2" style="width:100%;margin-bottom:8px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required></textarea>
      <input type="hidden" name="is_new[]" value="1">
    </div>
  `;
      nameCount = 1;

      // Clear position inputs
      document.querySelector('input[name="position_id"]').value = '';
      document.querySelector('input[name="position"]').value = '';
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
      <textarea name="background[]" placeholder="Background and Achievements" rows="2" style="width:100%;margin-bottom:8px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required></textarea>
      <input type="hidden" name="is_new[]" value="1">
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
    <textarea name="background[]" placeholder="Background and Achievements" rows="2" style="width:100%;margin-bottom:8px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required></textarea>
    <input type="hidden" name="is_new[]" value="1">
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
      document.querySelector('input[name="position_id"]').value = positionId;
      document.querySelector('input[name="position"]').value = position;

      // Hide the Add Candidate button when editing
      document.querySelector('.add-field').style.display = 'none';

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
          <textarea name="background[]" placeholder="Background and Achievements" rows="2" style="width:100%;margin-bottom:8px;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">${candidate.background ? candidate.background : ''}</textarea>
          <input type="hidden" name="existing_image[]" value="${candidate.image || ''}">
          <input type="hidden" name="candidate_id[]" value="${candidate.id}">
          <input type="hidden" name="is_new[]" value="0">
          <input type="hidden" name="position_id" value="${positionId}">
          <button type="button" class="remove-field-btn" onclick="removeNameField(this)" ${candidates.length === 1 ? 'style=\"display:none;\"' : ''}>×</button>

        `;
            container.appendChild(group);
          });
          nameCount = candidates.length;
        });
    }

    function validateAndSubmit(event) {
      event.preventDefault();

      const form = document.getElementById('candidateForm');
      const formData = new FormData(form);

      // Send form data using fetch
      fetch('save_position.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          console.log('Server response:', data);
          if (data.success) {
            closeModal();
            window.location.reload();
          } else {
            alert('Error saving data: ' + data.messages.join('\n'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error saving data. Please try again.');
        });
    }

    // Update the form submission
    document.getElementById('candidateForm').addEventListener('submit', validateAndSubmit);

    function deletePosition(positionId) {
      if (confirm('Are you sure you want to delete this position and all its candidates?')) {
        fetch('delete_position.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'position_id=' + encodeURIComponent(positionId)
        })
          .then(response => response.text())
          .then(data => {
            console.log('Server response:', data);
            window.location.reload();
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error deleting position');
          });
      }
    }

    function deleteCandidate(candidateId, candidateName, positionId) {
      if (confirm('Are you sure you want to delete ' + candidateName + '?')) {
        fetch('delete_candidate.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'candidate_id=' + encodeURIComponent(candidateId) + '&position_id=' + encodeURIComponent(positionId)
        })
          .then(response => response.json())
          .then(data => {
            console.log('Server response:', data);
            if (data.success) {
              window.location.reload();
            } else {
              alert('Error deleting candidate: ' + data.messages.join('\n'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error deleting candidate');
          });
      }
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

    function saveVotingForm() {
      document.getElementById('scannerSettingsModal').style.display = 'flex';
    }

    function openScannerSettings() {
      fetch('get_scanner_settings.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('viewStartDateTime').textContent = 
              new Date(data.settings.start_datetime).toLocaleString();
            document.getElementById('viewEndDateTime').textContent = 
              new Date(data.settings.end_datetime).toLocaleString();
          } else {
            document.getElementById('viewStartDateTime').textContent = 'Not set';
            document.getElementById('viewEndDateTime').textContent = 'Not set';
          }
          document.getElementById('viewScannerSettingsModal').style.display = 'flex';
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load scanner settings.'
          });
        });
    }

    function closeScannerSettings() {
      document.getElementById('scannerSettingsModal').style.display = 'none';
    }

    function saveSettingsAndForm() {
      const startDateTime = document.getElementById('startDateTime').value;
      const endDateTime = document.getElementById('endDateTime').value;

      if (!startDateTime || !endDateTime) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Please set both start and end date/time.'
        });
        return;
      }

      // Close the scanner settings modal immediately
      closeScannerSettings();

      // Show confirmation dialog
      Swal.fire({
        title: 'Save Settings & Voting Form',
        text: 'Are you sure you want to save these settings and update the voting form?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FDDE54',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save all!'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          Swal.fire({
            title: 'Saving...',
            text: 'Please wait while we save your settings.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // Send settings to server
          fetch('save_scanner_settings.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              start_datetime: startDateTime,
              end_datetime: endDateTime
            })
          })
          .then(response => response.json().catch(() => {
            throw new Error('Invalid response from server');
          }))
          .then(data => {
            if (data.success) {
              // If settings are saved, proceed to save voting form
              return fetch('update_voting_form.php', {
                method: 'POST'
              })
              .then(response => response.json().catch(() => {
                throw new Error('Invalid response from voting form update');
              }));
            }
            throw new Error(data.message || 'Failed to save scanner settings');
          })
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                html: `Scanner settings and voting form have been saved successfully.<br><br>
                      <strong>Voting Period:</strong><br>
                      Start: ${new Date(startDateTime).toLocaleString()}<br>
                      End: ${new Date(endDateTime).toLocaleString()}`
              });
              
              // Update the view settings if modal is open
              document.getElementById('viewStartDateTime').textContent = 
                new Date(startDateTime).toLocaleString();
              document.getElementById('viewEndDateTime').textContent = 
                new Date(endDateTime).toLocaleString();
            } else {
              throw new Error(data.message || 'Failed to save voting form');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.message || 'An error occurred while saving.'
            });
          });
        }
      });
    }

    // View Scanner Settings
    document.querySelector('.scanner-icon-btn').addEventListener('click', function(e) {
      e.stopPropagation();
      fetch('get_scanner_settings.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('viewStartDateTime').textContent = 
              new Date(data.settings.start_datetime).toLocaleString();
            document.getElementById('viewEndDateTime').textContent = 
              new Date(data.settings.end_datetime).toLocaleString();
          } else {
            document.getElementById('viewStartDateTime').textContent = 'Not set';
            document.getElementById('viewEndDateTime').textContent = 'Not set';
          }
          document.getElementById('viewScannerSettingsModal').style.display = 'flex';
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load scanner settings.'
          });
        });
    });

    function closeViewSettings() {
      document.getElementById('viewScannerSettingsModal').style.display = 'none';
    }

    function editScannerSettings() {
      closeViewSettings();
      document.getElementById('scannerSettingsModal').style.display = 'flex';
      
      // Load current settings into the form
      fetch('get_scanner_settings.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('startDateTime').value = 
              data.settings.start_datetime.slice(0, 16);
            document.getElementById('endDateTime').value = 
              data.settings.end_datetime.slice(0, 16);
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
      if (event.target.classList.contains('scanner-settings-modal')) {
        closeScannerSettings();
      }
      if (event.target.classList.contains('view-scanner-settings-modal')) {
        closeViewSettings();
      }
    }

    function confirmDeleteAll() {
      Swal.fire({
        title: 'Delete All Candidates',
        text: 'Are you sure you want to delete all candidates? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4d4d',
        cancelButtonColor: '#666',
        confirmButtonText: 'Yes, delete all!'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          Swal.fire({
            title: 'Deleting...',
            text: 'Please wait while we delete all candidates.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // Send delete request to server
          fetch('delete_all_candidates.php', {
            method: 'POST'
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'All candidates have been deleted successfully.'
              }).then(() => {
                window.location.reload();
              });
            } else {
              throw new Error(data.message || 'Failed to delete candidates');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.message || 'An error occurred while deleting candidates.'
            });
          });
        }
      });
    }

    function openVotingFormPreview() {
      const modal = document.getElementById('votingFormPreviewModal');
      modal.style.display = 'flex';
      
      // Show loading state
      const previewDiv = document.getElementById('votingFormPreview');
      previewDiv.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #FDDE54;"></i><p style="margin-top: 10px; color: #FDDE54;">Loading voting form...</p></div>';
      
      // Fetch the voting form preview
      fetch('get_voting_form_preview.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            previewDiv.innerHTML = data.html;
          } else {
            previewDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #FDDE54;">No voting form available yet.</div>';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          previewDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #FDDE54;">Error loading voting form.</div>';
        });
    }

    function closeVotingFormPreview() {
      document.getElementById('votingFormPreviewModal').style.display = 'none';
    }

    // Image Modal Functions
    function openImageModal(element) {
      const modal = document.getElementById("imageModal");
      const modalImg = document.getElementById("modalImage");
      const captionText = document.getElementById("modalCaption");
      const img = element.querySelector('img');
      
      if (img) {
        modal.style.display = "block";
        modalImg.src = img.src;
        captionText.innerHTML = img.alt;
      }
    }

    function closeImageModal() {
      document.getElementById("imageModal").style.display = "none";
    }

    // Close modal when clicking outside the image
    window.onclick = function(event) {
      const modal = document.getElementById("imageModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === "Escape") {
        document.getElementById("imageModal").style.display = "none";
      }
    });

  </script>
</body>

</html>