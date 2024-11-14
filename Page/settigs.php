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
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .profile-header img {
            border-radius: 50%;
            width: 80px;
            height: 80px;
            margin-right: 20px;
        }

        .profile-header div {
            text-align: left;
        }

        .profile-header div strong {
            font-size: 18px;
            color: #333;
        }

        .profile-header div small {
            font-size: 14px;
            color: #777;
        }

        .settings-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .settings-menu li {
            padding: 12px;
            background: #f9f9f9;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .settings-menu li:hover {
            background-color: #f1f1f1;
        }

        .settings-menu li i {
            margin-right: 15px;
            font-size: 20px;
            color: #333;
        }

        .logout-button {
            width: 100%;
            padding: 15px;
            background: #ff4d4d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
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

            .profile-header img {
                width: 60px;
                height: 60px;
            }

            .settings-menu li {
                padding: 10px;
            }
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
            <h1>Account Settings</h1>
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
            <li title="Update your profile details">
                <i class="bi bi-person-circle"></i> Profile
            </li>
            <li title="Change your password">
                <i class="bi bi-lock"></i> Change Password
            </li>
            <li title="Change your language preferences">
                <i class="bi bi-translate"></i> Language Settings
            </li>
            <li title="Manage your privacy and security">
                <i class="bi bi-shield-lock"></i> Privacy & Security
            </li>
            <li title="Update notification preferences">
                <i class="bi bi-bell"></i> Notifications
            </li>
        </ul>

        <!-- Logout Button -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-button">Save</button>
        </form>
    </div>

    <!-- Bottom Navbar -->
    <<div class="navbar">
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
