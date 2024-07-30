<?php

$host = 'localhost';
$username_db = 'root';
$password_db = 'root';
$database = 'quiz_db';

// Create connection
$conn = mysqli_connect($host, $username_db, $password_db, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM quiz_scores ORDER BY score DESC"; 
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo '<div id="scoreTable">';
    echo '<table>';
    echo '<tr><th>Rank</th><th>Username</th><th>Score</th></tr>';
    
    // Initialize rank counter
    $currentRank = 1;

    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        $username = htmlspecialchars($row['username']); 
        $scoreValue = htmlspecialchars($row['score']); 

        // Determine the class for the rank
        $rankClass = '';
        if ($currentRank == 1) {
            $rankClass = 'gold';
        } elseif ($currentRank == 2) {
            $rankClass = 'silver';
        } elseif ($currentRank == 3) {
            $rankClass = 'bronze';
        }
        
        echo "<tr class='$rankClass'>";
        echo "<td>$currentRank</td>";
        echo "<td>$username</td>";
        echo "<td>$scoreValue</td>";
        echo "</tr>";

        // Increment rank counter for the next iteration
        $currentRank++;
    }

    echo '</table>';
    echo '</div>';

} else {
    echo "No high scores found";
}

mysqli_close($conn);

?>

