<?php

$token = $_POST["token"];

$token_hash = hash("sha256", $token);

include '../../Database/DatabaseConnection.php';

$sql = "SELECT * FROM VSStudents
        WHERE reset_token_hash = ?";

$stmt = $conn->prepare($sql); // Changed $mysqli to $conn

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Storing the password as plain text (not recommended for production)
$password_plain = $_POST["password"];

$sql = "UPDATE VSStudents
        SET StudentPassword = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE StudentID = ?";

$stmt = $conn->prepare($sql); // Changed $mysqli to $conn

$stmt->bind_param("ss", $password_plain, $user["StudentID"]);

$stmt->execute();

echo "Password updated. You can now login.";
header("Refresh:5; url=../../index.html");

?>
