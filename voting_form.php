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
    font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
}


body {
    background: linear-gradient(135deg, #C46B02 0%, #800000 25%, #7F0404 50%, #4D1414 75%, #000000 100%);
    min-height: 100vh;
    padding: 40px 20px;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="0.5" fill="%23ffffff" opacity="0.05"/><circle cx="75" cy="75" r="0.3" fill="%23ffffff" opacity="0.03"/><circle cx="50" cy="10" r="0.4" fill="%23ffffff" opacity="0.04"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
    z-index: -1;
}

.voting-container {
    max-width: 900px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(224, 224, 224, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 4px 16px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    backdrop-filter: blur(10px);
    position: relative;
}

.voting-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, #C46B02, #800000, #7F0404, #4D1414);
    z-index: 1;
}

.page-title {
    background: linear-gradient(135deg, #C46B02 0%, #800000 25%, #7F0404 50%, #4D1414 75%, #000000 100%);
    color: #FDDE54;
    font-size: 2.25rem;
    font-weight: 300;
    padding: 48px 40px 32px;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    border-bottom: 3px solid #FDDE54;
    position: relative;
    overflow: hidden;
}

.page-title::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(253, 222, 84, 0.1) 50%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

.positions-container {
    display: block;
    padding: 0;
}

.position-section {
    background: #ffffff;
    border-radius: 0;
    padding: 32px;
    box-shadow: none;
    border-bottom: 1px solid #e0e0e0;
    margin: 0;
}

.position-section:last-child {
    border-bottom: none;
}

.position-title {
    color: #1a1a1a;
    font-size: 1.5rem;
    font-weight: 400;
    margin-bottom: 24px;
    text-align: left;
    padding-bottom: 12px;
    border-bottom: 2px solid #800000;
    display: inline-block;
    position: relative;
}

.position-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 40%;
    height: 2px;
    background: #C46B02;
}

.candidates-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    padding: 0;
    margin-top: 24px;
}

.candidate-card {
    background: #ffffff;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
    text-align: left;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.candidate-card:hover {
    background: #fafafa;
    border-color: #800000;
    box-shadow: 0 4px 16px rgba(128, 0, 0, 0.1);
    transform: translateY(-2px);
}

.candidate-card.selected {
    border: 2px solid #800000;
    background: linear-gradient(135deg, #fff8f0 0%, #fff4e6 100%);
    box-shadow: 0 4px 20px rgba(128, 0, 0, 0.15);
}

.candidate-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #e8e8e8;
    flex-shrink: 0;
    margin: 0;
    transition: all 0.3s ease;
    position: relative;
}

.candidate-image:hover {
    width: 120px;
    height: 120px;
    border-radius: 8px; /* Makes it square with rounded corners */
    transform: scale(1.1);
    z-index: 10;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.candidate-card.selected .candidate-image {
    border-color: #C46B02;
    box-shadow: 0 0 0 2px rgba(196, 107, 2, 0.2);
}

.candidate-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.candidate-card:hover .candidate-image img {
    transform: scale(1.05);
}

.candidate-info-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.candidate-name {
    color: #1a1a1a;
    font-size: 1.125rem;
    font-weight: 500;
    margin: 0;
    line-height: 1.3;
}

.candidate-info {
    color: #e9c97a;
    font-size: 0.93rem;
    margin: 0;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.selected-indicator {
    position: absolute;
    top: 50%;
    right: 16px;
    color: #800000;
    font-size: 1.5rem;
    display: none;
    animation: checkmark 0.3s ease;
    transform: translateY(-50%);
    z-index: 2;
}

.candidate-card.selected .selected-indicator {
    display: block;
}

.candidate-card::before {
    content: '';
    width: 20px;
    height: 20px;
    border: 2px solid #dadce0;
    border-radius: 50%;
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: #ffffff;
    transition: all 0.2s ease;
    z-index: 1;
}

.submit-btn {
    background: linear-gradient(135deg, #FDDE54 0%, #ffd700 100%);
    color: #2d0808;
    border: none;
    padding: 16px 32px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    margin: 40px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 16px rgba(253, 222, 84, 0.3);
    position: relative;
    overflow: hidden;
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.submit-btn:hover::before {
    left: 100%;
}

.submit-btn:hover {
    background: linear-gradient(135deg, #ffd700 0%, #FDDE54 100%);
    box-shadow: 0 6px 20px rgba(253, 222, 84, 0.4);
    transform: translateY(-2px);
}

.submit-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(253, 222, 84, 0.3);
}

.submit-btn:disabled {
    background: #e0e0e0;
    color: #999;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Enhanced Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background-color: #2d0808;
    color: #FDDE54;
    margin: 0;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    border: 1px solid #FDDE54;
    width: 80%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #C46B02, #800000, #7F0404);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 32px 32px 20px;
    margin: 0;
    border-bottom: 1px solid #f0f0f0;
}

.modal-title {
    color: #FDDE54;
    font-size: 1.5rem;
    font-weight: 400;
    margin: 0;
}

.close-modal {
    color: #FDDE54;
    background: none;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    transition: all 0.2s ease;
}

.close-modal:hover,
.close-modal:focus {
    color: #fff;
    background: none;
}

.vote-summary {
    padding: 32px;
    margin: 0;
    max-height: 400px;
    overflow-y: auto;
}

.vote-summary-item {
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
    border-radius: 12px;
    border-left: 4px solid #800000;
    transition: all 0.2s ease;
}

.vote-summary-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.vote-summary-item:last-child {
    margin-bottom: 0;
}

.position-name {
    color: #800000;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.candidate-selected {
    color: #1a1a1a;
    font-size: 1.1rem;
    font-weight: 500;
}

.modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 32px 32px;
    border-top: 1px solid #f0f0f0;
    background: #fafafa;
}

.modal-btn {
    padding: 12px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.confirm-btn {
    background: linear-gradient(135deg, #800000 0%, #C46B02 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
}

.confirm-btn:hover {
    background: linear-gradient(135deg, #a00000 0%, #d47b02 100%);
    box-shadow: 0 6px 16px rgba(128, 0, 0, 0.4);
    transform: translateY(-2px);
}

.cancel-btn {
    background: transparent;
    color: #800000;
    border: 2px solid #e0e0e0;
}

.cancel-btn:hover {
    background: #f8f8f8;
    border-color: #800000;
}

/* All Background Modal Text Colors */
#allBackgroundModal .modal-content {
    color: #FDDE54;
}
#allBackgroundModal .candidate-bg-section {
    margin-bottom: 24px;
}
#allBackgroundModal .candidate-bg-position {
    font-weight: 700;
    font-size: 1.1em;
    color: #FDDE54;
    margin-bottom: 8px;
    border-bottom: 1.5px solid #C46B02;
    padding-bottom: 4px;
}
#allBackgroundModal .candidate-bg-card {
    margin-bottom: 18px;
    padding: 14px 18px;
    background: rgba(253,222,84,0.07);
    border-radius: 8px;
}
#allBackgroundModal .candidate-bg-name {
    font-weight: 600;
    color: #FDDE54;
    font-size: 1em;
}
#allBackgroundModal .candidate-bg-program {
    color: #ffe680;
    font-weight: 400;
    font-size: 0.98em;
    margin-left: 6px;
}
#allBackgroundModal .candidate-bg-info {
    margin-top: 6px;
    color: #f8f8f8;
    font-size: 0.98em;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .voting-container {
        margin: 0;
        border-radius: 0;
    }
    
    .position-section {
        padding: 24px 16px;
    }
    
    .page-title {
        padding: 32px 16px 20px;
        font-size: 1.75rem;
    }
    
    .candidate-card {
        padding: 12px;
        gap: 12px;
    }
    
    .candidate-image {
        width: 48px;
        height: 48px;
    }
    
    .submit-btn {
        margin: 24px 16px;
    }
    
    .modal-content {
        margin: 20px;
        width: calc(100% - 40px);
        margin-left: 50px;
    }
    
    .modal-header,
    .vote-summary,
    .modal-buttons {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .selected-indicator {
        top: 22px;
     }
}
.background-btn {
    background: #FDDE54;
    color: #7F0404;
    border: none;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 0.93em;
    font-weight: 600;
    margin-top: 6px;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    box-shadow: 0 2px 8px rgba(253,222,84,0.08);
}
.background-btn:hover {
    background: #800000;
    color: #FDDE54;
}
    </style>
</head>
<body>
    <div class="voting-container">
        <h1 class="page-title" style="position:relative;">
            EVSU Student Council Elections
            <button type="button" id="viewAllBackgroundBtn" style="position:absolute;top:22px;right:24px;background:#FDDE54;color:#7F0404;font-weight:600;padding:7px 14px;border:none;border-radius:7px;box-shadow:0 2px 8px rgba(253,222,84,0.13);font-size:0.93rem;cursor:pointer;transition:background 0.2s;z-index:10;">
                <i class="fas fa-users"></i> View All Background Information
            </button>
        </h1>
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
                            <div class="candidate-info-wrapper">
                                <h3 class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></h3>
                                <p class="candidate-info"><?php echo htmlspecialchars($candidate['program']); ?></p>
                                <p class="candidate-info"><?php echo htmlspecialchars($candidate['year']); ?> Year</p>
                                <?php if (!empty($candidate['background'])): ?>
                                    <button type="button" class="background-btn" data-background="<?php echo htmlspecialchars($candidate['background'], ENT_QUOTES); ?>" data-name="<?php echo htmlspecialchars($candidate['name'], ENT_QUOTES); ?>" onclick="event.stopPropagation(); showBackgroundModal(this);">Background Information</button>
                                <?php endif; ?>
                            </div>
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

    <!-- Background Modal -->
    <div id="backgroundModal" class="modal" style="display:none;z-index:2000;">
        <div class="modal-content" style="max-width:500px;">
            <div class="modal-header">
                <h2 class="modal-title" id="backgroundModalTitle">Background Information</h2>
                <button class="close-modal" id="closeBackgroundModal">&times;</button>
            </div>
            <div class="vote-summary" id="backgroundModalBody" style="padding:24px 32px 32px 32px;font-size:1.05em;"></div>
        </div>
    </div>

    <!-- All Background Modal -->
    <div id="allBackgroundModal" class="modal" style="display:none;z-index:3000;">
        <div class="modal-content" style="max-width:700px;">
            <div class="modal-header">
                <h2 class="modal-title">All Candidates' Background Information</h2>
                <button class="close-modal" id="closeAllBackgroundModal">&times;</button>
            </div>
            <div class="vote-summary" id="allBackgroundBody" style="padding:24px 32px 32px 32px;font-size:1.05em;max-height:60vh;overflow-y:auto;"></div>
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

        // Background modal logic
        function showBackgroundModal(btn) {
            const name = btn.getAttribute('data-name');
            const background = btn.getAttribute('data-background');
            document.getElementById('backgroundModalTitle').textContent = name + " - Background Information";
            document.getElementById('backgroundModalBody').innerHTML = background.replace(/\n/g, '<br>');
            document.getElementById('backgroundModal').style.display = 'block';
        }

        document.getElementById('closeBackgroundModal').onclick = function() {
            document.getElementById('backgroundModal').style.display = 'none';
        };
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('backgroundModal')) document.getElementById('backgroundModal').style.display = 'none';
        });

        // View All Background Information logic
        document.getElementById('viewAllBackgroundBtn').onclick = function() {
            const allBgBody = document.getElementById('allBackgroundBody');
            allBgBody.innerHTML = '';
            const positions = document.querySelectorAll('.position-section');
            positions.forEach(position => {
                const positionTitle = position.querySelector('.position-title').textContent;
                const candidates = position.querySelectorAll('.candidate-card');
                let hasBg = false;
                let sectionHtml = `<div class='candidate-bg-section'><div class='candidate-bg-position'>${positionTitle}</div>`;
                candidates.forEach(card => {
                    const name = card.querySelector('.candidate-name').textContent;
                    const program = card.querySelector('.candidate-info').textContent;
                    const year = card.querySelectorAll('.candidate-info')[1]?.textContent || '';
                    const bgBtn = card.querySelector('.background-btn');
                    if (bgBtn) {
                        hasBg = true;
                        const background = bgBtn.getAttribute('data-background');
                        sectionHtml += `<div class='candidate-bg-card'>
                            <div class='candidate-bg-name'>${name} <span class='candidate-bg-program'>(${program}, <span class='candidate-bg-program'>${year}</span>)</span></div>
                            <div class='candidate-bg-info'>${background.replace(/\n/g,'<br>')}</div>
                        </div>`;
                    }
                });
                sectionHtml += '</div>';
                if (hasBg) allBgBody.innerHTML += sectionHtml;
            });
            if (!allBgBody.innerHTML) allBgBody.innerHTML = '<div style="color:#fff;text-align:center;">No background information available for any candidate.</div>';
            document.getElementById('allBackgroundModal').style.display = 'block';
        };
        document.getElementById('closeAllBackgroundModal').onclick = function() {
            document.getElementById('allBackgroundModal').style.display = 'none';
        };
        window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('allBackgroundModal')) document.getElementById('allBackgroundModal').style.display = 'none';
        });
    </script>
</body>
</html>