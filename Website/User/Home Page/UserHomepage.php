<!DOCTYPE html>
<html>
<head>
    <title>SRC E-Voting GMi Website</title>
    <link rel="stylesheet" href="UserHomepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
        <div class="logo-container"><a href="UserHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
        <div class="top-right-buttons">
            <a href="../Edit Profile/UserProfilePage.php"><button class="user-button"><i class='fas fa-user-alt'></i></button></a>
            <button class="bars-button" id="barsButton"><i class="fa fa-bars"></i></button>
        </div>
    </header>
    <div class="main-header"><p>WILLKOMMEN IN UNSEREM "SMART VOTING SYSTEM"</p></div>
    <div class="sub-header">
        <h4><b>This is our new E-election!</b></h4>
        <p>This site is packed with many exciting features designed to streamline and simplify the election process.</p>
    </div>
    <main>
        <center><p>Please select an option:</p></center>
        <div class="option-button">
            <a href="../SRC Nomination/SRCNominationPage.php">
                <button class="option-button-item SRC-Nomination-Button">
                    <img src="../../../Images/SRC Nomination Icon.png" width="45" height="45">
                    SRC NOMINATION
                </button>
            </a>
            <a href="../Vote Casting/VoteCastingPage.php">
                <button class="option-button-item Vote-Casting-Button">
                    <img src="../../../Images/Vote Casting Icon.png" width="45" height="45">
                    VOTE CASTING
                </button>
            </a>
            <a href="../View Result/ViewResultPage.php">
                <button class="option-button-item View-Result-Button">
                    <img src="../../../Images/View Result Icon.png" width="45" height="45">
                    VIEW RESULT
                </button>
            </a>
            <a href="../GMi Updates/GMiUpdatesPage.php">
                <button class="option-button-item GMI-Updates-Button">
                    <img src="../../../Images/GMi Updates Icon.png" width="45" height="45">
                    GMi UPDATES
                </button>
            </a>
            <?php include 'SRCEditDetailsButton.php'; ?>
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
    <script src="UserHomepage.js"></script>
</body>
</html>