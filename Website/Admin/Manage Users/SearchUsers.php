<?php
// Start session at the beginning
session_start();

include '../../../Database/DatabaseConnection.php';

// Prepare SQL statement to search for students
$sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID, UserApproval FROM VSStudents WHERE 1=1";

// Add search term if provided
$searchTerm = null;
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = trim($_POST['search']); // trim whitespace
    $searchTerm = "%" . $search . "%"; // Wildcard search
    $sql .= " AND (StudentName LIKE ? OR StudentID LIKE ?)";
}

// Add user status filter if provided
if (isset($_POST['user_status'])) {
    if ($_POST['user_status'] == 'active') {
        $sql .= " AND UserApproval = 1";
    } elseif ($_POST['user_status'] == 'inactive') {
        $sql .= " AND UserApproval = 0";
    }
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('SQL prepare() failed: ' . htmlspecialchars($conn->error));
}

// Bind the parameters if search term is provided
if ($searchTerm !== null) {
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<form action='BulkUserProcess.php' method='post'>";
    echo "<input type='submit' class='Approve-Button' name='bulk_action' value='Approve Selected' onclick=\"return confirm('Are you sure you want to approve the desired user?')\">";
    echo "<input type='submit' class='Unapprove-Button' name='bulk_action' value='Unapprove Selected' onclick=\"return confirm('Are you sure you want to unapprove the desired user?')\">";
    echo "<input type='submit' class='Delete-Button' name='bulk_action' value='Delete Selected' onclick=\"return confirm('Are you sure you want to delete the desired user?')\">";
    echo "<br><br><br>";
    echo "<table border='1' align='center'>";
    echo "<tr>";
    echo "<th>Select/Deselect all<br><input type='checkbox' id='select-all'></th>";
    echo "<th>Profile Picture</th>";
    echo "<th>Student Name</th>";
    echo "<th>Student Email</th>";
    echo "<th>StudentID</th>";
    echo "<th>User Status</th>";
    echo "</tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td align='center'><input type='checkbox' name='selected_users[]' value='" . $row["StudentID"] . "'></td>";
        echo "<td align='center'><img src='../../../ProfilePicture/" . $row["StudentProfilePicture"] . "' alt='Profile Picture' style='width: 100px; height: 100px;'></td>";
        echo "<td>" . htmlspecialchars($row["StudentName"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["StudentEmail"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["StudentID"]) . "</td>";
        echo "<td>";
        if ($row["UserApproval"]) {
            echo "<span style='color: green;'>Active</span>";
        } else {
            echo "<span style='color: red;'>Inactive</span>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</form>";
} else {
    if ($searchTerm !== null) {
        echo "<br><center>No user matching your search.</center>";
    } else {
        echo "<center><h3>No " . ($_POST['user_status'] == 'active' ? "active" : "inactive") . " users found</h3></center>";
    }
}

$stmt->close();
$conn->close();
?>

<script>
document.getElementById('select-all').onclick = function() {
    var checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
};
</script>
