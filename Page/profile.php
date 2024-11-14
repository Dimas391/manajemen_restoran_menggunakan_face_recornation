<?php
// Hardcoded user data
$userData = [
    "name" => "Dimas",
    "email" => "Dimas@gmail.com",
    "avatar" => "kepala_sekolah.jpg" // Gambar avatar
];

// Handle logout
if (isset($_POST['logout'])) {
    setcookie('user_email', '', time() - 3600); // Expire the cookie
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            position: relative;
            padding-bottom: 60px; /* Space for bottom navbar */
        }
        .container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .back-button {
            display: inline-block;
            width: 35px;
            height: 35px;
            background: #333;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .back-button:hover {
            background: #555;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        .profile-header img {
            border-radius: 50%;
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }
        .settings-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .settings-menu li {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            background: #f4f4f4;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .settings-menu li:hover {
            background: #e0e0e0;
        }
        .settings-menu li i {
            margin-right: 10px;
            font-size: 20px;
        }
        .logout-button {
            display: block;
            width: 100%;
            padding: 12px;
            background: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
        }
        .logout-button:hover {
            background: #e03333;
        }

        /* Bottom Navbar */
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

        /* Responsive Design */
        @media (max-width: 500px) {
            .container {
                padding: 15px;
            }
            .settings-menu li {
                padding: 10px 12px;
            }
        }

        /* Increase margin-top for "Language" item to push it further down */
        .language-item {
            margin-top: 60px; /* More space added to push "Language" item further down */
        }
        .navbar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            margin-top: 20px;
            background-color: white;
            border-top: 1px solid #ccc;
        }
        .navbar button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            flex-direction: column;
            font-size: 12px;
            color: #333;
        }
        .navbar img {
            width: 25px;
            height: 25px;
            margin-bottom: 5px;
        }

        .navbar a {
        text-decoration: none;
        color: inherit; /* Agar warna teks mengikuti warna elemen induknya */
        }   

        .settings-menu a {
        text-decoration: none;
        color: inherit; /* Membuat warna teks mengikuti warna elemen orang tua */
    }

    .settings-menu a:hover {
    color: #007bff; /* Memberikan warna hover untuk elemen */
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="back-button" title="Go Back">&#8678;</div> <!-- Back Button with thick arrow -->
            <h1>Profile</h1>
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
            <img src="../assets/image/icon/icon_avatar.png" alt="User Avatar">
            <div>
                <strong><?php echo htmlspecialchars($userData['name']); ?></strong><br>
                <small><?php echo htmlspecialchars($userData['email']); ?></small>
            </div>
        </div>

        <!-- Settings Menu -->
        <ul class="settings-menu">
    <li title="Manage your account settings">
        <a href="settigs.php">
            <i class="bi bi-person-circle"></i> <!-- Account Icon -->
            Account Setting
        </a>
    </li>
    <hr> <!-- Divider between Account Settings and Language -->
    <li class="language-item" title="Change your language preferences">
        <a href="language.php">
            <i class="bi bi-translate"></i> <!-- Language Icon -->
            Language
        </a>
    </li>
    <li title="Provide your feedback">
        <a href="feedback.php">
            <i class="bi bi-chat-left-text"></i> <!-- Feedback Icon -->
            Feedback
        </a>
    </li>
    <li title="Rate our app">
        <a href="rate-us.php">
            <i class="bi bi-star"></i> <!-- Rate Us Icon -->
            Rate Us
        </a>
    </li>
    <li title="Download the latest version">
        <a href="new-version.php">
            <i class="bi bi-cloud-arrow-down"></i> <!-- New Version Icon -->
            New Version
        </a>
    </li>
</ul>


        <!-- Logout Button -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-button">Logout</button>
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
