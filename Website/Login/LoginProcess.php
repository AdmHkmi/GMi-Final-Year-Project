<?php
session_start(); // Start the session

include '../../Database/DatabaseConnection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username/email and password from the form
    $UsernameOrEmail = trim($_POST["UsernameOrEmail"]);
    $password = $_POST["password"];

    // Prepare SQL statement to retrieve admin from the database
    $sqlAdmin = "SELECT * FROM VSAdmin WHERE AdminUsername = ? AND AdminPassword = ?";
    $stmtAdmin = $conn->prepare($sqlAdmin);
    $stmtAdmin->bind_param("ss", $UsernameOrEmail, $password);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    // Check if a row is returned for admin
    if ($resultAdmin->num_rows > 0) {
        $adminRow = $resultAdmin->fetch_assoc();
        // Store admin username in the session
        $_SESSION['AdminUsername'] = $adminRow['AdminUsername'];
        $_SESSION['role'] = 'admin';
        $_SESSION['AdminLoggedIn'] = true;

        // Set cookies for admin login
        setcookie('AdminUsername', $adminRow['AdminUsername'], time() + (86400 * 30), "/"); // Cookie lasts for 30 days
        setcookie('role', 'admin', time() + (86400 * 30), "/");

        // Redirect to the admin homepage upon successful login
        header("Location: ../Admin/Home Page/AdminHomepage.php");
        exit; // Ensure that subsequent code is not executed after redirection
    }

    // Prepare SQL statement to retrieve user from the database
    $sqlUser = "SELECT * FROM VSStudents WHERE (StudentID = ? OR StudentEmail = ?) AND StudentPassword = ?";
    $stmtUser = $conn->prepare($sqlUser);
    $stmtUser->bind_param("sss", $UsernameOrEmail, $UsernameOrEmail, $password);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();

    // Check if a row is returned for user
    if ($resultUser->num_rows > 0) {
        $userRow = $resultUser->fetch_assoc();
        if ($userRow['UserApproval'] == 1) {
            // Store user details in session
            $_SESSION['StudentID'] = $userRow['StudentID'];
            $_SESSION['StudentEmail'] = $userRow['StudentEmail'];
            $_SESSION['role'] = 'user';
            $_SESSION['SRCVoteLimit'] = $userRow['SRCVoteLimit']; // Store SRCVoteStatus in session
            $_SESSION['NominationVoteLimit'] = $userRow['NominationVoteLimit']; // Store NominationVoteStatus in session

            // Set cookies for user login
            setcookie('StudentID', $userRow['StudentID'], time() + (86400 * 30), "/"); // Cookie lasts for 30 days
            setcookie('StudentEmail', $userRow['StudentEmail'], time() + (86400 * 30), "/"); // Cookie lasts for 30 days
            setcookie('role', 'user', time() + (86400 * 30), "/");

            // Redirect to the user homepage upon successful login
            header("Location: ../User/Home Page/UserHomepage.php");
            exit; // Ensure that subsequent code is not executed after redirection
        } else {
            // Handle user approval pending case
            echo '<script>alert("Your account is in the process of approval, please try again later!"); window.location.href = "../../index.html";</script>';
        }
    } else {
        // Handle authentication failure by showing a popup message
        echo '<script>alert("Invalid username or password. Please try again."); window.location.href = "../../index.html";</script>';
    }

    // Close the statement for user
    $stmtUser->close();

    // Close the statement for admin
    $stmtAdmin->close();
}

// Close database connection
$conn->close();
?>
