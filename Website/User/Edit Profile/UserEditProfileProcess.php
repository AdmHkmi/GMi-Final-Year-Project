<?php
session_start(); // Start the session to access session variables
include '../../../Database/DatabaseConnection.php';

// Check if the session variable is set
if (isset($_SESSION['StudentID'])) {
    // Get the logged-in username from the session
    $loggedInUser = $_SESSION['StudentID']; // Assuming the username is stored in a session variable

    // Check if the form was submitted for profile update or resetting the profile picture
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['reset_picture'])) {
            // Fetch the current profile picture from the database
            $sqlFetchCurrent = "SELECT StudentProfilePicture FROM VSStudents WHERE StudentID = ?";
            $stmtFetchCurrent = $conn->prepare($sqlFetchCurrent);
            $stmtFetchCurrent->bind_param("s", $loggedInUser);
            $stmtFetchCurrent->execute();
            $resultFetchCurrent = $stmtFetchCurrent->get_result();

            if ($resultFetchCurrent->num_rows > 0) {
                $rowCurrent = $resultFetchCurrent->fetch_assoc();
                $currentPicture = $rowCurrent['StudentProfilePicture'];

                // If the current picture is not the default one, delete it
                if ($currentPicture !== "Default.jpg") {
                    $uploadDir = '../../../ProfilePicture/';
                    $filePath = $uploadDir . $currentPicture;

                    if (file_exists($filePath)) {
                        unlink($filePath); // Delete the current image
                    }
                }

                // Reset profile picture to Default.jpg in the database
                $sql = "UPDATE VSStudents SET StudentProfilePicture = 'Default.jpg' WHERE StudentID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $loggedInUser);

                if ($stmt->execute()) {
                    echo '<script>alert("Profile picture reset to default."); window.location.href = "UserProfilePage.php";</script>';
                    exit; // Exit after resetting profile picture
                } else {
                    echo "Error resetting profile picture: " . $conn->error;
                    exit;
                }
            }
        } else {
            // Process the profile update (existing logic)
            $StudentName = $_POST['StudentName'];
            $StudentID = strtoupper($_POST['StudentID']); // Convert to uppercase
            $StudentEmail = $_POST['StudentEmail'];
            $StudentPassword = $_POST['StudentPassword'];
            $profilePicture = $_FILES['ProfilePicture'];

            // Validate input (you can add more specific validation as needed)
            if (empty($StudentName) || empty($StudentID) || empty($StudentEmail) || empty($StudentPassword)) {
                echo '<script>alert("Please fill in all required fields."); window.location.href = "UserProfilePage.php";</script>';
                exit; // Exit script if any field is empty
            }

            // Check if the new StudentID already exists and belongs to a different user
            $sqlCheckID = "SELECT StudentID FROM VSStudents WHERE StudentID = ? AND StudentID <> ?";
            $stmtCheckID = $conn->prepare($sqlCheckID);
            $stmtCheckID->bind_param("ss", $StudentID, $loggedInUser);
            $stmtCheckID->execute();
            $resultCheckID = $stmtCheckID->get_result();

            if ($resultCheckID->num_rows > 0) {
                echo '<script>alert("Student ID already exists. Please choose a different Student ID."); window.location.href = "UserProfilePage.php";</script>';
                exit; // Exit if StudentID already exists
            }

            // Check if the new StudentEmail already exists and belongs to a different user
            $sqlCheckEmail = "SELECT StudentEmail FROM VSStudents WHERE StudentEmail = ? AND StudentID <> ?";
            $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
            $stmtCheckEmail->bind_param("ss", $StudentEmail, $loggedInUser);
            $stmtCheckEmail->execute();
            $resultCheckEmail = $stmtCheckEmail->get_result();

            if ($resultCheckEmail->num_rows > 0) {
                echo '<script>alert("Student Email already exists. Please choose a different Student Email."); window.location.href = "UserProfilePage.php";</script>';
                exit; // Exit if StudentEmail already exists
            }

            // Validate and handle file upload
            if ($profilePicture['error'] == UPLOAD_ERR_OK) {
                $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'webp'];
                $fileSizeLimit = 5 * 1024 * 1024; // 5MB

                $fileName = $profilePicture['name'];
                $fileSize = $profilePicture['size'];
                $fileTmpName = $profilePicture['tmp_name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                // Check file size
                if ($fileSize > $fileSizeLimit) {
                    echo '<script>alert("File size exceeds 5MB. Please upload a smaller file."); window.location.href = "UserProfilePage.php";</script>';
                    exit; // Exit if file size is too large
                }

                // Check file extension
                if (!in_array($fileExtension, $allowedExtensions)) {
                    echo '<script>alert("Invalid file type. Please upload a valid image file."); window.location.href = "UserProfilePage.php";</script>';
                    exit; // Exit if file type is invalid
                }

                // Ensure the uploads directory exists
                $uploadDir = '../../../ProfilePicture/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Delete old profile picture if it's not the default one
                $sqlFetchCurrent = "SELECT StudentProfilePicture FROM VSStudents WHERE StudentID = ?";
                $stmtFetchCurrent = $conn->prepare($sqlFetchCurrent);
                $stmtFetchCurrent->bind_param("s", $loggedInUser);
                $stmtFetchCurrent->execute();
                $resultFetchCurrent = $stmtFetchCurrent->get_result();

                if ($resultFetchCurrent->num_rows > 0) {
                    $rowCurrent = $resultFetchCurrent->fetch_assoc();
                    $currentPicture = $rowCurrent['StudentProfilePicture'];
                    if ($currentPicture !== "Default.jpg" && file_exists($uploadDir . $currentPicture)) {
                        unlink($uploadDir . $currentPicture); // Remove the old picture
                    }
                }

                $newFileName = uniqid('', true) . '.' . $fileExtension;
                $uploadFile = $uploadDir . $newFileName;

                // Move the uploaded file to the desired directory
                if (move_uploaded_file($fileTmpName, $uploadFile)) {
                    // Update the user's profile in the database with the new image
                    $sql = "UPDATE VSStudents SET StudentName=?, StudentID=?, StudentEmail=?, StudentPassword=?, StudentProfilePicture=? WHERE StudentID=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $StudentName, $StudentID, $StudentEmail, $StudentPassword, $newFileName, $loggedInUser);
                } else {
                    echo "Error uploading profile picture.";
                    exit;
                }
            } else {
                // Update the user's profile in the database without changing the image
                $sql = "UPDATE VSStudents SET StudentName=?, StudentID=?, StudentEmail=?, StudentPassword=? WHERE StudentID=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $StudentName, $StudentID, $StudentEmail, $StudentPassword, $loggedInUser);
            }

            if ($stmt->execute()) {
                // Check if StudentID or StudentPassword was updated
                if ($StudentID !== $loggedInUser || !empty($StudentPassword)) {
                    // Log the user out since their StudentID or password has changed
                    session_destroy(); // Destroy the session
                    echo '<script>alert("Your Student ID or password has been changed. Please log in again."); window.location.href = "../../../index.html";</script>';
                    exit; // Exit after successful update
                }

                echo '<script>alert("Successfully updated."); window.location.href = "UserProfilePage.php";</script>';
                exit; // Exit after successful update
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }

    // SQL query to fetch the details of the logged-in user
    $sql = "SELECT StudentProfilePicture, StudentName, StudentID, StudentEmail, StudentPassword FROM VSStudents WHERE StudentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results and display them
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="Profile-Container">
                    <form action="UserProfilePage.php" method="post" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                        <table>
                            <tr>
                                <td><center><b>Profile Picture</b></center></td>
                                <td><center><b>Personal Details</b></center></td>
                            </tr>
                            <tr>
                                <td rowspan="3"><img src="../../../ProfilePicture/' . htmlspecialchars($row['StudentProfilePicture']) . '" alt="Profile Picture" height="250" width="250"></td>
                                <td>
                                    <b>Full Name</b><br>
                                    <input type="text" id="StudentName" name="StudentName" value="' . htmlspecialchars($row["StudentName"]) . '">
                                </td>
                            </tr>
                            <tr>
                                <td><b>Email</b><br>
                                    <input type="email" id="StudentEmail" name="StudentEmail" value="' . htmlspecialchars($row["StudentEmail"]) . '">
                                </td>
                            </tr>
                            <tr>
                                <td><b>Student ID</b><br>
                                    <input type="text" id="StudentID" name="StudentID" value="' . htmlspecialchars($row["StudentID"]) . '">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <center><input type="file" name="ProfilePicture" accept="image/*"></center>
                                </td>
                                <td>
                                    <b>Password</b><br>
                                    <input type="text" id="StudentPassword" name="StudentPassword" value="' . htmlspecialchars($row["StudentPassword"]) . '"><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <center><button type="submit" class="button-update">Update Profile</button></center>
                                    <center><button type="submit" name="reset_picture" class="button-reset" onclick="return confirm(\'Are you sure you want to reset your profile picture to default?\')">Default Profile Picture</button></center>
                                </td>
                            </tr>
                        </table>
                    </form>
                  </div>';
        }
    } else {
        echo "No data found for the logged-in user.";
    }
} else {
    // Redirect to index.html if the user is not logged in
    header("Location: ../../../index.html");
    exit; // Exit after redirecting
}

$conn->close();
?>
