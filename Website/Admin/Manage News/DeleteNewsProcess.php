<?php
session_start();

include '../../../Database/DatabaseConnection.php';

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
