<!DOCTYPE html>
<html>
<head>
    <title>Generate Report</title>
    <link rel="stylesheet" href="GenerateReport.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <!-- Back button -->
            <a href="../Home Page/AdminHomepage.php">
                <button class="back-button">
                    <i class='fas fa-arrow-left'></i> 
                </button>
            </a>
        </div>
    </header>
    <main>
        <div class="report-container">
            <center>
                <h1>Event Report</h1>
                <div class="report-details">
                    <?php
                    include '../../../Database/DatabaseConnection.php';
                    // Fetch report data from the database
$reportQuery = "
    SELECT
        (SELECT COUNT(DISTINCT StudentID) FROM VSStudents) AS totalUsers,
        (SELECT COUNT(DISTINCT StudentID) FROM VSCurrentCandidate) AS totalParticipants,
        (SELECT COUNT(DISTINCT StudentID) FROM VSCurrentCandidate) AS totalCandidatesSelected,
        (SELECT COUNT(DISTINCT StudentID) FROM VSCurrentCandidate) AS totalCandidatesParticipating,
        (SELECT COUNT(DISTINCT EventID) FROM VSEvents) AS totalNominationForms,
        (SELECT MAX(StartDate) FROM VSEvents) AS eventDateTime
    FROM dual
";


                    $result = $conn->query($reportQuery);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo "<p><strong>Total Users:</strong> " . $row['totalUsers'] . "</p>";
                        echo "<p><strong>Total Participants:</strong> " . $row['totalParticipants'] . "</p>";
                        echo "<p><strong>Total Candidates Selected:</strong> " . $row['totalCandidatesSelected'] . "</p>";
                        echo "<p><strong>Total Candidates Participating:</strong> " . $row['totalCandidatesParticipating'] . "</p>";
                        echo "<p><strong>Total Nomination Forms Submitted:</strong> " . $row['totalNominationForms'] . "</p>";
                        echo "<p><strong>Date and Time of the Event:</strong> " . $row['eventDateTime'] . "</p>";
                    } else {
                        echo "<p>No data available.</p>";
                    }

                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </center>
        </div>
    </main>
    <footer></footer>
</body>
</html>
