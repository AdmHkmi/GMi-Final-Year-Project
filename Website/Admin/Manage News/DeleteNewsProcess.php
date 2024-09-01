<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newsID = $_POST['deleteNewsID'];
    $stmt = $conn->prepare("DELETE FROM VSNews WHERE NewsID = ?");
    $stmt->bind_param("i", $newsID);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: ManageNews.php");
exit();
?>
