<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $isActive = $_POST['is_active']; // The value will be '1' for active or '0' for inactive

    // Allowed image formats
    $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'webp'];

    // Handle file upload
    $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : null;
    $imageSize = $_FILES['image']['size'];
    $imageTmpName = $_FILES['image']['tmp_name'];
    $imageError = $_FILES['image']['error'];
    $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if ($image) {
        // Check if file is an image
        $check = getimagesize($imageTmpName);
        if ($check === false) {
            echo '<script>alert("File is not an image."); window.location.href = "ManageNews.php";</script>';
            exit();
        }

        // Check file size (5MB = 5 * 1024 * 1024 bytes)
        if ($imageSize > 5 * 1024 * 1024) {
            echo '<script>alert("Sorry, your file is too large. Maximum size is 5MB."); window.location.href = "ManageNews.php";</script>';
            exit();
        }

        // Allow only certain formats
        if (!in_array($imageFileType, $allowedExtensions)) {
            echo '<script>alert("Sorry, only JPG, JPEG, PNG, GIF, BMP, TIFF, & WEBP files are allowed."); window.location.href = "ManageNews.php";</script>';
            exit();
        }

        // Check for errors
        if ($imageError !== 0) {
            echo '<script>alert("There was an error uploading your file."); window.location.href = "ManageNews.php";</script>';
            exit();
        }

        // Generate a unique file name and set the target file path
        $newFileName = uniqid('', true) . '.' . $imageFileType;
        $target_dir = "../../../News/";
        $target_file = $target_dir . $newFileName;

        // Move uploaded file to the target directory
        if (!move_uploaded_file($imageTmpName, $target_file)) {
            echo '<script>alert("Error uploading image."); window.location.href = "ManageNews.php";</script>';
            exit();
        }
        
        // Store only the filename in the database
        $imageForDB = $newFileName;
    } else {
        // If no file is uploaded, set imageForDB to null
        $imageForDB = null;
    }

    // Database connection
    include '../../../Database/DatabaseConnection.php';

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO VSNews (NewsTitle, NewsContent, NewsImage, IsActive) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute
    $stmt->bind_param("sssi", $title, $content, $imageForDB, $isActive);

    $stmt->execute();

    // Check if insertion was successful
    if ($stmt->affected_rows > 0) {
        echo '<script>alert("News created successfully."); window.location.href = "ManageNews.php";</script>';
    } else {
        echo '<script>alert("Error creating news."); window.location.href = "ManageNews.php";</script>';
    }

    // Clean up
    $stmt->close();
    $conn->close();
}
