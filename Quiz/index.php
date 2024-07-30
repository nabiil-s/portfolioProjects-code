
<?php

require 'database.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Escape and sanitize user input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $score = intval($_POST['score']);

    // Function to insert score
    function insertScore($conn, $username, $score) {
        $sql_insert = "INSERT INTO quiz_scores (username, score) VALUES (?,?)";
        $stmt = mysqli_prepare($conn, $sql_insert);

        mysqli_stmt_bind_param($stmt, 'si', $username, $score);
        $result_insert = mysqli_stmt_execute($stmt);

        if (!$result_insert) {
            echo "Insert failed: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }

    // Function to get lowest score
    function getLowestScore($conn) {
        $sql_min = "SELECT MIN(score) AS lowest_score FROM quiz_scores";
        $result = mysqli_query($conn, $sql_min);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row["lowest_score"];
        } else {
            return null;
        }
    }

    // Query to count entries
    $sql_count = "SELECT COUNT(*) AS entry_count FROM quiz_scores";
    $result_count = mysqli_query($conn, $sql_count);

    if (mysqli_num_rows($result_count) > 0) {
        $row = mysqli_fetch_assoc($result_count);
        $entry_count = $row["entry_count"];

        if ($entry_count < 7) {
            // Insert username and score if less than 7 entries
            insertScore($conn, $username, $score);
        } else {
            // If 7 entries, check if score is higher than the lowest score
            $lowest_score = getLowestScore($conn);

            if ($score > $lowest_score) {
                // Delete lowest score entry
                $sql_delete = "DELETE FROM quiz_scores WHERE score = ? LIMIT 1";
                $stmt_delete = mysqli_prepare($conn, $sql_delete);

                mysqli_stmt_bind_param($stmt_delete, "i", $lowest_score);
                mysqli_stmt_execute($stmt_delete);
                mysqli_stmt_close($stmt_delete);

                // Insert new score
                insertScore($conn, $username, $score);
            }
        }
    } else {
        echo "Error: Couldn't fetch entry count";
    }
} 

mysqli_close($conn);

?> 

<!DOCTYPE html>
<html lang="en-GB">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" type="image/png" href="img/speech.png">
    <title>Spanish Quiz</title>

</head>
<body>
    <div id="container">
        <div id="userData">
            <form id="userForm" action="" method="POST">
                <label for="name">Enter your name: </label>
                <input type="text" id="name" name="name">
                <button type="submit" onclick="showGreeting(event);">Submit</button>
                <div id="error"></div>
            </form>
        </div>

        <div id="userGreeting"></div>

        <div id="quizContainer">
            <div id="liveScore"></div>
            <div id="questions"></div>

            <div class="typeAnswer">
                <label for="answer">Your Answer: </label>
                <input type="text" id="answer">
                <button onclick="checkAnswer()">Submit</button>
            </div>

            <div id="result"></div>

        </div>

        <!-- Link to my portfolio --> 
        <div id="visitPortfolio">

            <div class="waterMark-container">
                 <h2 class=waterMark-info>Visit:</h2>

                <a class="portfolio-link" href="../Master_portfolio/indexDark.html">Portfolio</a>

                <h1 class="waterMark">N <span class="waterMark2">S</h1>

            </div>

        </div>

        <!-- hidden form -->
        <div id="finalResultsContainer" style="display: none;">
            <form id="resultForm" action="" method="POST">
                <input type="hidden" id="username" name="username">
                <input type="hidden" id="score" name="score">
                <p id="finalResultsMessage"></p>
            </form>
        </div>
        
    </div>

    <script src="script.js"></script>

</body>
</html>
