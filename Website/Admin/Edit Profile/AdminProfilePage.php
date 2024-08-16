<html>
<head>
    <title>User Profile Page</title>
    <link rel="stylesheet" href="AdminProfilePage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <h2 align="center" style="color: #24287E;">Edit Profile Details</h2>
    <?php include 'AdminEditProfileProcess.php'; ?>
    </main>
    <footer></footer>
</body>
</html>
