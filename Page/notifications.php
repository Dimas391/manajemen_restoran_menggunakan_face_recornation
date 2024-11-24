<?php
include 'session.php'; // Ensure session is started

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fetch notifications for the logged-in user
$id_pelanggan = $_SESSION['id_pelanggan'];
$sql = "SELECT message, date FROM notifications WHERE id_pelanggan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .notifications-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
        }

        .notifications-header {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .notification-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .notification-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.15);
        }

        .notification-date {
            font-size: 0.8rem;
            color: #bbb;
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

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #6B7280;
            transition: color 0.3s ease;
        }

        .nav-item.active {
            color: #9333EA;
        }

        .nav-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="notifications-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
        </button>

        <!-- Notifications Header -->
        <div class="notifications-header">
            <h1 class="text-2xl font-bold mb-2">Notifications</h1>
            <p class="text-gray-200">Here are your notifications</p>
        </div>

        <!-- Notifications List -->
        <?php if (empty($notifications)): ?>
            <div class="notification-card">
                <p class="text-gray-200">No notifications available.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card">
                    <p class="text-gray-200"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <p class="notification-date"><?php echo htmlspecialchars($notification['date']); ?></p>
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
            const notificationCards = document.querySelectorAll('.notification-card');
            notificationCards.forEach(card => {
                card.addEventListener('click', function() {
                    // You can add any action when a notification is clicked
                    // For example, redirect to a specific page or show more details
                });
            });
        });
    </script>
</body>
</html>