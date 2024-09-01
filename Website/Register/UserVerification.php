<?php
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

if (isset($_GET['email']) && isset($_GET['token'])) {
    $StudentEmail = $_GET['email'];
    $token = $_GET['token'];

    // Verify the token and update UserApproval
    $sql = "SELECT verificationToken FROM VSStudents WHERE StudentEmail = ? AND verificationToken = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $StudentEmail, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Token is valid, update UserApproval
        $updateSql = "UPDATE VSStudents SET UserApproval = true, verificationToken = NULL WHERE StudentEmail = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $StudentEmail);

        if ($updateStmt->execute()) {
            echo '<script>alert("Account verified successfully!"); window.location.href = "../../index.html";</script>';
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $updateStmt->close();
    } else {
        echo '<script>alert("Invalid or expired token."); window.location.href = "../../index.html";</script>';
    }

    $stmt->close();
}

$conn->close();
?>
