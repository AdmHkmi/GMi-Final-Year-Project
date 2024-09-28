<?php
include '../../../Database/DatabaseConnection.php';

$sql = "SELECT StudentProfilePicture, StudentName, StudentEmail, StudentID, UserApproval FROM VSStudents ORDER BY UserApproval DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Start the bulk actions form
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
        $studentID = htmlspecialchars($row["StudentID"]);
        echo "<tr>";
        echo "<td align='center'><input type='checkbox' name='selected_users[]' value='$studentID'></td>";
        echo "<td align='center'><div class 'studentpfp'></div><img src='../../../ProfilePicture/" . htmlspecialchars($row["StudentProfilePicture"]) . "' alt='Profile Picture' style='width: 100px; height: 100px;'></td>";
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
    echo "0 results";
}

$conn->close();
?>

<script>
document.getElementById('select-all').onclick = function() {
    var checkboxes = document.getElementsByName('selected_users[]');
    for (var checkbox of checkboxes) {
        checkbox.checked = this.checked;
    }
}
</script>
