<?php
// Start the session at the beginning of the file
session_start();

// Check if the session is not set for AdminLoggedIn
if (!isset($_SESSION['AdminLoggedIn'])) {
    echo '<script>alert("Session is not set up, please sign in first."); window.location.href = "../../../index.html";</script>';
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <!-- Link to external stylesheet -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/icon" href="../../../Images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ManageUsers.css">
</head>
<body>
    <!-- Background Image -->
    <div class="background-image"></div>
    <!-- Header Section with Logo and Buttons -->
    <header class="header">
    <div class="logo-container">
        <a href="../Home Page/AdminHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"> <!-- Logo -->
        </a>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
            <li><a href="ManageUsers.php">Manage Users</a></li>
            <li><a href="../Manage Participants/ManageParticipants.php">Manage Participants</a></li>
            <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
            <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
            <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
        </ul>
    </nav>
    <div class="top-right-buttons"> <!-- Button linking to Add User Page --> <a href="AddUser.php"><button class="add-user">Add User</button></a> <!-- Scroll to top button --> <button class="back-button" onclick="scrollToTop()"><i class='fas fa-arrow-up'></i></button> <!-- Button linking back to Admin Homepage --> <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a> </div>
</header>
    <!-- Main Content -->
    <main>
        <!-- Page Title -->
        <h1>Manage Users</h1> 
        <!-- Instructions Section -->
        <div class="instructions">
            <p>Welcome to the Manage Users page! Here, you can view existing users, approve or unapprove user accounts, add new users, or delete users as needed.</p>
        </div>
        <!-- Container for Managing Users -->
        <div class="Manage-Users-Container">
            <!-- Sub-header for User List -->
            <div class="header-section">
                <h2>List of Registered Users</h2>
            </div>
            <p>To manage user access and prevent outsiders from gaining entry, you can approve their accounts, unapprove them if needed, or delete any unauthorized accounts from the system.</p>
            <!-- Search and Filter Form -->
            <center>                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <!-- Search input for filtering users by name or ID -->
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" placeholder="Enter Name or StudentID">
                    <button type="submit">Search</button>
                    <br><br>
                    <!-- Radio buttons for filtering users by status -->
                    <input type="radio" id="all" name="user_status" value="all" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'all' ? 'checked' : ''; ?>>
                    <label for="all">All</label>
                    <input type="radio" id="active" name="user_status" value="active" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'active' ? 'checked' : ''; ?>>
                    <label for="active">Active</label>
                    <input type="radio" id="inactive" name="user_status" value="inactive" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'inactive' ? 'checked' : ''; ?>>
                    <label for="inactive">Inactive</label>
                    <button type="submit">Filter</button>
                </form>
                <br>
                <!-- Display List of Users -->
                <?php 
                    include '../../../Database/DatabaseConnection.php';
                    // Base SQL query to fetch users
                    $sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID, UserApproval FROM VSStudents WHERE 1=1";
                    // Add search term condition if search term is provided
                    $searchTerm = null;
                    if (isset($_POST['search']) && !empty($_POST['search'])) {
                        $search = trim($_POST['search']); // Trim any extra whitespace
                        $searchTerm = "%" . $search . "%"; // Add wildcard characters for partial matches
                        $sql .= " AND (StudentName LIKE ? OR StudentID LIKE ?)";
                    }
                    // Add filter condition based on user status (active/inactive)
                    if (isset($_POST['user_status'])) {
                        if ($_POST['user_status'] == 'active') {
                            $sql .= " AND UserApproval = 1";
                        } elseif ($_POST['user_status'] == 'inactive') {
                            $sql .= " AND UserApproval = 0";
                        }
                    }
                    // Prepare the SQL query
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die('SQL prepare() failed: ' . htmlspecialchars($conn->error));
                    }
                    // Bind the search term parameters if search term is set
                    if ($searchTerm !== null) {
                        $stmt->bind_param("ss", $searchTerm, $searchTerm);
                    }
                    // Execute the query and get the result
                    $stmt->execute();
                    $result = $stmt->get_result();
                    // If users are found, display them in a table
                    if ($result->num_rows > 0) {
                        // Form for approving, unapproving, or deleting selected users
                        echo "<form action='ManageUsersProcess.php' method='post'>";
                        echo "<input type='submit' class='Approve-Button' name='bulk_action' value='Approve Selected' style='background-color: green; color: white;' onclick=\"return confirm('Are you sure you want to approve the desired user?')\">";
                        echo "<input type='submit' class='Unapprove-Button' name='bulk_action' value='Unapprove Selected' style='background-color: orange; color: white;' onclick=\"return confirm('Are you sure you want to unapprove the desired user?')\">";
                        echo "<input type='submit' class='Delete-Button' name='bulk_action' value='Delete Selected' style='background-color: red; color: white;' onclick=\"return confirm('Are you sure you want to delete the desired user?')\">";
                        echo "<br><br><br>";
                        // Table to display the users
                        echo "<div class='table-container'>";
                        echo "<table border='1' align='center'>";
                        echo "<tr>";
                        echo "<th>Select/Deselect all<br><input type='checkbox' id='select-all'></th>";
                        echo "<th>Profile Picture</th>";
                        echo "<th>Student Name</th>";
                        echo "<th>Student Email</th>";
                        echo "<th>StudentID</th>";
                        echo "<th>User Status</th>";
                        echo "</tr>";
                        // Loop through the users and display their details
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td align='center'><input type='checkbox' name='selected_users[]' value='" . $row["StudentID"] . "'></td>";
                            echo "<td align='center'><img src='../../../ProfilePicture/" . $row["StudentProfilePicture"] . "' alt='Profile Picture' style='width: 100px; height: 100px;'></td>";
                            echo "<td>" . htmlspecialchars($row["StudentName"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["StudentEmail"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["StudentID"]) . "</td>";
                            // Display user status as "Active" or "Inactive"
                            echo "<td>";
                            if ($row["UserApproval"]) {
                                echo "<span style='color: green;'>Active</span>";
                            } else {
                                echo "<span style='color: red;'>Inactive</span>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div";
                        echo "</form>";
                    } else {
                        // Display appropriate message if no users found
                        if ($searchTerm !== null) {
                            echo "<br><center>No user matching your search.</center>";
                        } else {
                            echo "<center><h3>No " . ($_POST['user_status'] == 'active' ? "active" : "inactive") . " users found</h3></center>";
                        }
                    }
                    // Close the statement and database connection
                    $stmt->close();
                    $conn->close();
                ?>
            </center>
        </div>
    </main>
    <!-- Footer (Empty for now) -->
    <footer></footer>
    <!-- JavaScript to handle scrolling and select/deselect all checkboxes -->
    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Select/Deselect all checkboxes functionality
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        };
    </script>
</body>
</html>
