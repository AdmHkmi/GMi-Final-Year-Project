<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to delete all entries from VSApprovedSRC table
$sql_delete_src = "DELETE FROM VSCurrentSRC";

if ($conn->query($sql_delete_src) === TRUE) {
    echo '<script>alert("All approved SRC entries have been deleted."); window.location.href = "ManageResult.php";</script>';
} else {
    echo "Error deleting from VSApprovedSRC: " . $conn->error;
}

$conn->close();
?>
