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
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php">
                <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo">
            </a>
        </div>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php">
                <button class="back-button"><i class='fas fa-arrow-left'></i></button>
            </a>
        </div>
    </header>
    <div class="Container">
        <h1>Manage Events</h1>
        <div class="instructions">
            <p>Welcome to the Manage Events page! Here, you can view existing events, add new events, edit news, or delete events as needed.</p>
        </div>
        <div class="header-section">
            <h2>Existing Events</h2>
        </div>
        <p>To manage your events, you can modify their details by clicking 'Edit,' reset them using the 'Reset' button, or remove an event from the schedule with the 'Delete' option.</p>
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
        <br>
        <div class="header-section">
            <h2>ADD NEW EVENTS</h2>
        </div>
        <p>To add a new event to the schedule, please complete the form below with the event details.</p>
        <form method="post" action="AddEventProcess.php" onsubmit="return confirm('Are you sure you want to add this event?')">
            <label for="EventName">Event Name:</label>
            <input type="text" id="EventName" name="EventName" required><br><br>
            <label for="StartDate">Start Date:</label>
            <input type="datetime-local" id="StartDate" name="StartDate" required><br><br>
            <label for="EndDate">End Date:</label>
            <input type="datetime-local" id="EndDate" name="EndDate" required><br><br>
            <div class="add-event-button"><button type="submit">Add Event</button></div>
        </form>
    </div>
</body>
</html>
