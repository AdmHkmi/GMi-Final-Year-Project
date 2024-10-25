<?php include 'AddUserProcess.php'; ?>  <!-- Include the PHP script to handle form submission -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Users Page</title>
    <!-- Font Awesome for icons and external CSS for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="AddUser.css">
</head>
<body>
    <div class="background-image"></div> <!-- Background image for the page -->
    <header>
        <!-- Logo section with a link back to the Admin Homepage -->
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <!-- Top-right buttons (a back button to return to the Manage Users page) -->
        <div class="top-right-buttons">
            <a href="ManageUsers.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <!-- Container for adding new user information -->
        <div class="add-user-container">
            <div class="header-section">
                <h2>Add a New User</h2> <!-- Section header -->
            </div>
            <p>Add users manually to avoid registration delays or technical issues.</p> <!-- Instructions for the admin -->
            <!-- Form to add a new user -->
            <form action="AddUser.php" method="POST">
                <!-- Input for Student ID -->
                <label for="studentID">Student ID:</label>
                <input type="text" id="studentID" name="studentID" required>
                <!-- Input for Student Email -->
                <label for="studentEmail">Student Email:</label>
                <input type="email" id="studentEmail" name="studentEmail" required>
                <!-- Input for Password -->
                <label for="studentPassword">Password:</label>
                <input type="password" id="studentPassword" name="studentPassword" required>
                <!-- Input for Student Name -->
                <label for="studentName">Student Name:</label>
                <input type="text" id="studentName" name="studentName" required>
                <!-- Checkbox for User Approval (whether the user account should be approved upon creation) -->
                <label for="userApproval">User Approval:</label>
                <input type="checkbox" id="userApproval" name="userApproval">
                <!-- Submit button to add the user -->
                <button type="submit">Add User</button>
            </form>
        </div>
    </main>
    <footer></footer>
</body>
</html>
