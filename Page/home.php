<?php
include "session.php";

$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : 'Guest';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .floating-food {
            position: absolute;
            animation: float 6s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .search-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
        }

        .action-button {
            background-color: #9333EA;
            transition: all 0.3s ease;
            border-radius: 25px;
        }

        .action-button:hover {
            transform: scale(1.05);
            background-color: #7C3AED;
        }

        .main-image {
            border-radius: 50%;
            box-shadow: 0 0 0px rgba(147, 51, 234, 0.3);
        }
    </style>
</head>

<body class="bg-gray-900">
    <div class="max-w-screen-xl mx-auto p-4">
        <!-- Header with Logo and Location -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-2">
                <img src="../assets/image/logo.png" alt="Restaurant Logo" class="h-8">
            </div>
            <button class="bg-purple-600 px-6 py-2 rounded-full text-white">
                <?php echo $username; ?>
            </button>
        </div>

        <!-- Location Bar -->
        <div class="mb-8">
            <p class="text-gray-400 text-sm">Lokasi</p>
            <p class="text-white">Indonesia, Aceh</p>
            <p class="text-gray-400 text-sm">Lhokseumawe, Bukit Rata</p>
        </div>

        <!-- Welcome Section -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold mb-2">Welcome</h1>
            <p class="text-xl text-gray-300">Enjoy Our delicius food</p>
            <p class="text-sm text-gray-400">Best Food made by our Passionate Chefs</p>
        </div>

        <!-- Search Bar -->
        <div class="search-container mb-8">
            <form action="/search" method="get" class="flex items-center p-2">
                <input type="text" name="query" placeholder="Search" class="w-full bg-transparent border-none focus:outline-none px-4 py-2">
                <button class="bg-purple-600 p-2 rounded-full">
                    <i class="bi bi-search text-white"></i>
                </button>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4 mb-12">
            <a href="menu.php" class="action-button py-3 px-6 text-white font-semibold text-center">
                Menu
            </a>
            <a href="ReservasiDanPickup.php" class="action-button py-3 px-6 text-white font-semibold text-center">
                Pesan
            </a>
        </div>

        <!-- Main Food Image -->
        <div class="relative mb-12">
            <!-- Floating Food Images -->
            <img src="../assets/image/makanan.png" alt="Floating Food 1" class="floating-food w-16 top-0 left-10" style="animation-delay: -2s">
            <img src="../assets/image/makanan.png" alt="Floating Food 2" class="floating-food w-16 top-20 right-10" style="animation-delay: -1s">
            <img src="../assets/image/makanan.png" alt="Floating Food 3" class="floating-food w-16 bottom-20 left-20" style="animation-delay: -3s">
            
            <!-- Main Centered Food Image -->
            <img src="../assets/image/makanan.png" alt="Main Dish" class="main-image w-64 mx-auto">
        </div>

        <!-- What We Serve Section -->
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold mb-8">Your Favourite Food</h2>
            <div class="grid grid-cols-1 gap-6">
                <div class="text-center">
                    <img src="../assets/image/iklan.png" alt="Easy to Use" class="w-50 h-50 mx-auto mb-4">
                    <h3 class="text-lg mb-2">Mudah digunakan</h3>
                    <p class="text-sm text-gray-400">Akses kapan saja dan dimana saja</p>
                </div>
                <div class="text-center">
                    <img src="../assets/image/iklan2.png" alt="Quality" class="w-50 h-50 mx-auto mb-4">
                    <h3 class="text-lg mb-2">Kualitas terbaik</h3>
                    <p class="text-sm text-gray-400">Tidak usaha ragu bagi kualitas</p>
                </div>
                <div class="text-center">
                    <img src="../assets/image/iklan.png" alt="Easy to Use" class="w-50 h-50 mx-auto mb-4">
                    <h3 class="text-lg mb-2">Mudah digunakan</h3>
                    <p class="text-sm text-gray-400">Akses kapan saja dan dimana saja</p>
                </div>
            </div>
        </div>

        <!-- Maps Section -->
        <div class="rounded-lg overflow-hidden mb-8">
        <iframe 
            src="https://maps.google.com/maps?q=Lhokseumawe,%20Bukit%20Rata&t=&z=13&ie=UTF8&iwloc=&output=embed"
            width="100%" 
            height="300" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
        <div class="contact-section p-6 text-white">
            <div class="flex items-center mb-4">
                <i class="bi bi-geo-alt text-xl mr-3 text-purple-500"></i>
                <div>
                    <p class="font-medium">Lokasi Kami:</p>
                    <p class="text-gray-300">Lhokseumawe, Bukit Rata</p>
                </div>
            </div>
            <div class="flex items-center">
                <i class="bi bi-telephone text-xl mr-3 text-purple-500"></i>
                <div>
                    <p class="font-medium">Telepon:</p>
                    <p class="text-gray-300">+62 81234567890</p>
                </div>
            </div>
        </div>
    </div>  

    <!-- Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-gray-800 p-4">
        <div class="max-w-screen-xl mx-auto flex justify-around">
            <a href="home.php" class="text-white flex flex-col items-center">
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
</body>
</html>