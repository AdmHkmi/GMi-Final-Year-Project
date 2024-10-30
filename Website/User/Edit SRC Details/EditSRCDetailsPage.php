<html>
<head>
    <title>SRC Details Page</title>
    <link rel="stylesheet" href="EditSRCDetailsPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/UserHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="../Home Page/UserHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <?php
        session_start(); // Start the session to access session variables

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
                    echo '<div class="Profile-Container">
                            <form action="EditSRCDetailsPage.php" method="post" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                                <div class="profile-header">
                                    <h2>Edit Your SRC Profile</h2>
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
                            <div class="profile-footer"><form method="post" action="Poster.php"><button class="posterbutton" type="submit">Print Poster</button></form></div>
                        </div>';
                }
            } else {
                echo '<script>alert("No record found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
            }
        } else {
            // Handle the case where the session variable is not set
            echo '<script>alert("You must be logged in to view this page."); window.location.href = "../../../index.html";</script>';
        }

        // Close the connection
        $conn->close();
        ?>
    </main>
    <footer></footer>
</body>
</html>
