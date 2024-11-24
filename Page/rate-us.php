<?php
include 'session.php'; // Pastikan ini ada di bagian atas

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai rating dan komentar dari form
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Simpan rating dan komentar ke database (logika penyimpanan tidak ditampilkan di sini)
    // Misalnya, Anda bisa menggunakan prepared statements untuk menyimpan data ke database

    // Redirect atau tampilkan pesan sukses
    $_SESSION['success'] = "Thank you for your feedback!";
    header("Location: rate_us.php"); // Redirect ke halaman yang sama untuk menampilkan pesan
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Us | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .rate-us-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
        }

        .rate-us-header {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .rating-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .rating-card:hover {
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
    <div class="rate-us-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
        </button>

        <!-- Rate Us Header -->
        <div class="rate-us-header">
            <h1 class="text-2xl font-bold mb-2">Rate Us</h1>
            <p class="text-gray-200">We value your feedback!</p>
        </div>

        <!-- Rating Form -->
        <form method="POST" class="rating-card">
            <div class="mb-4">
                <label for="rating" class="block text-gray-200 mb-2">Rating:</label>
                <select name="rating" id="rating" class="bg-gray-800 text-white border border-gray-600 rounded p-2">
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="comment" class="block text-gray-200 mb-2">Comment:</label>
                <textarea name="comment" id="comment" rows="4" class="bg-gray-800 text-white border border-gray-600 rounded p-2" placeholder="Your comments..."></textarea>
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Submit Feedback
            </button>
        </form>

        <!-- Display Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mt-4 bg-green-500 text-white p-3 rounded">
                <?php
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); // Hapus pesan setelah ditampilkan
                ?>
            </div>
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