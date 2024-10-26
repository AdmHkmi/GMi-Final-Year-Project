<!DOCTYPE html>
<html>
<head>
    <title>Manage Result</title>
    <link rel="stylesheet" href="ManageResult.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
                <li><a href="../Manage Users/ManageUsers.php">Manage Users</a></li>
                <li><a href="../Manage Participants/ManageParticipants.php">Manage Participants</a></li>
                <li><a href="ManageResult.php">Manage Results</a></li>
                <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
                <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
            </ul>
        </nav>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button --></a>
        </div>
    </header>
    <main>
        <h1>Manage Results</h1>
        <div class="instructions">
            <p>Welcome to the Manage Results page! Here, you can view existing results, share or unshare results, and reset candidates and SRC as needed.</p>
        </div>
        <div class="side-by-side-container">
        <div class="Container">
        <div class="header-section"><h2>SRC Result</h2></div>
        <p>View all approved candidates. Click "Share" to share results with students or "Unshare" if you prefer not to. To change your decisions, click "Reset."</p>
            <center>
                <h2>List of Approved Candidates</h2>
                <div class="button-group">
                    <form action="ResetCandidates.php" method="post" onsubmit="return confirm('Are you sure you want to reset all approved candidates?');">
                        <button type="submit" class="Reset_Candidate_Button">Reset Candidates</button>
                    </form><br>
                    <form action="UnshareNominationResultProcess.php" method="post" onsubmit="return confirm('Are you sure you want to unshare the result?');">
                        <button type="submit" class="Unshare_Nomination_Result_Button">Unshare Nomination Result</button>
                    </form><br>
                    <form action="ShareNominationResultProcess.php" method="post" onsubmit="return confirm('Are you sure you want to share the result?');">
                        <button type="submit" class="Share_Nomination_Result_Button">Share Nomination Result</button>
                    </form>
                </div>
                <?php
                    include '../../../Database/DatabaseConnection.php';

                    // Fetch IsActive status from VSEvents table
                    $fetchStmt = $conn->prepare("SELECT IsActive FROM VSEvents WHERE EventName = 'Nomination Result'");
                    $fetchStmt->execute();
                    $fetchStmt->bind_result($isActive);
                    $fetchStmt->fetch();
                    $fetchStmt->close();

                    // Determine status text and color based on IsActive value
                    $statusText = $isActive ? '<span class="shared">Shared</span>' : '<span class="not-shared">Not Shared</span>';
                    echo "<p>Candidate Result Status: $statusText</p>";

                    // Close connection
                    $conn->close();
                ?>
                <br>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" placeholder="Enter Name or StudentID">
                    <input type="hidden" name="form_type" value="candidates">
                    <button type="submit">Search</button>
                </form>
                <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'candidates') { 
                    include 'SearchApprovedCandidates.php'; 
                } 
                ?>
                <br>
                <?php include 'ViewApprovedCandidates.php'; ?>
            </center>
        </div>
        <div class="Container">
        <div class="header-section"><h2>SRC Result</h2></div>
        <p>View all approved SRCs. Click "Share" to share results with students or "Unshare" if you prefer not to. To change your decisions, click "Reset."</p>
            <center>
                <h2>List of Approved SRC</h2>
                <div class="button-group">
                    <form action="ResetSRC.php" method="post" onsubmit="return confirm('Are you sure you want to reset all approved SRC?');">
                        <button type="submit" class="Reset_SRC_Button">Reset SRC</button>
                    </form><br>
                    <form action="UnshareSRCResultProcess.php" method="post" onsubmit="return confirm('Are you sure you want to unshare the result?');">
                        <button type="submit" class="Unshare_SRC_Result_Button">Unshare SRC Result</button>
                    </form><br>
                    <form action="ShareSRCResultProcess.php" method="post" onsubmit="return confirm('Are you sure you want to share the result?');">
                        <button type="submit" class="Share_SRC_Result_Button">Share SRC Result</button>
                    </form>
                </div>
                <?php
                // Database connection details
                include '../../../Database/DatabaseConnection.php';

                // Fetch IsActive status from VSEvents table
                $fetchStmt = $conn->prepare("SELECT IsActive FROM VSEvents WHERE EventName = 'SRC Result'");
                $fetchStmt->execute();
                $fetchStmt->bind_result($isActive);
                $fetchStmt->fetch();
                $fetchStmt->close();

                // Determine status text and color based on IsActive value
                $statusText = $isActive ? '<span class="shared">Shared</span>' : '<span class="not-shared">Not Shared</span>';
                echo "<p>SRC Result Status: $statusText</p>";

                // Close connection
                $conn->close();
                ?>
                <br>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" placeholder="Enter Name or StudentID">
                    <input type="hidden" name="form_type" value="src">
                    <button type="submit">Search</button>
                </form>
                <?php 
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] == 'src') { 
                    include 'SearchApprovedSRC.php'; 
                } 
                ?>
                <br>
                <?php include 'ViewApprovedSRC.php'; ?>
            </center>
        </div>
            </div>
    </main>
    <footer></footer>
</body>
</html>
