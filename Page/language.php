<?php
// Process to handle language change
if (isset($_POST['language'])) {
    $selectedLanguage = $_POST['language'];
    // Save the language selection to a cookie
    setcookie('selected_language', $selectedLanguage, time() + 3600, '/'); // Cookie valid for 1 hour
    header("Location: profile.php"); // Redirect back to profile page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fa;
            padding-bottom: 80px; /* Space for bottom navbar */
        }
        .container {
            max-width: 450px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        select {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        .save-btn {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .save-btn:hover {
            background-color: #45a049;
        }
        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .navbar a {
            color: black;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
        }
        .navbar a i {
            font-size: 24px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Select Language</h1>
        </div>

        <form action="language.php" method="POST">
            <div class="form-group">
                <label for="language">Choose your language:</label>
                <select id="language" name="language" required>
                    <option value="en" <?php echo $selectedLanguage == 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="id" <?php echo $selectedLanguage == 'id' ? 'selected' : ''; ?>>Indonesian</option>
                    <!-- Add more languages as needed -->
                </select>
            </div>
            <button type="submit" class="save-btn">Save Language</button>
        </form>
    </div>

    <!-- Bottom Navbar -->
    <div class="navbar">
        <a href="home.php">
            <button><img src="../assets/image/icon/Home.png" alt="Home" /><span>Home</span></button>
        </a>
        <a href="scan.php">
            <button><img src="../assets/image/icon/Scan.svg" alt="QR" /><span>Scan</span></button>
        </a>
        <a href="keranjang.php">
            <button><img src="../assets/image/icon/Document.png" alt="Pesan" /><span>Pesan</span></button>
        </a>
        <a href="profile.php">
            <button><img src="../assets/image/icon/Profile.png" alt="Profile" /><span>Profile</span></button>
        </a>
    </div>

</body>
</html>
