<?php

// Retrieve the token from the submitted form
$token = $_POST["token"];

// Hash the token using SHA-256 to prepare it for database comparison
$token_hash = hash("sha256", $token);

// Include the database connection file
include '../../Database/DatabaseConnection.php';

// Prepare a SQL statement to select the user based on the hashed reset token
$sql = "SELECT * FROM VSStudents
        WHERE ResetPasswordToken = ?";

$stmt = $conn->prepare($sql); // Use the established database connection

// Bind the hashed token to the prepared statement
$stmt->bind_param("s", $token_hash);

// Execute the statement
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Fetch the user details as an associative array
$user = $result->fetch_assoc();

// If no user is found with the provided token, terminate the script
if ($user === null) {
    die("token not found");
}

// Check if the token has expired, terminate the script if it has
if (strtotime($user["ResetPasswordTokenExpired"]) <= time()) {
    die("token has expired");
}

// Check if the new password and confirmation password match
if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Store the password as plain text (not recommended for production)
$password_plain = $_POST["password"];

// Prepare an SQL statement to update the user's password and clear the reset token
$sql = "UPDATE VSStudents
        SET StudentPassword = ?,
            ResetPasswordToken = NULL,
            ResetPasswordTokenExpired = NULL
        WHERE StudentID = ?";

$stmt = $conn->prepare($sql); // Use the established database connection

// Bind the new password and the user's ID to the prepared statement
$stmt->bind_param("ss", $password_plain, $user["StudentID"]);

// Execute the statement to update the password
$stmt->execute();

// Inform the user that the password has been updated and redirect them
echo "<script>alert('Password updated. You can now login.'); window.location.href = '../../index.html'; // Redirect to the index page</script>";
?>
