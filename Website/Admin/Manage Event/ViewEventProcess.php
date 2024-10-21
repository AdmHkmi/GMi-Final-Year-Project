<?php

include '../../../Database/DatabaseConnection.php';

// Fetch events
$fetch_events_sql = "SELECT EventID, EventName, StartDate, EndDate, IsActive FROM VSEvents";
$events_result = $conn->query($fetch_events_sql);

// Update IsActive column based on current date and event dates, except for "Nomination Result" and "SRC Result"
while ($event = $events_result->fetch_assoc()) {
    if ($event["EventName"] !== "Nomination Result" && $event["EventName"] !== "SRC Result") {
        $current_date = date('Y-m-d H:i:s');
        $start_date = $event["StartDate"];
        $end_date = $event["EndDate"];
        
        // Determine if event is active or not
        $is_active = ($current_date >= $start_date && $current_date <= $end_date) ? 1 : 0;
        
        // Update IsActive column in the database
        $update_status_sql = "UPDATE VSEvents SET IsActive = ? WHERE EventID = ?";
        $stmt = $conn->prepare($update_status_sql);
        $stmt->bind_param("ii", $is_active, $event["EventID"]);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch events again to display updated data
$events_result = $conn->query($fetch_events_sql);

// Display events
if ($events_result->num_rows > 0) {
    while ($event = $events_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($event["EventName"]) . "</td>";
        
        if ($event["EventName"] === "Nomination Result" || $event["EventName"] === "SRC Result") {
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>" . ($event["IsActive"] ? "Shared" : "Not Shared") . "</td>";
        } else {
            // Check for missing dates
            $start_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["StartDate"] : "Not Announced Yet";
            $end_date_display = ($event["StartDate"] && $event["EndDate"]) ? $event["EndDate"] : "Not Announced Yet";
            echo "<td>" . $start_date_display . "</td>";
            echo "<td>" . $end_date_display . "</td>";
            echo "<td>" . ($event["IsActive"] ? "Active" : "Inactive") . "</td>";
        }
        
        echo "<td>";
        
        // Conditionally display the delete button based on event type
        if ($event["EventName"] != "Nomination Vote" && $event["EventName"] != "SRC Vote" && $event["EventName"] != "Nomination Result" && $event["EventName"] != "SRC Result") {
            echo "<form method='post' action='DeleteEventProcess.php' class='inline-form' onsubmit='return confirm(\"Are you sure you want to delete this event?\");'>";
            echo "<input type='hidden' name='deleteEventID' value='" . $event["EventID"] . "'>";
            echo "<button class='button delete-button' type='submit'>Delete</button>";
            echo "</form>";
        }

        // Conditionally display the Reset button based on event type
        if ($event["EventName"] != "Nomination Result" && $event["EventName"] != "SRC Result") {
            echo "<form method='post' action='";
            if ($event["EventName"] === "Nomination Vote") {
                echo "ResetNominationVoteEventProcess.php";
            } elseif ($event["EventName"] === "SRC Vote") {
                echo "ResetSRCVoteEventProcess.php";
            } else {
                echo "ResetEventProcess.php";
            }
            echo "' class='inline-form' onsubmit='return confirm(\"Are you sure you want to reset this event?\");'>";
            echo "<input type='hidden' name='ResetEvent' value='" . $event["EventID"] . "'>";
            echo "<button class='button reset-button' type='submit'>Reset</button>";
            echo "</form>";
        }

        // Display Edit button for each event
        echo "<button onclick='toggleEditRow(" . $event["EventID"] . ")' class='button edit-button'>Edit</button>";

        echo "</td>";
        echo "</tr>";

        // Edit form row for Nomination Result and SRC Result events
        if ($event["EventName"] === "Nomination Result" || $event["EventName"] === "SRC Result") {
            echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>";
            echo "<td colspan='5'>";
            echo "<form method='post' action='EditEventProcess.php'>";
            echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>";
            echo "<label for='IsActive-" . $event["EventID"] . "'>Status:</label>";
            echo "<select id='IsActive-" . $event["EventID"] . "' name='IsActive' class='select-status'>";
            echo "<option value='1'" . ($event["IsActive"] == 1 ? " selected" : "") . ">Shared</option>";
            echo "<option value='0'" . ($event["IsActive"] == 0 ? " selected" : "") . ">Not Shared</option>";
            echo "</select><br><br>";
            echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        } 
        elseif ($event["EventName"] === "Nomination Vote" || $event["EventName"] === "SRC Vote") {
            echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>";
            echo "<td colspan='5'>";
            echo "<form method='post' action='EditEventProcess.php'>";
            echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>";
            echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
            echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . str_replace(" ", "T", $event["StartDate"]) . "' required><br><br>";
            echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
            echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . str_replace(" ", "T", $event["EndDate"]) . "' required><br><br>";
            echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        } else {
            // Edit form row for other events (hidden initially)
            echo "<tr id='edit-row-" . $event["EventID"] . "' style='display:none;'>";
            echo "<td colspan='5'>";
            echo "<form method='post' action='EditEventProcess.php'>";
            echo "<input type='hidden' name='EventID' value='" . $event["EventID"] . "'>";
            echo "<label for='EventName-" . $event["EventID"] . "'>Event Name:</label>";
            echo "<input class='input-text' type='text' id='EventName-" . $event["EventID"] . "' name='EventName' value='" . htmlspecialchars($event["EventName"]) . "' required><br><br>";
            echo "<label for='StartDate-" . $event["EventID"] . "'>Start Date:</label>";
            echo "<input type='datetime-local' id='StartDate-" . $event["EventID"] . "' name='StartDate' value='" . str_replace(" ", "T", $event["StartDate"]) . "' required><br><br>";
            echo "<label for='EndDate-" . $event["EventID"] . "'>End Date:</label>";
            echo "<input type='datetime-local' id='EndDate-" . $event["EventID"] . "' name='EndDate' value='" . str_replace(" ", "T", $event["EndDate"]) . "' required><br><br>";
            echo "<button class='button save-button' type='submit' onclick=\"return confirm('Are you sure you want to save this changes?')\">Save Changes</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    }
} else {
    echo "<tr><td colspan='5'>No events found.</td></tr>";
}

$conn->close();
?>

<script>
    // JavaScript function to toggle visibility of edit form row
    function toggleEditRow(eventID) {
        var editRow = document.getElementById('edit-row-' + eventID);
        if (editRow.style.display === 'none') {
            editRow.style.display = 'table-row';
        } else {
            editRow.style.display = 'none';
        }
    }
</script>
