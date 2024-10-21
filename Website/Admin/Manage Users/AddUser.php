<?php include 'AddUserProcess.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Users Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="AddUser.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="ManageUsers.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <div class="add-user-container">
        <div class="header-section">
            <h2>Add a New User</h2>
        </div>
        <p>Add users manually to avoid registration delays or technical issues.</p>
            <form action="AddUser.php" method="POST">
                <label for="studentID">Student ID:</label>
                <input type="text" id="studentID" name="studentID" required>

                <label for="studentEmail">Student Email:</label>
                <input type="email" id="studentEmail" name="studentEmail" required>

                <label for="studentPassword">Password:</label>
                <input type="password" id="studentPassword" name="studentPassword" required>

                <label for="studentName">Student Name:</label>
                <input type="text" id="studentName" name="studentName" required>

                <label for="userApproval">User Approval:</label>
                <input type="checkbox" id="userApproval" name="userApproval">

                <button type="submit">Add User</button>
            </form>
        </div>
    </main>
    <footer></footer>
</body>
</html>
