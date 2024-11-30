<?php
// Mulai session
session_start();

// Koneksi ke database MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil id_menu dari URL
$id_menu = isset($_GET['id_menu']) ? intval($_GET['id_menu']) : 0;

// Fungsi untuk membersihkan input
function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Query untuk mengambil data detail menu
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu, diskon FROM menu WHERE id_menu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_menu);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $menu = $result->fetch_assoc();
    
    // Add discount calculation here
    $originalPrice = $menu['harga'];
    $hasDiscount = isset($menu['diskon']) && $menu['diskon'] > 0;
    $discountedPrice = $hasDiscount 
        ? $originalPrice * (1 - ($menu['diskon'] / 100)) 
        : $originalPrice;
} else {
    echo "Menu tidak ditemukan.";
    exit;
}

// Query untuk mengambil menu yang paling sering dipesan
$recommended_sql = "
    SELECT m.id_menu, m.nama_menu, m.harga, m.gambar_menu, 
           COUNT(k.id_menu) as total_pesanan 
    FROM keranjang k
    JOIN menu m ON k.id_menu = m.id_menu
    WHERE m.id_menu != ? 
    GROUP BY m.id_menu, m.nama_menu, m.harga, m.gambar_menu
    ORDER BY total_pesanan DESC
    LIMIT 4
";
$recommended_stmt = $conn->prepare($recommended_sql);
$recommended_stmt->bind_param("i", $id_menu);
$recommended_stmt->execute();
$recommended_result = $recommended_stmt->get_result();

// Handler untuk add to cart
if (isset($_POST['add_to_cart'])) {
    // Periksa apakah pengguna sudah login
    if (isset($_SESSION['id_pelanggan'])) {
        $id_pelanggan = $_SESSION['id_pelanggan'];
        $quantity = intval($_POST['quantity']);

        // Cek apakah menu sudah ada di keranjang
        $cek_keranjang = "SELECT * FROM keranjang 
                          WHERE id_pelanggan = ? AND id_menu = ?";
        $stmt_cek = $conn->prepare($cek_keranjang);
        $stmt_cek->bind_param("ii", $id_pelanggan, $id_menu);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0) {
            // Update quantity jika menu sudah ada di keranjang
            $update_sql = "UPDATE keranjang 
                           SET quantity = quantity + ? 
                           WHERE id_pelanggan = ? AND id_menu = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("iii", $quantity, $id_pelanggan, $id_menu);
            
            if ($stmt_update->execute()) {
                header("Location: ?id_menu=$id_menu&status=update_success");
                exit();
            }
        } else {
            // Tambahkan menu baru ke keranjang
            $sql_insert = "INSERT INTO keranjang (id_pelanggan, id_menu, quantity) 
                           VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iii", $id_pelanggan, $id_menu, $quantity);
            
            if ($stmt_insert->execute()) {
                header("Location: ?id_menu=$id_menu&status=success");
                exit();
            }
        }
    } else {
        // Redirect ke halaman login jika belum login
        header("Location: login.php?pesan=belum_login");
        exit();
    }
}

// Tutup koneksi database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= bersihkan_input($menu['nama_menu']) ?> - Detail Menu</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .menu-detail {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
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

        .quantity-button {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .ingredients-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .ingredient-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .ingredient-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .price-container{
            margin-left: 17px;
        }

        .text-lg{
            margin-left: 22px
        }

        .ingredient-item img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 8px;
        }

        .recommended-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
            padding: 20px;
        }
        
        .recommended-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .recommended-item:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .recommended-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

         .original-price {
            text-decoration: line-through;
            color: #9CA3AF;
            margin-right: 0.5rem;
            font-size: 1.25rem;
        }

        .discount-badge {
            background-color: #FF6B6B;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 1rem;
        }

        .discounted-price {
            color: #9333EA;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen pb-24">
    <div class="max-w-screen-xl mx-auto p-4">
        <!-- Header -->
        <div class="flex items-center mb-4">
            <a href="menu.php" class="text-white text-2xl mr-4">
                <i class="bi bi-arrow-left"></i>
            </a>
            <img src="../assets/image/logo.png" alt="Restaurant Logo" class="h-8">
        </div>

        <!-- Menu Detail Card -->
        <?php if ($hasDiscount): ?>
                <div class="discount-badge absolute top-4 right-4">
                    -<?php echo $menu['diskon']; ?>%
                </div>
        <?php endif; ?>

        <div class="menu-detail rounded-xl overflow-hidden mb-6">
            <img src="../assets/allmenu/<?= bersihkan_input($menu['gambar_menu']) ?>" 
                 alt="<?= bersihkan_input($menu['nama_menu']) ?>"
                 class="w-full h-64 object-cover">

            <div class="p-4">
                <h1 class="text-2xl font-bold text-white mb-2">
                    <?= bersihkan_input($menu['nama_menu']) ?>
                </h1>
                
                <div class="price-container mb-4">
            <?php if ($hasDiscount): ?>
                <span class="original-price">
                    Rp <?php echo number_format($originalPrice, 0, ',', '.'); ?>
                </span>
                <span class="discounted-price text-3xl">
                    Rp <?php echo number_format($discountedPrice, 0, ',', '.'); ?>
                </span>
            <?php else: ?>
                <span class="text-3xl text-purple-400 font-bold">
                    Rp <?php echo number_format($originalPrice, 0, ',', '.'); ?>
                </span>
            <?php endif; ?>
        </div>

              

                <!-- Ingredients -->
                <h2 class="text-lg font-semibold text-white mb-3">Komposisi</h2>
                <div class="ingredients-grid">
                    <!-- Hardcoded ingredients for demonstration -->
                    <?php 
                    $ingredients = [
                        ['name' => 'Beef', 'image' => 'https://storage.googleapis.com/a1aa/image/qo3MM8yWFopgBRcfL2QKdbULYuZ3bpzoX1OdmBgulMcMwo3JA.jpg'],
                        ['name' => 'Lettuce', 'image' => 'https://storage.googleapis.com/a1aa/image/GKBUxdZFrFqeZC5mqfddmj3A07di7LZrdi2ySbasvmUcgRvTA.jpg'],
                        ['name' => 'Olive Oil', 'image' => 'https://storage.googleapis.com/a1aa/image/d0AyAzRSUCIrOF5nAMvWSNOWgiPMhkOJSzb5ZYATn3UFY07E.jpg'],
                        ['name' => 'Egg', 'image' => 'https://storage.googleapis.com/a1aa/image/xn8WH7kOSBKnHVI7Xkdhz94iCEqPi6fKlUKzPXNhMrlJwo3JA.jpg'],
                        ['name' => 'Tomato', 'image' => 'https://storage.googleapis.com/a1aa/image/SkGS6Z3nJrooHt7FQbVO8K85Im9cGSN92UWNp6EEgMhGY07E.jpg']
                    ];

                    foreach ($ingredients as $ingredient): ?>
                        <div class="ingredient-item">
                            <img alt="<?= $ingredient['name'] ?>" src="<?= $ingredient['image'] ?>" />
                            <span><?= $ingredient['name'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Description -->
                <h2 class="text-lg font-semibold text-white mb-3">Deskripsi</h2>
                    <div class="description-text">
                        <p class="mb-2"><?= nl2br(htmlspecialchars($menu['keterangan'])) ?></p>
                        <p>
                            Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts ...
                            <a href="#" class="text-purple-400 hover:text-purple-300">detail</a>
                        </p>
                    </div>
                </div>

                <div class="price-container mb-4">
                    <?php if ($hasDiscount): ?>
                        <span class="original-price">
                            Rp <?php echo number_format($originalPrice, 0, ',', '.'); ?>
                        </span>
                        <span class="discounted-price text-3xl">
                            Rp <?php echo number_format($discountedPrice, 0, ',', '.'); ?>
                        </span>
                    <?php else: ?>
                        <span class="text-3xl text-purple-400 font-bold">
                            Rp <?php echo number_format($originalPrice, 0, ',', '.'); ?>
                        </span>
                    <?php endif; ?>
                </div>


                <!-- Add to Cart Form -->
                <form method="POST" class="mt-6">
                    <div class="flex items-center justify-center space-x-4 mb-4">
                        <button type="button" 
                                class="quantity-button bg-red-500 text-white text-xl"
                                onclick="decreaseQuantity()">−</button>
                        <span id="quantity" class="text-xl font-medium w-12 text-center">1</span>
                        <button type="button"
                                class="quantity-button bg-green-500 text-white text-xl"
                                onclick="increaseQuantity()">+</button>
                    </div>

                    <input type="hidden" name="quantity" id="quantity_input" value="1">
                    <button type="submit" 
                            name="add_to_cart"
                            class="action-button w-full h-70 py-4 text-white font-semibold text-lg">
                        Tambahkan ke Keranjang
                    </button>
                </form>
            </div>
        </div>

        <!-- Recommended Menu Section -->
        <div>
            <!-- Updated Recommended Menu Section -->
<div>
    <h2 class="text-lg font-semibold text-white mb-4">Menu yang Sering Dipesan</h2>
    <div class="recommended-grid grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php while ($recommended = $recommended_result->fetch_assoc()): ?>
            <a href="detail_order.php?id_menu=<?= $recommended['id_menu'] ?>" 
               class="recommended-item bg-gray-800 rounded-lg overflow-hidden shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                <div class="relative">
                    <img src="../assets/allmenu/<?= bersihkan_input($recommended['gambar_menu']) ?>" 
                         alt="<?= bersihkan_input($recommended['nama_menu']) ?>"
                         class="w-full h-40 object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2">
                        <h3 class="text-white font-medium text-sm truncate">
                            <?= bersihkan_input($recommended['nama_menu']) ?>
                        </h3>
                        <p class="text-purple-300 font-bold text-xs">
                            Rp <?= number_format($recommended['harga'], 0, ',', '.') ?>
                        </p>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>
    </div>

    <!-- Navigation -->
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
        let quantity = 1;

        function increaseQuantity() {
            quantity++;
            updateQuantityDisplay();
        }

        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                updateQuantityDisplay();
            }
        }

        function updateQuantityDisplay() {
            document.getElementById('quantity').innerText = quantity;
            document.getElementById('quantity_input').value = quantity;
        }

        // Handle success message
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'success') {
            alert("Menu berhasil ditambahkan ke keranjang");
            // Optional: Remove status from URL to prevent repeated alerts
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/[\?&]status=success/, ''));
        }

        if (status === 'update_success') {
            alert("Jumlah menu di keranjang berhasil diperbarui");
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/[\?&]status=update_success/, ''));
        }

        // Optional: Add some advanced error handling
        window.onerror = function(message, source, lineno, colno, error) {
            console.error('Error:', message);
            console.error('Source:', source);
            console.error('Line:', lineno);
            console.error('Column:', colno);
            console.error('Error object:', error);
            
            // Optional: Send error to server or log
            fetch('log_error.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    source: source,
                    lineno: lineno,
                    colno: colno,
                    error: error ? error.toString() : null
                })
            });

            // Prevent default error handling
            return true;
        };

        // Add touch and click support for mobile and desktop
        document.addEventListener('DOMContentLoaded', function() {
            const quantityButtons = document.querySelectorAll('.quantity-button');
            
            quantityButtons.forEach(button => {
                // Prevent double-tap zoom on mobile
                button.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                });

                // Add active state for better touch feedback
                button.addEventListener('touchstart', function() {
                    this.classList.add('bg-opacity-70');
                });

                button.addEventListener('touchend', function() {
                    this.classList.remove('bg-opacity-70');
                });
            });

            // Optional: Image lazy loading
            const images = document.querySelectorAll('img');
            const lazyLoadOptions = {
                threshold: 0.1,
                rootMargin: '50px'
            };

            if ('IntersectionObserver' in window) {
                let imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            let image = entry.target;
                            image.src = image.dataset.src || image.src;
                            imageObserver.unobserve(image);
                        }
                    });
                }, lazyLoadOptions);

                images.forEach(img => {
                    if (img.dataset.src) {
                        imageObserver.observe(img);
                    }
                });
            }
        });

        // Performance tracking (optional)
        window.addEventListener('load', function() {
            // Measure page load time
            const pageLoadTime = window.performance.now();
            console.log(`Page loaded in ${pageLoadTime.toFixed(2)} milliseconds`);

            // Optional: Send performance data to server
            fetch('log_performance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pageLoadTime: pageLoadTime,
                    page: 'detail_order.php',
                    menuId: <?= $id_menu ?>
                })
            });
        });
    </script>
</body>
</html>