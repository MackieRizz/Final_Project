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
                $html .= '</div>';
                
                // Radio button
                $html .= '<input type="radio" name="position_' . htmlspecialchars($position_id) . '" value="' . htmlspecialchars($candidate['id']) . '" style="margin-left: 15px;">';
                
                $html .= '</div>';
            }
            
            $stmt->close();
            $html .= '</div>';
        }
        
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