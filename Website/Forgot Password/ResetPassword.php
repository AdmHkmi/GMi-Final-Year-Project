<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

include '../../Database/DatabaseConnection.php';

$sql = "SELECT * FROM VSStudents
        WHERE ResetPasswordToken = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["ResetPasswordTokenExpired"]) <= time()) {
    die("token has expired");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="ResetPassword.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <a href="../../index.html"><img src="../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <img src="../../Images/MaraLogo.png" class="MaraLogo" alt="Mara Logo">
    </header>
    <div class="background-image"></div>
    <main>
        <div class="reset-container">
            <h1>Reset Password</h1>
            <form method="post" action="ResetPasswordProcess.php">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <label for="password">New Password</label>
                <input type="text" id="password" name="password" required placeholder="Enter your new password">
                <label for="password_confirmation">Confirm Password</label>
                <input type="text" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your new password">
                <br>
                <button type="submit" class="btn">Reset Password</center></button>
            </form>
        </div>
    </main>
</body>
</html>
