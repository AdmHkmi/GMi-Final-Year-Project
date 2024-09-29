<?php
include '../../../Database/DatabaseConnection.php';
?>

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
                    // Fetch report data from the database
                    $reportQuery = "
                        SELECT
                            (SELECT COUNT(DISTINCT StudentID) FROM VSStudents) AS totalUsers,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSStudents WHERE UserApproval = 1) AS totalApprovedUsers,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSStudents WHERE UserApproval = 0) AS totalUnapprovedUsers,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE CandidateApproval = 1) AS totalCandidates,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE SRCApproval = 1) AS totalSRC,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE NominationVoteLimit >= 1) AS totalParticipationNomination,
                            (SELECT COUNT(DISTINCT StudentID) FROM VSVote WHERE SRCVoteLimit >= 1) AS totalParticipationSRC
                        FROM dual
                    ";

                    $result = $conn->query($reportQuery);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo "<p><strong>Total Users:</strong> " . $row['totalUsers'] . "</p>";
                        echo "<p><strong>Total Approved Users:</strong> " . $row['totalApprovedUsers'] . "</p>";
                        echo "<p><strong>Total Unapproved Users:</strong> " . $row['totalUnapprovedUsers'] . "</p>";
                        echo "<p><strong>Total Candidates:</strong> " . $row['totalCandidates'] . "</p>";
                        echo "<p><strong>Total SRC:</strong> " . $row['totalSRC'] . "</p>";
                        echo "<p><strong>Total Participation (Nomination Vote):</strong> " . $row['totalParticipationNomination'] . "</p>";
                        echo "<p><strong>Total Participation (SRC Vote):</strong> " . $row['totalParticipationSRC'] . "</p>";
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
