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
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php">
                <button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button -->
            </a>
        </div>
    </header>
    <h1>Manage Events</h1>
    <div class="instructions">
        <p>Welcome to the Manage Events page! Here, you can view existing events, add new events, edit news, or delete events as needed.</p>
    </div>
    <div class="Container">
        <div class="header-section">
            <h2>Existing Events</h2> <!-- Section header for existing events -->
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
            // Check if there are events to display
            if ($events_result->num_rows > 0) {
                // Loop through and display each event
                while ($event = $events_result->fetch_assoc()) {
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
                        echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>"; // Save changes button
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    } elseif ($event["EventName"] === "Nomination Vote" || $event["EventName"] === "SRC Vote") {
                        // Create the edit form row for voting events
                        echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>"; // Hidden row
                        echo "<td colspan='5'>";
                        echo "<form method='post' action='EditEventProcess.php'>";
                        echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                        echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
                        echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . str_replace(" ", "T", $event["StartDate"]) . "' required><br><br>"; // Input for start date
                        echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
                        echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . str_replace(" ", "T", $event["EndDate"]) . "' required><br><br>"; // Input for end date
                        echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>"; // Save changes button
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    } else {
                        // Create the edit form row for regular events
                        echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>"; // Hidden row
                        echo "<td colspan='5'>";
                        echo "<form method='post' action='EditEventProcess.php'>";
                        echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>"; // Hidden input for event ID
                        echo "<label for='EventName-" . $event["EventID"] . "'>Event Name:</label>";
                        echo "<input class='input-text' type='text' id='EventName-" . $event["EventID"] . "' name='EventName' value='" . htmlspecialchars($event["EventName"]) . "' required><br><br>"; // Input for event name
                        echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
                        echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . str_replace(" ", "T", $event["StartDate"]) . "' required><br><br>"; // Input for start date
                        echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
                        echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . str_replace(" ", "T", $event["EndDate"]) . "' required><br><br>"; // Input for end date
                        echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>"; // Save changes button
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='5'>No events found.</td></tr>"; // Display message if no events are found
            }
            // Close the database connection
            $conn->close();
            ?>
        </table>
        <br>
        <div class="header-section">
            <h2>ADD NEW EVENTS</h2> <!-- Section header for adding new events -->
        </div>
        <p>To add a new event to the schedule, please complete the form below with the event details.</p>
        <form method="post" action="AddEventProcess.php" onsubmit="return confirm('Are you sure you want to add this event?')">
            <label for="EventName">Event Name:</label>
            <input type="text" id="EventName" name="EventName" required><br><br> <!-- Input for new event name -->
            <label for="StartDate">Start Date:</label>
            <input type="datetime-local" id="StartDate" name="StartDate" required><br><br> <!-- Input for new event start date -->
            <label for="EndDate">End Date:</label>
            <input type="datetime-local" id="EndDate" name="EndDate" required><br><br> <!-- Input for new event end date -->
            <div class="add-event-button"><button type="submit">Add Event</button></div> <!-- Button to add the event -->
        </form>
    </div>

    <script>
        // JavaScript function to toggle visibility of the edit form row
        function toggleEditRow(eventID) {
            var editRow = document.getElementById('edit-row-' + eventID);
            if (editRow.style.display === 'none') {
                editRow.style.display = 'table-row'; // Show the edit row
            } else {
                editRow.style.display = 'none'; // Hide the edit row
            }
        }
    </script>
</body>
</html>
