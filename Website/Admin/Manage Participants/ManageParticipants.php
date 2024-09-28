<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ManageParticipants.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <div class="Manage-Participants-Container">
            <center>
            <h2>Manage Participants</h2>
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
</body>
</html>
