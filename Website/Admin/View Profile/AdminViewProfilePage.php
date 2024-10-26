<html>
<head>
    <title>View Profile</title>
    <link rel="stylesheet" href="AdminViewProfilePage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            <li><a href="../Manage Participants/ManageParticipants.php">Manage Participants</a></li>
            <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
            <li><a href="../Manage News/ManageNews.php">Manage News</a></li>
            <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
        </ul>
    </nav>
    <div class="top-right-buttons">
        <a href="../Home Page/AdminHomepage.php">
            <button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button -->
        </a>
    </div>
</header>
    <main>
    <h1>View Profile</h1>
    <div class="instructions">
    <p>Welcome to the View Profile page! Here, you can view your username and password.</p>
    </div>    
    <?php include 'AdminViewProfileProcess.php'; ?>
    </main>
    <footer></footer>
</body>
</html>
