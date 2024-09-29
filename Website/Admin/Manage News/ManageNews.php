<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News</title>
    <link rel="stylesheet" href="ManageNews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <div class="Add-News-Container">
        <div class="add-news">
            <h2 style="background-color: #24287E; color: white; padding: 15px; margin: 20px 0; text-align: center; border-radius: 4px;">Add News</h2>
            <form action="AddNewsProcess.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" placeholder="News Title" required>
                </div>
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea name="content" id="content" placeholder="Enter the description of the news" required></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" name="image" id="image">
                </div>
                <div class="form-group">
                    <label for="is_active">Status:</label>
                    <select name="is_active" id="is_active" required>
                        <option value="1">Shared</option>
                        <option value="0">Not Shared</option>
                    </select>
                </div>
                <div class="form-group">
                <button type="submit" onclick="return confirm('Are you sure you want to add this news?')">Add News</button>
                </div>
            </form>
        </div>
        </div>
        <div class="Manage-News-Container">
        <div class="manage-news">
            <h2 style="background-color: #24287E; color: white; padding: 15px; margin: 20px 0; text-align: center; border-radius: 4px;">Manage News</h2>
            <?php include 'ViewAndManageNewsProcess.php'; ?>
        </div>
        </div>
    </main>
    <script>
        // JavaScript function to toggle visibility of edit form row
        function toggleEditRow(newsID) {
            var editRow = document.getElementById('edit-row-' + newsID);
            if (editRow.style.display === 'none') {
                editRow.style.display = 'block';
            } else {
                editRow.style.display = 'none';
            }
        }
    </script>
</body>
</html>
