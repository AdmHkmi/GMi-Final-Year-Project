<?php

// Retrieve the token from the URL parameters
$token = $_GET["token"];

// Hash the token using SHA-256 to match with the hashed token in the database
$token_hash = hash("sha256", $token);

// Include the database connection file
include '../../Database/DatabaseConnection.php';

// Prepare a SQL statement to select the user based on the hashed reset token
$sql = "SELECT * FROM VSStudents
        WHERE ResetPasswordToken = ?";

$stmt = $conn->prepare($sql);

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

include 'ResetPassword.html'
?>