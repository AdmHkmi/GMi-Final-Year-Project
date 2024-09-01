<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "VotingSystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// Check if the session variable is set
if (isset($_SESSION['StudentID'])) {
    // Get the logged-in username from the session
    $loggedInUser = $_SESSION['StudentID']; // Assuming the username is stored in a session variable

    // Check if the form was submitted for profile update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data
        $StudentName = $_POST['StudentName'];
        $Manifesto = $_POST['Manifesto'];

        // Validate input (you can add more specific validation as needed)
        if (empty($StudentName) || empty($Manifesto)) {
            echo '<script>alert("Please fill in all required fields."); window.location.href = "EditSRCDetailsPage.php";</script>';
            exit; // Exit script if any field is empty
        }

        // Initialize upload file path
        $uploadFile = $_POST['currentProfilePicture']; // Default to current profile picture

        // Check if a new profile picture file is uploaded
        if (!empty($_FILES['ProfilePicture']['name'])) {
            $profilePicture = $_FILES['ProfilePicture'];

            // Validate file type and size
            $allowedTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/bmp', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($profilePicture['type'], $allowedTypes)) {
                echo '<script>alert("Invalid file type. Please upload a valid image file."); window.location.href = "EditSRCDetailsPage.php";</script>';
                exit;
            }

            if ($profilePicture['size'] > $maxFileSize) {
                echo '<script>alert("File size exceeds 5MB limit."); window.location.href = "EditSRCDetailsPage.php";</script>';
                exit;
            }

            // Handle file upload
            if ($profilePicture['error'] == UPLOAD_ERR_OK) {
                $uploadDir = '../../../ProfilePicture/';
                
                // Extract file extension
                $fileExtension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);

                // Generate a unique file name
                $newFileName = uniqid('', true) . '.' . $fileExtension;
                $uploadFile = $newFileName; // Only store the file name and extension

                // Ensure the uploads directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($profilePicture['tmp_name'], $uploadDir . $uploadFile)) {
                    // File upload successful

                    // Delete the old profile picture if it is not "Default.jpg"
                    $currentPicture = $_POST['currentProfilePicture'];
                    if ($currentPicture !== 'Default.jpg' && file_exists($uploadDir . $currentPicture)) {
                        unlink($uploadDir . $currentPicture);
                    }
                } else {
                    echo '<script>alert("Error uploading profile picture."); window.location.href = "EditSRCDetailsPage.php";</script>';
                    exit;
                }
            } else {
                echo '<script>alert("Error uploading profile picture."); window.location.href = "EditSRCDetailsPage.php";</script>';
                exit;
            }
        }

        // Prepare and execute SQL update statement for ApprovedCandidates table
        $sql = "UPDATE VSStudents SET StudentName=?, StudentProfilePicture=?, Manifesto=? WHERE StudentID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $StudentName, $uploadFile, $Manifesto, $loggedInUser);

        if ($stmt->execute()) {
            // If update is successful, update VSStudents table as well
            $sqlUpdateSVUser = "UPDATE VSStudents SET StudentName=?, StudentProfilePicture=? WHERE StudentID=?";
            $stmtUpdateSVUser = $conn->prepare($sqlUpdateSVUser);
            $stmtUpdateSVUser->bind_param("sss", $StudentName, $uploadFile, $loggedInUser);
            $stmtUpdateSVUser->execute();
            
            echo '<script>alert("Successfully updated."); window.location.href = "EditSRCDetailsPage.php";</script>';
            exit; // Exit after successful update
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    // SQL query to fetch the details of the logged-in user from ApprovedCandidates table
    $sql = "SELECT StudentProfilePicture, StudentID, StudentName, Manifesto FROM VSStudents WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results and display them
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="Profile-Container">
                    <form action="EditSRCDetailsPage.php" method="post" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                        <table>
                            <tr>
                                <td><center><b>Profile Picture</b></center></td>
                                <td><center><b>SRC Details</b></center></td>
                            </tr>
                            <tr>
                                <td rowspan="2"><img src="../../../ProfilePicture/' . htmlspecialchars($row['StudentProfilePicture']) . '" alt="Profile Picture" height="250" width="250"></td>
                                <td>
                                    <b>Full Name</b><br>
                                    <input type="text" id="StudentName" name="StudentName" value="' . htmlspecialchars($row["StudentName"]) . '">
                                </td>
                            </tr>
                            <tr>
                                <td><b>Manifesto</b><br>
                                    <textarea id="Manifesto" name="Manifesto" rows="4" cols="50" style="width: 400px; height: 125px; overflow: auto; resize: none;" placeholder="Please write your manifesto.">' . htmlspecialchars($row["Manifesto"]) . '</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <center><input type="file" name="ProfilePicture" accept=".png,.jpg,.jpeg,.gif,.bmp,.webp"></center>
                                    <input type="hidden" name="currentProfilePicture" value="' . htmlspecialchars($row['StudentProfilePicture']) . '">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><center><button type="submit">Update SRC Profile</button></center></td>
                            </tr>
                        </table>
                    </form>
                </div>';
        }
    } else {
        echo '<script>alert("No record found for the logged-in user."); window.location.href = "../Home Page/UserHomepage.php";</script>';
    }
} else {
    // Handle the case where the session variable is not set
    echo '<script>alert("You must be logged in to view this page."); window.location.href = "../../../index.html";</script>';
}

// Close the connection
$conn->close();
?>
