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
    <header class="header">
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="../Manage Events/ManageEvents.php">Manage Events</a></li>
                <li><a href="../Manage Users/ManageUsers.php">Manage Users</a></li>
                <li><a href="../Manage Participants/ManageParticipants.php">Manage Participants</a></li>
                <li><a href="../Manage Result/ManageResult.php">Manage Results</a></li>
                <li><a href="ManageNews.php">Manage News</a></li>
                <li><a href="../Generate Report/GenerateReport.php">Generate Report</a></li>
            </ul>
        </nav>
        <div class="top-right-buttons">
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button> <!-- Back button --></a>
        </div>
    </header>
    <main>
        <h1>Manage News</h1>
        <div class="instructions"><p>Welcome to the Manage News page! Here, you can view existing news articles, publish or unpublish news, and update content as needed.</p></div>
        <div class="Add-News-Container">
        <div class="add-news">
            <div class="header-section"><h2>Add News</h2></div>
            <p>In this section, you can add news items by filling in the title and content. Optionally, include an image and set the status to share with students. Once added, the news item can be managed and edited as needed.</p>
            <form action="AddNewsProcess.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" placeholder="News Title" required>
                </div>
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea name="content" id="content" placeholder="Enter the description of the news" required style="resize: none;"></textarea>
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
            <div class="header-section"><h2>Manage News</h2></div>
            <p>In this section, you can edit existing news items by updating the title and content. Optionally, you can change the image and adjust the status to control sharing with students. Once updated, the news item will reflect the changes made.</p>
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
