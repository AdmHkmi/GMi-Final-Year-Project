<?php
session_start();
include('../../../Database/DatabaseConnection.php');

if (isset($_SESSION['StudentID'])) {
    $studentID = $_SESSION['StudentID'];

    // SQL query to get the QRCode file path
    $sql = "SELECT QRCode FROM VSVote WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $qrCodeFile = '../../../Images/QRCode/' . $row['QRCode'];

        // Check if the QR code file exists
        if (file_exists($qrCodeFile)) {
            // Set headers to initiate a download
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename="' . basename($qrCodeFile) . '"');
            header('Content-Length: ' . filesize($qrCodeFile));
            readfile($qrCodeFile);
            exit;
        } else {
            echo '<script>alert("QR code file does not exist."); window.location.href = "../Home Page/UserHomepage.php";</script>';
        }
    } else {
        echo '<script>alert("No QR code found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
    }
} else {
    echo '<script>alert("Please log in to download your QR code."); window.location.href = "../../../index.html";</script>';
}

// Close the database connection
$conn->close();
?>
