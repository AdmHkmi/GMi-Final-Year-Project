<!DOCTYPE html>
<html>
<head>
    <title>Manage Users Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="ManageUsers.css">
</head>
<body>
    <div class="background-image"></div>
    <header>
        <div class="logo-container">
            <a href="../Home Page/AdminHomepage.php"><img src="../../../Images/GMiLogo.png" class="GMiLogo" alt="GMi Logo"></a>
        </div>
        <div class="top-right-buttons">
            <a href="AddUser.php"><button class="add-user">Add User</button></a> 
            <button class="back-button" onclick="scrollToTop()"><i class='fas fa-arrow-up'></i></button>
            <a href="../Home Page/AdminHomepage.php"><button class="back-button"><i class='fas fa-arrow-left'></i></button></a>
        </div>
    </header>
    <main>
        <div class="Manage-Users-Container">
            <center>
                <h2>List of Registered Users</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label for="search">Search:</label>
                    <input type="text" id="search" name="search" placeholder="Enter Name or StudentID">
                    <button type="submit">Search</button>
                    <br><br>
                    <input type="radio" id="all" name="user_status" value="all" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'all' ? 'checked' : ''; ?>>
                    <label for="all">All</label>
                    <input type="radio" id="active" name="user_status" value="active" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'active' ? 'checked' : ''; ?>>
                    <label for="active">Active</label>
                    <input type="radio" id="inactive" name="user_status" value="inactive" <?php echo isset($_POST['user_status']) && $_POST['user_status'] == 'inactive' ? 'checked' : ''; ?>>
                    <label for="inactive">Inactive</label>
                    <button type="submit">Filter</button>
                </form>
                <br>
                <?php 
                    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
                        include 'SearchUsers.php'; 
                    } else {
                        include 'ViewAndManageUsers.php';
                    }
                ?>
            </center>
        </div>
    </main>
    <footer></footer>

    <script>
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
