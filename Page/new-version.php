<?php
include 'session.php'; // Pastikan ini ada di bagian atas

// Simulasi data pembaruan (dalam aplikasi nyata, Anda mungkin mengambil ini dari database atau API)
$updates = [
    ["version" => "1.0.1", "date" => "2023-10-01", "description" => "Bug fixes and performance improvements."],
    ["version" => "1.0.0", "date" => "2023-09-15", "description" => "Initial release with basic features."],
];

// Jika ada pesan sukses dari proses pembaruan
if (isset($_SESSION['update_success'])) {
    $update_success_message = $_SESSION['update_success'];
    unset($_SESSION['update_success']); // Hapus pesan setelah ditampilkan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check for Updates | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .updates-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
        }

        .updates-header {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .update-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .update-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.15);
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
    </style>
</head>
<body>
    <div class="updates-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
        </button>

        <!-- Updates Header -->
        <div class="updates-header">
            <h1 class="text-2xl font-bold mb-2">Check for Updates</h1>
            <p class="text-gray-200">Stay up to date with the latest features and improvements!</p>
        </div>

        <!-- Display Success Message -->
        <?php if (isset($update_success_message)): ?>
            <div class="mt-4 bg-green-500 text-white p-3 rounded">
                <?php echo htmlspecialchars($update_success_message); ?>
            </div>
        <?php endif; ?>

        <!-- Updates List -->
        <?php if (empty($updates)): ?>
            <div class="update-card">
                <p class="text-gray-200">No updates available.</p>
            </div>
        <?php else: ?>
            <?php foreach ($updates as $update): ?>
                <div class="update-card">
                    <h2 class="text-lg font-bold"><?php echo htmlspecialchars($update['version']); ?></h2>
                    <p class="notification-date"><?php echo htmlspecialchars($update['date']); ?></p>
                    <p class="text-gray-200"><?php echo htmlspecialchars($update['description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Anda dapat menambahkan interaksi JavaScript di sini jika diperlukan
    });
    </script>
</body>
</html>