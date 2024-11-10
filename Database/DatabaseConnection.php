<?php
// Database connection details
$servername = "gmisrce-election.my";
$username = "gmisrcee";
$password = "efd;AGv#96I79M";
$dbname = "gmisrcee_VotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
