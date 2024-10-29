<?php
session_start();

include '../../../Database/DatabaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newsID = $_POST['newsID'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_active = $_POST['is_active'];

    // Fetch the current image name
    $fetch_sql = "SELECT NewsImage FROM VSNews WHERE NewsID=?";
    $stmt = $conn->prepare($fetch_sql);
    $stmt->bind_param("i", $newsID);
    $stmt->execute();
    $stmt->bind_result($currentImage);
    $stmt->fetch();
    $stmt->close();

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $imageSize = $_FILES['image']['size'];
        $imageFileType = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'webp'];

        // Check if file is an image
        $check = getimagesize($image_tmp_name);
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

        // Generate a unique file name
        $newFileName = uniqid('', true) . '.' . $imageFileType;
        $target_dir = "../../../News/";
        $target_file = $target_dir . $newFileName;

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            // Remove the old file if it exists
            if (!empty($currentImage) && file_exists($target_dir . $currentImage)) {
                unlink($target_dir . $currentImage);
            }

            // Update news item with new image
            $update_sql = "UPDATE VSNews SET NewsTitle=?, NewsContent=?, NewsImage=?, IsActive=? WHERE NewsID=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $title, $content, $newFileName, $is_active, $newsID);
        } else {
            // Handle file upload error
            echo '<script>alert("Error uploading image."); window.location.href = "ManageNews.php";</script>';
            exit();
        }
    } else {
        // Update news item without changing the image
        $update_sql = "UPDATE VSNews SET NewsTitle=?, NewsContent=?, IsActive=? WHERE NewsID=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssii", $title, $content, $is_active, $newsID);
    }

    if ($stmt->execute()) {
        echo '<script>alert("News updated successfully."); window.location.href = "ManageNews.php";</script>';
    } else {
        echo '<script>alert("Error updating news: ' . $stmt->error . '"); window.location.href = "ManageNews.php";</script>';
    }

    $stmt->close();
}

$conn->close();
exit();
