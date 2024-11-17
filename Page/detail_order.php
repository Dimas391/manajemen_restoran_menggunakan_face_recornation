<?php
include "session.php";

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

// Query untuk mengambil data detail menu
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu FROM menu WHERE id_menu = $id_menu";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $menu = $result->fetch_assoc();
} else {
    echo "Menu tidak ditemukan.";
    exit;
}

// Handler untuk add to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['id_pelanggan'])) {
        $id_pelanggan = $_SESSION['id_pelanggan'];
        $quantity = $_POST['quantity'];

        $sql_insert = "INSERT INTO keranjang (id_pelanggan, id_menu, quantity) 
                       VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("iii", $id_pelanggan, $id_menu, $quantity);
            if ($stmt->execute()) {
                header("Location: ?id_menu=$id_menu&status=success");
                exit();
            }
        }
    } else {
        echo "<script>alert('Harap login terlebih dahulu.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($menu['nama_menu']) ?> - Detail Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
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

        .menu-detail {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .quantity-button {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
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

        .ingredient-item img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 8px;
        }

        .ingredient-item span {
            font-size: 12px;
            color: #e0e0e0;
        }

        .description-text {
            color: #a0a0a0;
            font-size: 14px;
            line-height: 1.6;
        }

        .description-text a {
            color: #9333EA;
            text-decoration: none;
        }

        .description-text a:hover {
            text-decoration: underline;
        }

        .action-button {
        /* Ukuran default untuk desktop */
        width: 100%;
        height: 70px;
        padding: 1rem;
        font-size: 1.125rem; /* 18px */
    }

    /* Styling untuk perangkat mobile */
    @media (max-width: 768px) {
        .action-button {
        width: 100%; /* Lebar tombol */
        max-width: 300px; /* Batas maksimal lebar tombol */
        height: 50px; /* Tinggi tombol */
        padding: 0.9rem; /* Padding */
        font-size: 0.875rem; /* Ukuran font */
        margin: 0 auto; /* Memusatkan tombol */
        text-align: center; /* Teks di dalam tombol */
        display: flex; /* Flexbox */
        justify-content: center; /* Teks tombol horizontal */
        align-items: center; /* Teks tombol vertikal */
        background-color: #9333EA; /* Warna tombol */
        color: white; /* Warna teks */
        border: none; /* Hapus border default */
        border-radius: 20px; /* Lebih melengkung */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Opsional: tambahkan bayangan */
        cursor: pointer; /* Ubah kursor saat hover */
        transition: background-color 0.3s ease; /* Efek transisi */
    }

    .action-button:hover {
        background-color: #7e22ce; /* Warna tombol saat di-hover */
    }
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
        <div class="menu-detail rounded-xl overflow-hidden mb-6">
            <img src="../assets/allmenu/<?= htmlspecialchars($menu['gambar_menu']) ?>" 
                 alt="<?= htmlspecialchars($menu['nama_menu']) ?>"
                 class="w-full h-64 object-cover">

            <div class="p-4">
                <h1 class="text-2xl font-bold text-white mb-2">
                    <?= htmlspecialchars($menu['nama_menu']) ?>
                </h1>
                
                <p class="text-xl text-purple-400 mb-4">
                    Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                </p>

                <!-- Ingredients with Images -->
                <h2 class="text-lg font-semibold text-white mb-3">Komposisi</h2>
                <div class="ingredients-grid">
                    <div class="ingredient-item">
                        <img alt="Beef" src="https://storage.googleapis.com/a1aa/image/qo3MM8yWFopgBRcfL2QKdbULYuZ3bpzoX1OdmBgulMcMwo3JA.jpg" />
                        <span>Beef</span>
                    </div>
                    <div class="ingredient-item">
                        <img alt="Lettuce" src="https://storage.googleapis.com/a1aa/image/GKBUxdZFrFqeZC5mqfddmj3A07di7LZrdi2ySbasvmUcgRvTA.jpg" />
                        <span>Lettuce</span>
                    </div>
                    <div class="ingredient-item">
                        <img alt="Olive Oil" src="https://storage.googleapis.com/a1aa/image/d0AyAzRSUCIrOF5nAMvWSNOWgiPMhkOJSzb5ZYATn3UFY07E.jpg" />
                        <span>Olive Oil</span>
                    </div>
                    <div class="ingredient-item">
                        <img alt="Egg" src="https://storage.googleapis.com/a1aa/image/xn8WH7kOSBKnHVI7Xkdhz94iCEqPi6fKlUKzPXNhMrlJwo3JA.jpg" />
                        <span>Egg</span>
                    </div>
                    <div class="ingredient-item">
                        <img alt="Tomato" src="https://storage.googleapis.com/a1aa/image/SkGS6Z3nJrooHt7FQbVO8K85Im9cGSN92UWNp6EEgMhGY07E.jpg" />
                        <span>Tomato</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-white mb-3">Deskripsi</h2>
                    <div class="description-text">
                        <p class="mb-2"><?= nl2br(htmlspecialchars($menu['keterangan'])) ?></p>
                        <p>
                            Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts ...
                            <a href="#" class="text-purple-400 hover:text-purple-300">detail</a>
                        </p>
                    </div>
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
            window.location.href = 'menu.php';
        }
    </script>
</body>
</html>