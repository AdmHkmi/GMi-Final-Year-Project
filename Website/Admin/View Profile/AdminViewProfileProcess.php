<?php
session_start(); // Start the session to access session variables

include '../../../Database/DatabaseConnection.php';

// Check if the session variable is set
if (isset($_SESSION['AdminUsername'])) {
    // Get the logged-in username from the session
    $loggedInUser = $_SESSION['AdminUsername']; // Assuming the username is stored in a session variable

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
                <table>
                    <tr>
                        <td><b>Username</b><br>
                            <input type="text" id="AdminUsername" name="AdminUsername" value="' . htmlspecialchars($row["AdminUsername"]) . '" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Password</b><br>
                            <input type="text" id="AdminPassword" name="AdminPassword" value="' . htmlspecialchars($row["AdminPassword"]) . '" readonly><br><br>
                        </td>
                    </tr>
                </table>
            </div>';
        }
    } 
} else {
    // Handle the case where the session variable is not set
    echo '<script>alert("You must be logged in to view this page."); window.location.href = "../../../index.html";</script>';
}

// Close the connection
$conn->close();
?>
