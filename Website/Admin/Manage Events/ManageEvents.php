<?php
// Start the session
session_start();
// Set the timezone for date and time operations
date_default_timezone_set('Asia/Kuala_Lumpur'); 
// Include the database connection file
include '../../../Database/DatabaseConnection.php';
// SQL query to fetch all events from the database
$fetch_events_sql = "SELECT EventID, EventName, StartDate, EndDate, IsActive FROM VSEvents";
$events_result = $conn->query($fetch_events_sql);

// Update the IsActive status for each event based on the current date
while ($event = $events_result->fetch_assoc()) {
    // Skip "Nomination Result" and "SRC Result" events for status updates
    if ($event["EventName"] !== "Nomination Result" && $event["EventName"] !== "SRC Result") {
        $current_date = date('Y-m-d H:i:s'); // Get the current date and time
        $start_date = $event["StartDate"]; // Get the event start date
        $end_date = $event["EndDate"]; // Get the event end date
        // Determine if the event is active based on the current date
        $is_active = ($current_date >= $start_date && $current_date <= $end_date) ? 1 : 0;        
        // Prepare and execute the update statement to change the IsActive status
        $update_status_sql = "UPDATE VSEvents SET IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_status_sql);
        $stmt->bind_param("ii", $is_active, $event["EventID"]); // Bind parameters
        $stmt->execute(); // Execute the update
        $stmt->close(); // Close the statement
    }
}

// Fetch the events again to display the updated data
$events_result = $conn->query($fetch_events_sql);
?>

<html>
<head>
    <title>Manage Events</title>
    <link rel="stylesheet" href="ManageEvents.css"> <!-- Include CSS for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Include Font Awesome for icons -->
</head>
<body>
    <div class="background-image"></div> <!-- Background image -->
    <header class="header">
    <div class="logo-container">
        <a href="../Home Page/AdminHomepage.php">
            <img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"> <!-- Logo -->
        </a>
    </div>
    <nav class="navbar">
        <ul>
            <li><a href="ManageEvents.php">Manage Events</a></li>
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
    <h1>Manage Events</h1>
    <div class="instructions">
        <p>Welcome to the Manage Events page! Here, you can view existing events, add new events, edit events, or delete events as needed.</p>
    </div>
    <div class="container">
        <div class="header-section">
            <h2>Add New Event</h2>
        </div>
        <p>In this section, you can add new events to the system. Fill in the event name, start date, and end date to create an event. Once added, the event will be available for management, allowing you to monitor its status and make any necessary adjustments.</p>
        <form method="post" action="AddEventProcess.php">
            <label for="EventName">Event Name:</label>
            <input type="text" id="EventName" name="EventName" required>
            <label for="StartDate">Start Date:</label>
            <input type="datetime-local" id="StartDate" name="StartDate" required>
            <label for="EndDate">End Date:</label>
            <input type="datetime-local" id="EndDate" name="EndDate" required>
            <button type="submit" class="button add-button">Add Event</button>
        </form>
    </div>

    <div class="Container">
        <div class="header-section">
            <h2>Main Events</h2> <!-- Section header for main events -->
        </div>
        <p>Main events play a vital role in the voting process. In this section, you'll find important events as listed below. The status of these events is key: if they're inactive, students can't cast their votes, and the voting process stops. Additionally, if the 'Nomination Result' or 'SRC Result' is inactive, students won't be able to see the results, but when they're active, everyone can view them. You can also utilize the "Send Email" button to notify students about the selected event.</p>
        <div class="warning">REMEMBER!!! Everytime you reset "Nomination Vote" or "SRC Vote", it will reset the Candidate Vote Count and Student Vote Limit.</div>
        <table>
            <tr>
                <th>Event Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            $main_events_count = 0; // Counter for main events
            // Check for main events
            while ($event = $events_result->fetch_assoc()) {
                if (in_array($event["EventName"], ["Nomination Vote", "Nomination Result", "SRC Vote", "SRC Result"])) {
                    $main_events_count++;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($event["EventName"]) . "</td>"; // Display event name
                    // Handle display logic for "Nomination Result" and "SRC Result"
                    if ($event["EventName"] === "Nomination Result" || $event["EventName"] === "SRC Result") {
                        echo "<td>-</td>";
                        echo "<td>-</td>";
                        echo "<td>" . ($event["IsActive"] ? "Shared" : "Not Shared") . "</td>";
                    } else {
                        // For regular events, display start and end dates
                        $start_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["StartDate"] : "Not Announced Yet";
                        $end_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["EndDate"] : "Not Announced Yet";
                        echo "<td>" . $start_date_display . "</td>";
                        echo "<td>" . $end_date_display . "</td>";
                        echo "<td>" . ($event["IsActive"] ? "Active" : "Inactive") . "</td>";
                    }
                    echo "<td>";
                    // Display the Send Email button
                    echo "<form method='post' action='SendEmail.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to send an email for this event?\");'>";
                    echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>";
                    echo "<button class='button email-button' type='submit'>Send Email</button>";
                    echo "</form>";
                    // Conditionally display the delete button for events that can be deleted
                    if ($event["EventName"] != "Nomination Vote" && $event["EventName"] != "SRC Vote" && $event["EventName"] != "Nomination Result" && $event["EventName"] != "SRC Result") {
                        echo "<form method='post' action='DeleteEventProcess.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to delete this event?\");'>";
                        echo "<input type='hidden' name='deleteEventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                        echo "<button class='button delete-button' type='submit'>Delete</button>"; // Delete button
                        echo "</form>";
                    }
                    // Conditionally display the Reset button for events that can be reset
                    if ($event["EventName"] != "Nomination Result" && $event["EventName"] != "SRC Result") {
                        echo "<form method='post' action='";
                        // Determine the action based on the event name
                        if ($event["EventName"] === "Nomination Vote") {
                            echo "ResetNominationVoteEventProcess.php";
                        } elseif ($event["EventName"] === "SRC Vote") {
                            echo "ResetSRCVoteEventProcess.php";
                        } else {
                            echo "ResetEventProcess.php";
                        }
                        echo "' class='inline-form' onsubmit='return confirm(\"Are you sure you want to reset this event?\");'>";
                        echo "<input type='hidden' name='ResetEvent' value='" . $event["EventID"] . "'>"; // Hidden input for reset event ID
                        echo "<button class='button reset-button' type='submit'>Reset</button>"; // Reset button
                        echo "</form>";
                    }
                    // Display the Edit button to toggle the edit form
                    echo "<button onclick='toggleEditRow(" . $event["EventID"] . ")' class='button edit-button'>Edit</button>";
                    echo "</td>"; // Close table data
                    echo "</tr>"; // Close table row
                    // Create the edit form row for "Nomination Result" and "SRC Result" events
                    if ($event["EventName"] === "Nomination Result" || $event["EventName"] === "SRC Result") {
                        echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>"; // Hidden row
                        echo "<td colspan='5'>"; // Span across all columns
                        echo "<form method='post' action='EditEventProcess.php'>";
                        echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                        echo "<label for='IsActive-" . $event["EventID"] . "'>Status:</label>";
                        echo "<select id='IsActive-" . $event["EventID"] . "' name='IsActive' class='select-status'>"; // Dropdown for status
                        echo "<option value='1'" . ($event["IsActive"] == 1 ? " selected" : "") . ">Shared</option>";
                        echo "<option value='0'" . ($event["IsActive"] == 0 ? " selected" : "") . ">Not Shared</option>";
                        echo "</select><br><br>";
                        echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save these changes?')\">Save Changes</button>"; // Save changes button
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    } else {
                        // Create the edit form row for other events
                        echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>"; // Hidden row
                        echo "<td colspan='5'>";
                        echo "<form method='post' action='EditEventProcess.php'>";
                        echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                        echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
                        echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . date('Y-m-d\TH:i', strtotime($event["StartDate"])) . "' required><br>"; // Start date input
                        echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
                        echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . date('Y-m-d\TH:i', strtotime($event["EndDate"])) . "' required><br><br>"; // End date input
                        echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save these changes?')\">Save Changes</button>"; // Save changes button
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            }
            // Check if there are no main events
            if ($main_events_count == 0) {
                echo "<tr><td colspan='5'>No main events available.</td></tr>"; // Message for no main events
            }
            ?>
        </table>
    </div>

    <div class="Container">
        <div class="header-section">
            <h2>Other Events</h2> <!-- Section header for other events -->
        </div>
        <p>The Other Events section displays all events that are not classified as main events. Here, you can view additional events. You have the ability to manage these events.</p>
        <table>
            <tr>
                <th>Event Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            // Reset the events result set again to fetch other events
            $events_result = $conn->query($fetch_events_sql);
            $other_events_count = 0; // Counter for other events
            // Check for other events
            while ($event = $events_result->fetch_assoc()) {
                if (!in_array($event["EventName"], ["Nomination Vote", "Nomination Result", "SRC Vote", "SRC Result"])) {
                    $other_events_count++;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($event["EventName"]) . "</td>"; // Display event name
                    $start_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["StartDate"] : "Not Announced Yet";
                    $end_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["EndDate"] : "Not Announced Yet";
                    echo "<td>" . $start_date_display . "</td>";
                    echo "<td>" . $end_date_display . "</td>";
                    echo "<td>" . ($event["IsActive"] ? "Active" : "Inactive") . "</td>";
                    echo "<td>";
                    // Display the Send Email button
                    echo "<form method='post' action='SendEmail.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to send an email for this event?\");'>";
                    echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>";
                    echo "<button class='button email-button' type='submit'>Send Email</button>";
                    echo "</form>";
                    // Display the delete button
                    echo "<form method='post' action='DeleteEventProcess.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to delete this event?\");'>";
                    echo "<input type='hidden' name='deleteEventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                    echo "<button class='button delete-button' type='submit'>Delete</button>"; // Delete button
                    echo "</form>";
                    // Display the reset button
                    echo "<form method='post' action='ResetEventProcess.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to reset this event?\");'>";
                    echo "<input type='hidden' name='ResetEvent' value='" . $event["EventID"] . "'>"; // Hidden input for reset event ID
                    echo "<button class='button reset-button' type='submit'>Reset</button>"; // Reset button
                    echo "</form>";
                    // Display the Edit button
                    echo "<button onclick='toggleEditRow(" . $event["EventID"] . ")' class='button edit-button'>Edit</button>";
                    echo "</td>"; // Close table data
                    echo "</tr>"; // Close table row
                    // Create the edit form row for other events
                    echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>"; // Hidden row
                    echo "<td colspan='5'>";
                    echo "<form method='post' action='EditEventProcess.php'>";
                    echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                    echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
                    echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . date('Y-m-d\TH:i', strtotime($event["StartDate"])) . "' required><br>"; // Start date input
                    echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
                    echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . date('Y-m-d\TH:i', strtotime($event["EndDate"])) . "' required><br><br>"; // End date input
                    echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save these changes?')\">Save Changes</button>"; // Save changes button
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            // Check if there are no other events
            if ($other_events_count == 0) {
                echo "<tr><td colspan='5'>No other events available.</td></tr>"; // Message for no other events
            }
            ?>
        </table>
    </div>
    <script>
        // Function to toggle the visibility of the edit row
        function toggleEditRow(eventId) {
            var editRow = document.getElementById('edit-row-' + eventId);
            if (editRow.style.display === 'none') {
                editRow.style.display = 'table-row'; // Show the edit row
            } else {
                editRow.style.display = 'none'; // Hide the edit row
            }
        }
    </script>
</body>
</html>
