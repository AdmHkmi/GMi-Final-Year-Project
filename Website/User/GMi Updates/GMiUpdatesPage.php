<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GMi Updates Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="GMiUpdatesPage.css">
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
        <?php include 'ViewUpdatesProcess.php';?>
    </main>
    <footer></footer>
</body>
</html>
