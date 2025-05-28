<?php
// Include the auto-generated voting form data
require_once 'voting_form_data.php';

// Check if user is logged in and has scanned QR
session_start();
if (!isset($_SESSION['student_id']) || !isset($_SESSION['has_scanned'])) {
    header('Location: scanner.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVSU Voting Form</title>
    <link rel="icon" type="image/png" href="Images/EvsuLogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            padding: 20px;
        }

        .voting-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            text-align: center;
            color: #FDDE54;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .positions-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .position-section {
            background: #4a1010;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .position-title {
            color: #FDDE54;
            font-size: 1.5em;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 10px;
        }

        .candidate-card {
            background: #662d2d;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.2s;
            cursor: pointer;
            position: relative;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
            background: #7a3535;
        }

        .candidate-card.selected {
            border: 2px solid #FDDE54;
            background: #7a3535;
        }

        .candidate-image {
            width: 150px;
            height: 150px;
            margin: 0 auto 10px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #FDDE54;
        }

        .candidate-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .candidate-name {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .candidate-info {
            color: #FDDE54;
            font-size: 0.9em;
            margin-bottom: 5px;
        }

        .submit-btn {
            background: #FDDE54;
            color: #2d0808;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            display: block;
            margin: 20px auto;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #ffd700;
            transform: translateY(-2px);
        }

        .submit-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
        }

        .radio-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .selected-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #FDDE54;
            font-size: 1.2em;
            display: none;
        }

        .candidate-card.selected .selected-indicator {
            display: block;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
        }

        .modal-content {
            background: #4a1010;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            position: relative;
            color: #fff;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FDDE54;
        }

        .modal-title {
            color: #FDDE54;
            font-size: 1.5em;
            margin: 0;
        }

        .close-modal {
            color: #FDDE54;
            font-size: 1.5em;
            cursor: pointer;
            background: none;
            border: none;
        }

        .vote-summary {
            margin-bottom: 20px;
        }

        .vote-summary-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #662d2d;
            border-radius: 5px;
        }

        .position-name {
            color: #FDDE54;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .candidate-selected {
            color: #fff;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .confirm-btn {
            background: #FDDE54;
            color: #2d0808;
        }

        .cancel-btn {
            background: #662d2d;
            color: #FDDE54;
        }
    </style>
</head>
<body>
    <div class="voting-container">
        <h1 class="page-title">EVSU Student Council Elections</h1>
        <form id="votingForm" action="submit_vote.php" method="POST">
            <div class="positions-container">
                <?php foreach ($positions as $position): ?>
                <div class="position-section">
                    <h2 class="position-title"><?php echo htmlspecialchars($position['position']); ?></h2>
                    <div class="candidates-grid">
                        <?php foreach ($position['candidates'] as $candidate): ?>
                        <label class="candidate-card">
                            <input type="radio" 
                                   name="vote[<?php echo $position['position_id']; ?>]" 
                                   value="<?php echo $candidate['candidate_id']; ?>" 
                                   class="radio-input" 
                                   required>
                            <div class="candidate-image">
                                <img src="<?php echo htmlspecialchars($candidate['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                            </div>
                            <h3 class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></h3>
                            <p class="candidate-info"><?php echo htmlspecialchars($candidate['program']); ?></p>
                            <p class="candidate-info"><?php echo htmlspecialchars($candidate['year']); ?> Year</p>
                            <i class="fas fa-check-circle selected-indicator"></i>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="submit-btn" id="submitVote">Submit Vote</button>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirm Your Votes</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="vote-summary" id="voteSummary">
                <!-- Vote summary will be inserted here -->
            </div>
            <div class="modal-buttons">
                <button class="modal-btn cancel-btn" id="cancelVote">Cancel</button>
                <button class="modal-btn confirm-btn" id="confirmVote">Confirm Votes</button>
            </div>
        </div>
    </div>

    <script>
        // Handle candidate selection
        document.querySelectorAll('.candidate-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from other cards in the same position section
                const positionSection = this.closest('.position-section');
                positionSection.querySelectorAll('.candidate-card').forEach(c => {
                    c.classList.remove('selected');
                });
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Check the radio input
                const radio = this.querySelector('.radio-input');
                radio.checked = true;
            });
        });

        // Modal elements
        const modal = document.getElementById('confirmationModal');
        const closeModal = document.querySelector('.close-modal');
        const cancelVote = document.getElementById('cancelVote');
        const confirmVote = document.getElementById('confirmVote');
        const voteSummary = document.getElementById('voteSummary');

        // Function to generate vote summary
        function generateVoteSummary() {
            voteSummary.innerHTML = '';
            const positions = document.querySelectorAll('.position-section');
            let allVoted = true;
            
            positions.forEach(position => {
                const positionTitle = position.querySelector('.position-title').textContent;
                const selectedCandidate = position.querySelector('input[type="radio"]:checked');
                
                if (!selectedCandidate) {
                    allVoted = false;
                    return;
                }

                const candidateCard = selectedCandidate.closest('.candidate-card');
                const candidateName = candidateCard.querySelector('.candidate-name').textContent;
                const candidateProgram = candidateCard.querySelector('.candidate-info').textContent;

                const summaryItem = document.createElement('div');
                summaryItem.className = 'vote-summary-item';
                summaryItem.innerHTML = `
                    <div class="position-name">${positionTitle}</div>
                    <div class="candidate-selected">${candidateName} - ${candidateProgram}</div>
                `;
                voteSummary.appendChild(summaryItem);
            });

            return allVoted;
        }

        // Form submission handling
        document.getElementById('votingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const allVoted = generateVoteSummary();
            
            if (!allVoted) {
                alert('Please select a candidate for each position before submitting.');
                return;
            }
            
            // Show the confirmation modal
            modal.style.display = 'block';
        });

        // Close modal when clicking the close button or cancel button
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        cancelVote.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Confirm vote and submit form
        confirmVote.addEventListener('click', () => {
            document.getElementById('votingForm').submit();
        });

        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html> 