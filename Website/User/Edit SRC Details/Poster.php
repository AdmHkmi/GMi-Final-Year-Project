<?php
session_start();
include('../../../phpqrcode/qrlib.php');
require('../../../fpdf/fpdf.php');
include('../../../Database/DatabaseConnection.php');

class PDF extends FPDF {
    function DrawBorder() {
        $this->SetLineWidth(1);
        $this->Rect(5, 5, $this->GetPageWidth() - 10, $this->GetPageHeight() - 10);
    }

    function Header() {}
    function Footer() {
        $this->DrawBorder();
    }
}

if (isset($_SESSION['StudentID'])) {
    $studentID = $_SESSION['StudentID'];
    $tempDir = "../../../Images/QRCode/";

    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $checkSql = "SELECT QRCode, Poster FROM VSVote WHERE StudentID = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $studentID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $existingData = $checkResult->fetch_assoc();
        if (!empty($existingData['QRCode']) && !empty($existingData['Poster'])) {
            $pdfFilePath = $tempDir . $existingData['Poster'];
            echo '<script>alert("Poster already generated."); window.location.href = "' . $pdfFilePath . '";</script>';
            exit;
        }
    }

    $codeContents = "http://localhost/Voting System/Website/User/Vote Casting/ViewSRCDetails.php?StudentID=" . urlencode($studentID);
    $fileName = $studentID . '_' . md5($codeContents) . '.png';
    $pngAbsoluteFilePath = $tempDir . $fileName;

    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($codeContents, $pngAbsoluteFilePath);
    }

    $sql = "SELECT S.StudentProfilePicture, S.StudentName, V.Manifesto FROM VSStudents S JOIN VSVote V ON S.StudentID = V.StudentID WHERE S.StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $profilePicture = '../../../ProfilePicture/' . $userData['StudentProfilePicture'];
        $studentName = $userData['StudentName'];
        $manifesto = $userData['Manifesto'];

        $pdf = new PDF();
        $pdf->AddPage();
        
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');
        
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 10, 'SRC Profile Poster', 0, 1, 'C');
        $pdf->Ln(10);

        $profilePictureX = ($pdf->GetPageWidth() - 50) / 2;
        if (file_exists($profilePicture)) {
            $pdf->Image($profilePicture, $profilePictureX, 40, 50, 50);
        }

        $pdf->SetY(100);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Student Name:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $studentName, 0, 1);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Student ID:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $studentID, 0, 1);
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Manifesto:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $manifesto);
        $pdf->Ln(10);

        $pdf->SetY(-70);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Scan here to vote', 0, 1, 'C');

        $pdf->SetY(-60);
        $qrCodeWidth = 50;
        $qrCodeX = ($pdf->GetPageWidth() - $qrCodeWidth) / 2;
        $pdf->Image($pngAbsoluteFilePath, $qrCodeX, $pdf->GetY(), $qrCodeWidth);

        $pdfFileName = $studentID . '_Poster.pdf';
        $pdfFilePath = $tempDir . $pdfFileName;
        $pdf->Output('F', $pdfFilePath);

        $updateSql = "UPDATE VSVote SET QRCode = ?, Poster = ? WHERE StudentID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sss", $fileName, $pdfFileName, $studentID);
        $updateStmt->execute();

        echo '<script>alert("Poster generated successfully!"); window.location.href = "' . $pdfFilePath . '";</script>';
    } else {
        echo '<script>alert("No record found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
    }
} else {
    echo '<script>alert("Please log in to generate your QR code."); window.location.href = "../../index.html";</script>';
}

$conn->close();
?>
