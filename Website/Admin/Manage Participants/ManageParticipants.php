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
    <div class="top-right-buttons"><!-- Scroll to top button --> <button class="back-button" onclick="scrollToTop()"><i class='fas fa-arrow-up'></i></button> <!-- Button linking back to Admin Homepage --> <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a> </div>
</header>
    <main>
    <h1>Manage Participants</h1>
        <div class="instructions">
            <p>Welcome to the Manage Participants page! Here, you can view existing participants, approve or unapprove candidates, and determine the winner of the vote.</p>
        </div>
        <div class="Manage-Participants-Container">
            <div class="header-section"><h2>Manage Candidate and SRC</h2></div>
            <p>You can view the votes that have been cast for each candidate and select the winner. To streamline your search for a specific candidate, you can utilize the search tool.</p>
            <center>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="search-form">
                <label for="search" class="search-label">Search:</label>
                <input type="text" id="search" name="search" placeholder="Enter Name or StudentID" class="search-input">
                <button type="submit" class="search-button">Search</button>
            </form>  
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { include 'SearchParticipants.php'; } ?><br>
            <?php include 'ViewAndManageParticipants.php';?>
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
