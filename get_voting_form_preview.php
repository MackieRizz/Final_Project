<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Get all positions and their candidates
    $query = "SELECT DISTINCT position_id, position FROM candidate_positions ORDER BY position_id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $html = '<div style="font-family: Arial, sans-serif;">';
        $html .= '<h2 style="text-align: center; color: #2d0808; margin-bottom: 30px;">EVSU Student Council Elections</h2>';
        
        while ($row = $result->fetch_assoc()) {
            $position_id = $row['position_id'];
            $position = $row['position'];
            
            $html .= '<div style="margin-bottom: 30px; border: 1px solid #ddd; padding: 20px; border-radius: 10px;">';
            $html .= '<h3 style="color: #2d0808; margin-bottom: 15px;">' . htmlspecialchars($position) . '</h3>';
            
            // Get candidates for this position
            $candidates_query = "SELECT * FROM candidate_positions WHERE position_id = ? ORDER BY candidate_id";
            $stmt = $conn->prepare($candidates_query);
            $stmt->bind_param("s", $position_id);
            $stmt->execute();
            $candidates_result = $stmt->get_result();
            
            while ($candidate = $candidates_result->fetch_assoc()) {
                $html .= '<div style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; border: 1px solid #eee; border-radius: 5px;">';
                
                // Candidate image
                if (!empty($candidate['image'])) {
                    $html .= '<img src="' . htmlspecialchars($candidate['image']) . '" alt="' . htmlspecialchars($candidate['name']) . '" style="width: 60px; height: 60px; border-radius: 50%; margin-right: 15px; object-fit: cover;">';
                } else {
                    $html .= '<div style="width: 60px; height: 60px; border-radius: 50%; background: #eee; margin-right: 15px; display: flex; align-items: center; justify-content: center; color: #666;">No Image</div>';
                }
                
                // Candidate info
                $html .= '<div style="flex-grow: 1;">';
                $html .= '<h4 style="margin: 0; color: #2d0808;">' . htmlspecialchars($candidate['name']) . '</h4>';
                $html .= '<p style="margin: 5px 0; color: #666;">' . htmlspecialchars($candidate['program']) . ' - ' . htmlspecialchars($candidate['year']) . ' Year</p>';
                if (!empty($candidate['background'])) {
                    $html .= '<button type="button" class="background-btn" data-background="' . htmlspecialchars($candidate['background'], ENT_QUOTES) . '" data-name="' . htmlspecialchars($candidate['name'], ENT_QUOTES) . '" style="background:#FDDE54;color:#800000;border:none;border-radius:6px;padding:6px 16px;font-size:0.95em;font-weight:600;margin-top:6px;cursor:pointer;transition:background 0.2s,color 0.2s;box-shadow:0 2px 8px rgba(253,222,84,0.08);">Background Information</button>';
                }
                $html .= '</div>';
                
                // Radio button
                $html .= '<input type="radio" name="position_' . htmlspecialchars($position_id) . '" value="' . htmlspecialchars($candidate['id']) . '" style="margin-left: 15px;">';
                
                $html .= '</div>';
            }
            
            $stmt->close();
            $html .= '</div>';
        }
        
        // Add modal and JS at the end of $html
        $html .= '<div id="backgroundModal" style="display:none;position:fixed;z-index:2000;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;">';
        $html .= '<div style="background:#fff;color:#2d0808;max-width:500px;width:90%;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.3);padding:24px 32px 32px 32px;position:relative;">';
        $html .= '<h2 id="backgroundModalTitle">Background Information</h2>';
        $html .= '<button id="closeBackgroundModal" style="position:absolute;top:10px;right:10px;background:none;border:none;font-size:1.5em;color:#800000;cursor:pointer;">&times;</button>';
        $html .= '<div id="backgroundModalBody" style="margin-top:18px;font-size:1.05em;"></div>';
        $html .= '</div></div>';
        $html .= '<script>document.querySelectorAll(".background-btn").forEach(function(btn){btn.addEventListener("click",function(){var name=this.getAttribute("data-name");var background=this.getAttribute("data-background");document.getElementById("backgroundModalTitle").textContent=name+" - Background Information";document.getElementById("backgroundModalBody").innerHTML=background.replace(/\n/g,"<br>");document.getElementById("backgroundModal").style.display="flex";});});document.getElementById("closeBackgroundModal").onclick=function(){document.getElementById("backgroundModal").style.display="none";};window.addEventListener("click",function(e){if(e.target===document.getElementById("backgroundModal")){document.getElementById("backgroundModal").style.display="none";}});</script>';
        
        $html .= '</div>';
        
        echo json_encode(['success' => true, 'html' => $html]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No positions found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>