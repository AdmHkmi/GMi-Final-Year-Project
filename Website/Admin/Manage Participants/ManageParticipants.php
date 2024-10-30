<!DOCTYPE html>
<html>
<head>
    <title>Manage Participants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ManageParticipants.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php">
                <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"> <!-- Logo -->
            </a>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
                <li><a href="../Manage Users/ManageUsers.php">Manage Users</a></li>
                <li><a href="ManageParticipants.php">Manage Participants</a></li>
                <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
                <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
                <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
            </ul>
        </nav>
        <div class="top-right-buttons">
            <button class="back-button" onclick="scrollToTop()"><i class='fas fa-arrow-up'></i></button>
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <h1>Manage Participants</h1>
        <div class="instructions">
            <p>Welcome to the Manage Participants page! Here, you can view existing participants, approve or unapprove candidates, and determine the winner of the vote.</p>
        </div>
        <div class="Manage-Participants-Container">
            <div class="header-section"><h2>Manage Candidate and SRC</h2></div>
            <p>You can view the votes that have been cast for each candidate and select the winner. To streamline your search for a specific candidate, you can utilize the search tool. You can also notify students about their application status, including whether their candidate nomination or SRC candidacy has been successfully approved.</p>
            <center>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="search-form">
                <label for="search" class="search-label">Search:</label>
                <input type="text" id="search" name="search" placeholder="Enter Name or StudentID" class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>  

            <!-- Add Notify Candidate and Notify SRC buttons -->
             <div class="notification-buttons">
             <form action="SendEmailCandidate.php" method="post" class="notification-form" onsubmit="return confirm('Are you sure you want to notify the approved candidates?');">
                <button type="submit" class="notify-button">Notify Candidate</button>
            </form>
            <form action="SendEmailSRC.php" method="post" class="notification-form" onsubmit="return confirm('Are you sure you want to notify the approved SRCs?');">
                <button type="submit" class="notify-button">Notify SRC</button>
            </form>
            </div>

            <?php
            // Include database connection
            include '../../../Database/DatabaseConnection.php';

            // Initialize search term
            $searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

            // Prepare the SQL query with a parameterized statement to prevent SQL injection
            $sql = "SELECT VSStudents.StudentProfilePicture, 
               VSStudents.StudentName, 
               VSStudents.StudentEmail, 
               VSStudents.StudentID, 
               VSVote.TotalCandidateVote, 
               VSVote.TotalSRCVote, 
               VSVote.CandidateApproval AS CandidateStatus, 
               VSVote.SRCApproval AS SRCStatus
               FROM VSStudents
               JOIN VSVote ON VSStudents.StudentID = VSVote.StudentID
               WHERE VSStudents.UserApproval = 1 
               ORDER BY 
               CASE 
               WHEN VSStudents.StudentName LIKE ? OR VSStudents.StudentID LIKE ? THEN 0 
               ELSE 1 
               END, 
               VSVote.TotalCandidateVote DESC, 
               VSVote.TotalSRCVote DESC";
               
               // Prepare statement
               $stmt = $conn->prepare($sql);
               $likeSearchTerm = '%' . $searchTerm . '%';
               $stmt->bind_param('ss', $likeSearchTerm, $likeSearchTerm);
               $stmt->execute();
               $result = $stmt->get_result();

               if ($result->num_rows > 0) {
                echo "<div class='participants-grid'>"; // Create a grid container for participants
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='participant-card'>"; // Start participant card
                    // Display profile picture
                    echo "<img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' alt='Profile Picture' class='profile-picture'>";
                    // Display name and other details
                    echo "<div class='participant-info'>";
                    echo "<h3>" . htmlspecialchars($row["StudentName"]) . "</h3>";
                    echo "<p>Email: " . htmlspecialchars($row["StudentEmail"]) . "</p>";
                    echo "<p>Student ID: " . htmlspecialchars($row["StudentID"]) . "</p>";
                    echo "<p>Total Candidate Votes: " . htmlspecialchars($row["TotalCandidateVote"]) . "</p>";
                    echo "<p>Total SRC Votes: " . htmlspecialchars($row["TotalSRCVote"]) . "</p>";

                    // Candidate Status
                    echo "<p>Candidate Status: ";
                    if ($row["CandidateStatus"] == 1) {
                        echo "<span class='active'>Candidate Active</span>";
                    } else {
                        echo "<span class='inactive'>Candidate Inactive</span>";
                    }
                    echo "</p>";
                    // SRC Status
                    echo "<p>SRC Status: ";       
                    if ($row["SRCStatus"] == 1) {           
                        echo "<span class='active'>SRC Active</span>";       
                    } else {          
                        echo "<span class='inactive'>SRC Inactive</span>";      
                    }       
                    echo "</p>";
                    // Action buttons
                    echo "<div class='action-buttons'>";
                    // Approve Candidate button
                    echo "<form action='CandidateApprovedProcess.php' method='post'>";        
                    echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";        
                    echo "<button type='submit' class='Approve-Button'>Approve Candidate</button>";        
                    echo "</form>";
        
                    // Unapprove Candidate button       
                    echo "<form action='CandidateUnapprovedProcess.php' method='post'>";        
                    echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";        
                    echo "<button type='submit' class='Unapprove-Button'>Unapprove Candidate</button>";        
                    echo "</form>";
        
                    // Approve SRC button       
                    echo "<form action='SRCApprovedProcess.php' method='post'>";        
                    echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";        
                    echo "<button type='submit' class='Approve-Button'>Approve SRC</button>";        
                    echo "</form>";

                    // Unapprove SRC button
                    echo "<form action='SRCUnapprovedProcess.php' method='post'>";
                    echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
                    echo "<button type='submit' class='Unapprove-Button'>Unapprove SRC</button>";
                    echo "</form>";

                    // View Manifesto button (only visible if CandidateStatus is true)
                    if ($row["CandidateStatus"] == 1) {
                        echo "<form action='ViewSRCDetails.php' method='get'>";
                        echo "<input type='hidden' name='StudentID' value='" . htmlspecialchars($row["StudentID"]) . "'>";
                        echo "<button type='submit' class='View-SRC-Details-Button'>View SRC Details</button>";
                        echo "</form>";
                    }
                    echo "</div>"; // Close action buttons
                    echo "</div>"; // Close participant info
                    echo "</div>"; // Close participant card
                }
                echo "</div>"; // Close participants grid
            } else {
                echo "<p>No participants found matching your search.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
            </center>
        </div>
    </main>
    <footer></footer>
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
