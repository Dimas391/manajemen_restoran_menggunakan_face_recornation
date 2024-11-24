<?php
// session.php
session_start();
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit();
}

// Handle language change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_language'])) {
    // Simpan pilihan bahasa ke dalam session atau database
    $_SESSION['language'] = $_POST['language'];
    // Redirect atau tampilkan pesan sukses
    header("Location: language.php");
    exit();
}

// Ambil bahasa yang dipilih dari session
$selectedLanguage = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Settings | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .settings-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 80px);
        }

        .profile-header {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .input-group {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }

        .input-field {
            position: relative;
            margin-bottom: 15px;
        }

        .input-field:last-child {
            margin-bottom: 0;
        }

        .input-field select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .input-field select:focus {
            border-color: #9333EA;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
            outline: none;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1a1a1a;
            padding: 15px;
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-around;
            z-index: 1000;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-button {
            background-color: rgba(220, 38, 38, 0.2);
            color: #ef4444;
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 30px;
        }

        .logout-button:hover {
            background-color: rgba(220, 38, 38, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="settings-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
    </button>
            <!-- Language Settings Header -->
            <div class="profile-header">
            <h2 class="text-2xl font-bold">Language Settings</h2>
            <p class="mt-2">Select your preferred language for the application.</p>
        </div>

        <!-- Language Selection Form -->
        <form method="POST" class="input-group">
            <div class="input-field">
                <select name="language" required>
                    <option value="en" <?php echo $selectedLanguage === 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="id" <?php echo $selectedLanguage === 'id' ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                    <option value="es" <?php echo $selectedLanguage === 'es' ? 'selected' : ''; ?>>Español</option>
                    <option value="fr" <?php echo $selectedLanguage === 'fr' ? 'selected' : ''; ?>>Français</option>
                    <!-- Add more languages as needed -->
                </select>
            </div>
            <button type="submit" name="change_language" class="w-full mt-4 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-button">
                <i class="bi bi-box-arrow-right mr-2"></i>
                Logout
            </button>
        </form>
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-gray-800 p-4">
        <div class="max-w-screen-xl mx-auto flex justify-around">
            <a href="home.php" class="text-gray-400 flex flex-col items-center">
                <i class="bi bi-house"></i>
                <span class="text-sm">Home</span>
            </a>
            <a href="scan.php" class="text-gray-400 flex flex-col items-center">
                <i class="bi bi-qr-code"></i>
                <span class="text-sm">Scan</span>
            </a>
            <a href="keranjang.php" class="text-gray-400 flex flex-col items-center">
                <i class="bi bi-cart"></i>
                <span class="text-sm">Keranjang</span>
            </a>
            <a href="profile.php" class="text-gray-400 flex flex-col items-center">
                <i class="bi bi-person"></i>
                <span class="text-sm">Profile</span>
            </a>
        </div>
    </nav>

    <script>
        // Add focus effects to select fields
        document.querySelectorAll('.input-field select').forEach(select => {
            select.addEventListener('focus', function() {
                this.style.borderColor = '#9333EA';
            });

            select.addEventListener('blur', function() {
                this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            });
        });
    </script>
</body>
</html>