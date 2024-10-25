<?php
session_start(); // Start the session to access session variables

// Check if AdminLoggedIn session variable is not set
if (!isset($_SESSION['AdminLoggedIn'])) {
    header("Location: ../../../index.html"); // Redirect to login page
    exit();
}
?>
<html>
<head>
    <title>SRC E-Voting GMi Website</title>
    <link rel="stylesheet" href="AdminHomepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
        <div class="logo-container"><a href="AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
        <div class="top-right-buttons">
            <a href="../Edit Profile/AdminProfilePage.php"><button class="user-button"><i class='fas fa-user-alt'></i></button></a>
            <button class="bars-button" id="barsButton"><i class="fa fa-bars"></i></button>
        </div>
    </header>
    <div class="main-header"><p>WILLKOMMEN IN UNSEREM "SMART VOTING SYSTEM"</p></div>
    <div class="sub-header"><h4><b>This is our new E-election!</b></h4><p>This site is packed with many exciting features designed to streamline and simplify the election process.</p></div>
    <main>
        <center><p>Please select an option:</p></center>
        <div class="option-button">
            <a href="../Manage Events/ManageEvents.php">
                <button class="option-button-item SRC-Nomination-Button">
                    <img src="../../../Images/Manage Event Icon.png" width="45" height="45">
                    MANAGE EVENTS
                </button>
            </a>
            <a href="../Manage Users/ManageUsers.php">
                <button class="option-button-item Manage-User-Button">
                    <img src="../../../Images/Manage Users Icon.png" width="45" height="45">
                    MANAGE USERS
                </button>
            </a>
            <a href="../Manage Participants/ManageParticipants.php">
                <button class="option-button-item Manage-Candidates-Button">
                    <img src="../../../Images/Manage Candidates Icon.png" width="45" height="45">
                    MANAGE PARTICIPANTS
                </button>
            </a>
            <a href="../Manage Result/ManageResult.php">
                <button class="option-button-item Manage-Result-Button">
                    <img src="../../../Images/Manage Result Icon.png" width="45" height="45">
                    MANAGE RESULT
                </button>
            </a>
            <a href="../Manage News/ManageNews.php">
                <button class="option-button-item Manage-News-Button">
                    <img src="../../../Images/Manage News Icon.png" width="45" height="45">
                    MANAGE NEWS
                </button>
            </a>
            <!-- New Generate Report button -->
            <a href="../Generate Report/GenerateReport.php">
                <button class="option-button-item Generate-Report-Button">
                    <img src="../../../Images/Generate Report Icon.png" width="45" height="45">
                    GENERATE REPORT
                </button>
            </a>
        </div>
    </main>
    <footer>
        <div class="contact-button">
            <a href="mailto:EElectionGMiManagement@gmail.com"><button>Contact Us</button></a>
        </div>
    </footer>
    <div id="overlay"></div>
    <div id="sidebar">
        <button class="logout-button" id="LOGOUTButton">Logout</button>
    </div>
    <script src="AdminHomepage.js"></script>
</body>
</html>
