<?php
session_start(); // Start the session to access session variables

include '../../../Database/DatabaseConnection.php';

// Check if the session variable is set
if (isset($_SESSION['AdminUsername'])) {
    // Get the logged-in username from the session
    $loggedInUser = $_SESSION['AdminUsername']; // Assuming the username is stored in a session variable

    // Check if the form was submitted for profile update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Process form data
        $AdminUsername = $_POST['AdminUsername'];
        $AdminPassword = $_POST['AdminPassword'];

        // Validate input (you can add more specific validation as needed)
        if (empty($AdminUsername) || empty($AdminPassword)) {
            echo '<script>alert("Please fill in all required fields."); window.location.href = "AdminProfilePage.php";</script>';
            exit; // Exit script if any field is empty
        }

        // Check if the new AdminUsername already exists and belongs to a different admin
        $sqlCheckUsername = "SELECT AdminUsername FROM VSAdmin WHERE AdminUsername = ? AND AdminUsername <> ?";
        $stmtCheckUsername = $conn->prepare($sqlCheckUsername);
        $stmtCheckUsername->bind_param("ss", $AdminUsername, $loggedInUser);
        $stmtCheckUsername->execute();
        $resultCheckUsername = $stmtCheckUsername->get_result();

        if ($resultCheckUsername->num_rows > 0) {
            echo '<script>alert("Username already exists. Please choose a different Username."); window.location.href = "AdminProfilePage.php";</script>';
            exit; // Exit if AdminUsername already exists
        }

        // SQL query to update the admin details
        $updateSql = "UPDATE VSAdmin SET AdminUsername = ?, AdminPassword = ? WHERE AdminUsername = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sss", $AdminUsername, $AdminPassword, $loggedInUser);

        if ($updateStmt->execute()) {
            // Update session variable
            $_SESSION['AdminUsername'] = $AdminUsername;
            echo '<script>alert("Successfully updated.");</script>';
            // Optionally, you can redirect or reload to refresh the form with updated data
            // header("Location: AdminProfilePage.php");
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }

    // SQL query to fetch the details of the logged-in admin
    $sql = "SELECT AdminUsername, AdminPassword FROM VSAdmin WHERE AdminUsername = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results and display them
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="Profile-Container">
            <div class="header-section"><h2>Profile Details</h2></div>
            <p>Please note that after making any changes, you will be required to log in again to access your account.</p>
            <form action="AdminProfilePage.php" method="post" onsubmit="return confirm(\'Are you sure you want to update your profile?\')">
                <table>
                    <tr>
                        <td><b>Username</b><br>
                            <input type="text" id="AdminUsername" name="AdminUsername" value="' . htmlspecialchars($row["AdminUsername"]) . '">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Password</b><br>
                            <input type="text" id="AdminPassword" name="AdminPassword" value="' . htmlspecialchars($row["AdminPassword"]) . '"><br><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><center><button type="submit">Update Profile</button></center></td>
                    </tr>
                </table>
            </form>
        </div>';
        }
    } else {
        echo '<script>alert("You already changed your username. Please login again."); window.location.href = "../../../index.html";</script>';
    }
} else {
    // Handle the case where the session variable is not set
    echo '<script>alert("You must be logged in to view this page."); window.location.href = "../../../index.html";</script>';
}

// Close the connection
$conn->close();
?>
