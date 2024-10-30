<?php
session_start(); // Start the session to access session variables

include('../../../phpqrcode/qrlib.php');
require('../../../fpdf/fpdf.php'); // Include FPDF library
include('../../../Database/DatabaseConnection.php'); // Include database connection for user data

// Custom FPDF class to add a border to all pages
class PDF extends FPDF {
    // Function to draw the border
    function DrawBorder() {
        // Draw a border around the entire content
        $this->SetLineWidth(1);
        $this->Rect(5, 5, $this->GetPageWidth() - 10, $this->GetPageHeight() - 10); // Border with margins
    }

    // Page header
    function Header() {
        // Optional: Add a header if needed
    }

    // Page footer
    function Footer() {
        $this->DrawBorder(); // Draw the border in the footer
    }
}

// Check if the user is logged in and has a StudentID in the session
if (isset($_SESSION['StudentID'])) {
    // Set up the directory for storing QR codes
    $tempDir = "../../../Images/QRCode/";

    // Ensure the directory exists or create it if not
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true); // Create directory with write permissions
    }

    // Use the student ID as part of the filename
    $studentID = $_SESSION['StudentID'];
    $codeContents = "http://localhost/Voting System/Website/User/Vote Casting/VoteCastingPage.php?search=" . urlencode($studentID);
    $fileName = $studentID . '_' . md5($codeContents) . '.png';

    $pngAbsoluteFilePath = $tempDir . $fileName;

    // Generate QR code image if it doesn't already exist
    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($codeContents, $pngAbsoluteFilePath);
    }

    // Fetch student data from the database
    $sql = "SELECT S.StudentProfilePicture, S.StudentName, V.Manifesto 
            FROM VSStudents S
            JOIN VSVote V ON S.StudentID = V.StudentID
            WHERE S.StudentID = ?"; // Ensure we fetch data only for the logged-in user
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $profilePicture = '../../../ProfilePicture/' . $userData['StudentProfilePicture'];
        $studentName = $userData['StudentName'];
        $manifesto = $userData['Manifesto'];

        // Create PDF with custom class
        $pdf = new PDF();
        $pdf->AddPage();
        
        // Set background color
        $pdf->SetFillColor(255, 255, 255); // White background
        $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F'); // Fill background
        
        // Set Title
        $pdf->SetFont('Arial', 'B', 24); // Larger font size for the title
        $pdf->Cell(0, 10, 'SRC Profile Poster', 0, 1, 'C');
        $pdf->Ln(10);
        
        // Center the profile picture
        $profilePictureX = ($pdf->GetPageWidth() - 50) / 2; // Centering calculation
        if (file_exists($profilePicture)) {
            $pdf->Image($profilePicture, $profilePictureX, 40, 50, 50); // Adjust size and position
        }

        // Position for content below the profile picture
        $pdf->SetY(100); // Adjust Y position below the profile picture
        $pdf->SetFont('Arial', 'B', 14); // Font size for section titles
        
        // Name
        $pdf->Cell(0, 10, 'Student Name:', 0, 1);
        $pdf->SetFont('Arial', '', 12); // Font size for content
        $pdf->Cell(0, 10, $studentName, 0, 1);
        $pdf->Ln(5);

        // Student ID
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Student ID:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $studentID, 0, 1);
        $pdf->Ln(5);

        // Manifesto
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Manifesto:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $manifesto);
        $pdf->Ln(10);

        // Add QR Code with "Scan here to vote" instruction at the bottom center
        $pdf->SetY(-70); // Position for "Scan here to vote" text
        $pdf->SetFont('Arial', 'B', 14); // Instruction font size
        $pdf->Cell(0, 10, 'Scan here to vote', 0, 1, 'C');
        
        $pdf->SetY(-60); // Set position for QR code
        $qrCodeWidth = 50; // Set QR code width
        $qrCodeX = ($pdf->GetPageWidth() - $qrCodeWidth) / 2; // Centering X position
        $pdf->Image($pngAbsoluteFilePath, $qrCodeX, $pdf->GetY(), $qrCodeWidth); // Add QR Code

        // Output PDF
        $pdfFileName = "../../../Images/QRCode/" . $studentID . '_Poster.pdf';
        $pdf->Output('F', $pdfFileName); // Save PDF to server

        // Get the base file names from the full paths
        $qrCodeFileName = basename($pngAbsoluteFilePath);
        $pdfFileNameOnly = basename($pdfFileName);

        // Removed SQL update for QR code and poster paths
        
        // Show download link
        echo '<script>alert("Poster generated successfully!"); window.location.href = "' . $pdfFileName . '";</script>';
        
    } else {
        echo '<script>alert("No record found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
    }
} else {
    // Redirect to login page if the user is not logged in
    echo '<script>alert("Please log in to generate your QR code."); window.location.href = "../../index.html";</script>';
}

// Close database connection
$conn->close();
?>
