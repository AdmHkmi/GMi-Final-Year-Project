<?php

// Retrieve the token from the submitted form
$token = $_POST["token"] ?? null;

// Check if the token is provided
if ($token === null) {
    die("No token provided.");
}

// Hash the token using SHA-256 to prepare it for database comparison
$token_hash = hash("sha256", $token);

// Include the database connection file
include '../../Database/DatabaseConnection.php';

// Prepare a SQL statement to select the user based on the hashed reset token
$sql = "SELECT * FROM VSStudents WHERE ResetPasswordToken = ?";
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
    die("Token not found.");
}

// Check if the token has expired, terminate the script if it has
if (strtotime($user["ResetPasswordTokenExpired"]) <= time()) {
    die("Token has expired.");
}

// Check if the new password and confirmation password match
$password = $_POST["password"] ?? null;
$password_confirmation = $_POST["password_confirmation"] ?? null;

if ($password === null || $password_confirmation === null) {
    die("Please provide both password fields.");
}

if ($password !== $password_confirmation) {
    die("Passwords must match.");
}

// Hash the new password before storing (important for security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare an SQL statement to update the user's password and clear the reset token
$sql = "UPDATE VSStudents
        SET StudentPassword = ?,
            ResetPasswordToken = NULL,
            ResetPasswordTokenExpired = NULL
        WHERE StudentID = ?";

$stmt = $conn->prepare($sql); // Use the established database connection

// Bind the new password and the user's ID to the prepared statement
$stmt->bind_param("ss", $hashed_password, $user["StudentID"]);

// Execute the statement to update the password
if ($stmt->execute()) {
    // Inform the user that the password has been updated and redirect them
    echo "<script>alert('Password updated. You can now login.'); window.location.href = '../../index.html';</script>";
} else {
    // Handle any errors that occur during the update
    echo "<script>alert('An error occurred while updating the password. Please try again.'); window.location.href = '../../index.html';</script>";
}

// Close the statement
$stmt->close();

// Close database connection
$conn->close();
?>
