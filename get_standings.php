<?php
include 'db.php';

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