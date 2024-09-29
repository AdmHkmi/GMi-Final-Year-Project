<?php
// Start the session and check if the admin is logged in
session_start();

// Set PHP timezone to match your server's timezone
date_default_timezone_set('Asia/Kuala_Lumpur'); // Adjust as per your timezone

include '../../../Database/DatabaseConnection.php';

?>

<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="ManageEvents.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header class="header">
        <div class="logo-container"><a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a></div>
        <div class="top-right-buttons"><a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a></div>
    </header>
    <div Class="Container">
    <h1>Manage Events</h1>
    <!-- Existing events table -->
    <h2 style="background-color: #24287E; color: white; padding: 15px; margin: 20px 0; text-align: center; border-radius: 4px;">Existing Events</h2>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Action</th> 
        </tr>
        <?php
        // Include ViewEventProcess.php to handle event display and delete forms
        include 'ViewEventProcess.php';
        ?>
    </table>
    </div>
    <br><br>
    <!-- Form to add new event -->
    <div class="Add-Event-Container">
    <h2 style="background-color: #24287E; color: white; padding: 15px; margin: 20px 0; text-align: center; border-radius: 4px;">ADD NEW EVENTS</h2>
    <form method="post" action="AddEventProcess.php" onsubmit="return confirm('Are you sure you want to add this event?')">
        <label for="EventName">Event Name:</label>
        <input type="text" id="EventName" name="EventName" required><br><br>
        <label for="StartDate">Start Date:</label>
        <input type="datetime-local" id="StartDate" name="StartDate" required><br><br>
        <label for="EndDate">End Date:</label>
        <input type="datetime-local" id="EndDate" name="EndDate" required><br><br>
        <button type="submit">Add Event</button>
    </form>
    </div>
</body>
</html>