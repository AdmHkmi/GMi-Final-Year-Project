<?php include '../Home Page/CheckCandidateApproval.php'; ?>

<html>
<head>
    <title>SRC Details Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/icon" href="../../../Images/favicon.ico">
    <link rel="stylesheet" href="EditSRCDetailsPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
    <div class="logo-container">
        <a href="../Home Page/UserHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"> <!-- Logo -->
        </a>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="../SRC Nomination/SRCNominationPage.php">Nomination Vote</a></li>
            <li><a href="../Vote Casting/VoteCastingPage.php">Candidate Vote</a></li>
            <li><a href="../View Result/ViewResultPage.php">View Result</a></li>
            <li><a href="../GMi Updates/GMiUpdatesPage.php">GMi Updates</a></li>
            <?php if ($showEditButton): ?>
                <li><a href="EditSRCDetailsPage.php">Edit SRC Details</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="top-right-buttons">
        <a href="../Home Page/UserHomepage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button -->
        </a>
    </div>
</header>
    <main>
        <div class="Instruction"> 
        <b><i>SRC PROFILE DETAILS</i></b><br>
        <b><i>You can update your name and manifesto.</i></b>
        </div>
        <?php
        include '../../../Database/DatabaseConnection.php';

        // Check if the session variable is set
        if (isset($_SESSION['StudentID'])) {
            // Get the logged-in StudentID from the session
            $loggedInUser = $_SESSION['StudentID'];

            // Check if the form was submitted for profile update
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Process form data
                $StudentName = $_POST['StudentName'];
                $Manifesto = $_POST['Manifesto'];

                // Validate input
                if (empty($StudentName) || empty($Manifesto)) {
                    echo '<script>alert("Please fill in all required fields."); window.location.href = "EditSRCDetailsPage.php";</script>';
                    exit; // Exit script if any field is empty
                }

                // Prepare and execute SQL update statement for VSVote table (for Manifesto)
                $sql = "UPDATE VSVote SET Manifesto=? WHERE StudentID=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $Manifesto, $loggedInUser);

                if ($stmt->execute()) {
                    // Prepare and execute SQL update statement for VSStudents table (for StudentName)
                    $sql2 = "UPDATE VSStudents SET StudentName=? WHERE StudentID=?";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bind_param("ss", $StudentName, $loggedInUser);
                    $stmt2->execute();
                    
                    echo '<script>alert("Successfully updated."); window.location.href = "EditSRCDetailsPage.php";</script>';
                    exit; // Exit after successful update
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            }

            // SQL query to fetch the details of the logged-in user from VSStudents table for StudentName and profile picture
            // and from VSVote table for Manifesto
            $sql = "SELECT S.StudentProfilePicture, S.StudentID, S.StudentName, V.Manifesto 
                    FROM VSVote V 
                    JOIN VSStudents S ON V.StudentID = S.StudentID 
                    WHERE V.StudentID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $loggedInUser);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are results and display them
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo ' <div class="extra-button">
                            <form method="post" action="Poster.php"><button class="posterbutton" type="submit">Print Poster</button></form>
                            <form method="post" action="DownloadQR.php"><button class="qrcodebutton" type="submit">Download QRCode</button></form>
                            </div>';
                    echo '<div class="Profile-Container">
                            <form action="EditSRCDetailsPage.php" method="post" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                                <div class="profile-header">
                                    <h2>SRC Profile Details</h2>
                                </div>
                                <div class="profile-content">
                                    <div class="profile-picture">
                                        <img src="../../../ProfilePicture/' . htmlspecialchars($row['StudentProfilePicture']) . '" alt="Profile Picture">
                                    </div>
                                    <div class="profile-details">
                                        <label for="StudentName"><b>Full Name</b></label>
                                        <input type="text" id="StudentName" name="StudentName" value="' . htmlspecialchars($row["StudentName"]) . '">
                                        <label for="Manifesto"><b>Manifesto</b></label>
                                        <textarea id="Manifesto" name="Manifesto" rows="4" cols="50" placeholder="Please write your manifesto." style="resize: none;">' . htmlspecialchars($row["Manifesto"]) . '</textarea>
                                    </div>
                                </div>
                                <div class="profile-footer">
                                    <button type="submit">Update SRC Profile</button>
                                </div>
                            </form>
                        </div>';
                }
            } else {
                echo '<script>alert("No record found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
            }
        } else {
            // Handle the case where the session variable is not set
            echo '<script>alert("Session is not set up, please sign in first."); window.location.href = "../../../index.html";</script>';
            exit; // Ensure no further code is executed        
        }
        // Close the connection
        $conn->close();
        ?>
    </main>
    <footer></footer>
</body>
</html>
