<?php
session_start(); // Start the session to access session variables

include '../../../Database/DatabaseConnection.php';

// Check if the session variable is set
if (isset($_SESSION['StudentID'])) {
    // Get the logged-in StudentID from the session
    $loggedInUser = $_SESSION['StudentID'];

    // Check if the form was submitted for profile update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data
        $StudentName = $_POST['StudentName'];
        $Manifesto = $_POST['Manifesto'];

        // Validate input
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
                $uploadFile = $newFileName; // Store the new file name

                // Ensure the uploads directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Move the uploaded file to the desired directory
                if (!move_uploaded_file($profilePicture['tmp_name'], $uploadDir . $uploadFile)) {
                    echo '<script>alert("Error uploading profile picture."); window.location.href = "EditSRCDetailsPage.php";</script>';
                    exit;
                }

                // Delete the old profile picture if it is not "Default.jpg"
                $currentPicture = $_POST['currentProfilePicture'];
                if ($currentPicture !== 'Default.jpg' && file_exists($uploadDir . $currentPicture)) {
                    unlink($uploadDir . $currentPicture);
                }
            } else {
                echo '<script>alert("Error uploading profile picture."); window.location.href = "EditSRCDetailsPage.php";</script>';
                exit;
            }
        }

        // Prepare and execute SQL update statement for VSVote table (for Manifesto and profile picture)
        $sql = "UPDATE VSVote SET StudentProfilePicture=?, Manifesto=? WHERE StudentID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $uploadFile, $Manifesto, $loggedInUser);

        if ($stmt->execute()) {
            // Prepare and execute SQL update statement for VSStudents table (for StudentName)
            $sql2 = "UPDATE VSStudents SET StudentName=? WHERE StudentID=?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ss", $StudentName, $loggedInUser);
            $stmt2->execute();
            
            echo '<script>alert("Successfully updated."); window.location.href = "EditSRCDetailsPage.php";</script>';
            exit; // Exit after successful update
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

// SQL query to fetch the details of the logged-in user from VSStudents table for StudentName and profile picture
// and from VSVote table for Manifesto
$sql = "SELECT S.StudentProfilePicture, S.StudentID, S.StudentName, V.Manifesto 
        FROM VSVote V 
        JOIN VSStudents S ON V.StudentID = S.StudentID 
        WHERE V.StudentID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();


    // Check if there are results and display them
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="Profile-Container">
                    <form action="EditSRCDetailsPage.php" method="post" enctype="multipart/form-data" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                        <div class="profile-header">
                            <h2>Edit Your SRC Profile</h2>
                        </div>
                        <div class="profile-content">
                            <div class="profile-picture">
                                <img src="../../../ProfilePicture/' . htmlspecialchars($row['StudentProfilePicture']) . '" alt="Profile Picture">
                                <input type="file" name="ProfilePicture" accept=".png,.jpg,.jpeg,.gif,.bmp,.webp">
                                <input type="hidden" name="currentProfilePicture" value="' . htmlspecialchars($row['StudentProfilePicture']) . '">
                            </div>
                            <div class="profile-details">
                                <label for="StudentName"><b>Full Name</b></label>
                                <input type="text" id="StudentName" name="StudentName" value="' . htmlspecialchars($row["StudentName"]) . '">
                                <label for="Manifesto"><b>Manifesto</b></label>
                                <textarea id="Manifesto" name="Manifesto" rows="4" cols="50" placeholder="Please write your manifesto." style="resize: none;">' . htmlspecialchars($row["Manifesto"]) . '</textarea>
                            </div>
                        </div>
                        <div class="profile-footer">
                            <button type="submit">Update SRC Profile</button>
                        </div>
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
