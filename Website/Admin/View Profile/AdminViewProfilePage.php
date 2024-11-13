<?php
session_start(); // Start the session at the very beginning

include '../../../Database/DatabaseConnection.php';

// Check if the session variable is set
if (isset($_SESSION['AdminUsername'])) {
    // Get the logged-in username from the session
    $loggedInUser = $_SESSION['AdminUsername'];

    // SQL query to fetch the details of the logged-in admin
    $sql = "SELECT AdminUsername, AdminPassword FROM VSAdmin WHERE AdminUsername = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Redirect to the login page if session variable is not set
    echo '<script>alert("Session is not set up, please sign in first."); window.location.href = "../../../index.html";</script>';
    exit();
}

// Close the connection after the query
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="AdminViewProfilePage.css">
    <link rel="icon" type="image/icon" href="../../../Images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
    <div class="logo-container">
        <a href="../Home Page/AdminHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo">
        </a>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
            <li><a href="../Manage Users/ManageUsers.php">Manage Users</a></li>
            <li><a href="../Manage Participants/ManageParticipants.php">Manage Participants</a></li>
            <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
            <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
            <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
        </ul>
    </nav>
    <div class="top-right-buttons">
        <a href="../Home Page/AdminHomepage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button>
        </a>
    </div>
</header>
    <main>
    <h1>View Profile</h1>
    <div class="instructions">
    <p>Welcome to the View Profile page! Here, you can view your username and password.</p>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="Profile-Container">
                <div class="header-section"><h2>Profile Details</h2></div>
                <table>
                    <tr>
                        <td><b>Username</b><br>
                            <input type="text" id="AdminUsername" name="AdminUsername" value="<?= htmlspecialchars($row["AdminUsername"]) ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Password</b><br>
                            <input type="text" id="AdminPassword" name="AdminPassword" value="<?= htmlspecialchars($row["AdminPassword"]) ?>" readonly><br><br>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    
    </main>
    <footer></footer>
</body>
</html>
