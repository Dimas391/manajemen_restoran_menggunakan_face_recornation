<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

include "session.php";

// Pastikan pengguna sudah login dan memiliki session
if (!isset($_SESSION['id_pelanggan'])) {
    echo "Anda harus login terlebih dahulu!";
    exit;
}

// Ambil ID pelanggan dari session
$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil data produk dari keranjang berdasarkan ID pelanggan
$sql = "SELECT k.id_keranjang, k.id_menu, k.quantity, m.nama_menu, m.harga, m.gambar_menu
        FROM keranjang k
        JOIN menu m ON k.id_menu = m.id_menu
        WHERE k.id_pelanggan = $id_pelanggan";

$result = $conn->query($sql);

// Jika produk ditemukan
$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-hlSrcA-fJy90gGT4"></script>
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

        .quantity-button {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .quantity-button:hover {
            transform: scale(1.1);
        }

        .cart-item {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.cart-item:hover {
    background: rgba(255, 255, 255, 0.08);
}

.quantity-button {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 1.25rem;
    -webkit-tap-highlight-color: transparent;
}

@media (max-width: 640px) {
    .cart-item-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .cart-item-details {
        width: 100%;
    }
    
    .cart-item-controls {
        width: 100%;
        height: 50px;
        justify-content: space-between;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

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

<body class="bg-gray-900">
    <div class="max-w-screen-xl mx-auto p-4 min-h-screen pb-24">
        <!-- Header -->
        <div class="flex items-center mb-8">
            <a href="home.php" class="text-white text-2xl mr-4">
                <i class="bi bi-arrow-left"></i>
            </a>
            <img src="../assets/image/logo.png" alt="Restaurant Logo" class="h-8">
        </div>

        <!-- Cart Title -->
        <h1 class="text-2xl font-bold mb-8 text-center text-purple-400">Keranjang Belanja</h1>

        <!-- Cart Items -->
        <!-- Cart Items -->
<div class="space-y-4 mb-8">
    <?php foreach ($products as $index => $product) : ?>
    <div class="cart-item rounded-xl p-4">
        <div class="cart-item-content flex items-center justify-between">
            <div class="cart-item-details flex items-center space-x-4">
                <img src="../assets/allmenu/<?= htmlspecialchars($product['gambar_menu']) ?>" 
                     alt="<?= htmlspecialchars($product['nama_menu']) ?>"
                     class="w-20 h-20 rounded-lg object-cover">
                
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        <?= htmlspecialchars($product['nama_menu']) ?>
                    </h3>
                    <p class="text-purple-400">
                        Rp <?= number_format((int)$product['harga'], 0, ',', '.') ?>
                    </p>
                </div>
            </div>

            <div class="cart-item-controls flex items-center space-x-4 sm:space-x-6">
                <!-- Quantity Controls -->
                <div class="quantity-controls flex items-center space-x-3 bg-gray-800 rounded-lg p-2"
                     data-index="<?= $index ?>" data-id-menu="<?= $product['id_menu'] ?>">
                    <button type="button" 
                            class="quantity-button bg-red-500 text-white" 
                            data-action="decrease">
                        <span class="text-xl">−</span>
                    </button>
                    <span class="quantity-value text-lg font-medium w-8 text-center">
                        <?= (int)$product['quantity'] ?>
                    </span>
                    <button type="button" 
                            class="quantity-button bg-green-500 text-white" 
                            data-action="increase">
                        <span class="text-xl">+</span>
                    </button>
                </div>

                <!-- Subtotal -->
                <div class="text-right min-w-[100px]">
                    <p class="text-sm text-gray-400">Subtotal</p>
                    <p class="subtotal text-lg font-semibold text-white">
                        Rp <?= number_format((int)$product['harga'] * (int)$product['quantity'], 0, ',', '.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

        <!-- Total and Checkout -->
        <div class="bg-gray-800 rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-400">Total Items:</span>
                <span class="text-white font-semibold"><?= count($products) ?></span>
            </div>
            
            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-400">Total Harga:</span>
                <span id="total-price" class="text-2xl font-bold text-purple-400">
                    Rp <?= number_format(array_sum(array_map(function($p) { 
                        return $p['harga'] * $p['quantity']; 
                    }, $products)), 0, ',', '.') ?>
                </span>
            </div>

        <button id="checkout-button" class="action-button w-full h-70 py-4 text-white font-semibold text-lg">
         Lanjutkan ke Pembayaran
        </button>
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
            <a href="keranjang.php" class="text-white flex flex-col items-center">
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
document.getElementById('checkout-button').addEventListener('click', async function() {
    try {
        // First, get the Midtrans Snap token (from ewallet.php)
        const snapResponse = await fetch('ewallet.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const snapResult = await snapResponse.json();

        if (snapResult.status === 'success') {
            // Call Midtrans Snap payment
            snap.pay(snapResult.snapToken, {
                onSuccess: async function(result) {
                    console.log('Payment Success:', result);
                    
                    // Process payment and move cart to history
                    const processResponse = await fetch('process_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const processResult = await processResponse.json();

                    if (processResult.status === 'success') {
                        // Redirect to order confirmation or show success message
                        alert('Pembayaran Berhasil! Nomor Pesanan: ' + processResult.order_number);
                        window.location.href = 'riwayat_pembelian.php'; // Optional: redirect to order history
                    } else {
                        alert('Gagal memproses pesanan: ' + processResult.message);
                    }
                },
                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    alert('Pembayaran sedang diproses');
                },
                onError: function(result) {
                    console.log('Payment Error:', result);
                    alert('Pembayaran gagal');
                }
            });
        } else {
            alert('Gagal mendapatkan Snap Token: ' + snapResult.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran');
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartData = <?= json_encode($products, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    
    // Add event listeners to all quantity controls
    document.querySelectorAll('.quantity-controls').forEach(control => {
        const index = control.dataset.index;
        const idMenu = control.dataset.idMenu;
        
        control.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const action = button.dataset.action;
                const quantitySpan = control.querySelector('.quantity-value');
                let currentQuantity = parseInt(quantitySpan.textContent);
                
                // Calculate new quantity
                let newQuantity = currentQuantity;
                if (action === 'increase') {
                    newQuantity = currentQuantity + 1;
                } else if (action === 'decrease' && currentQuantity > 1) {
                    newQuantity = currentQuantity - 1;
                }
                
                // Only proceed if quantity changed
                if (newQuantity !== currentQuantity) {
                    try {
                        const response = await fetch('update_keranjang.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id_menu: idMenu,
                                quantity: newQuantity
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.status === 'success') {
                            // Update displayed quantity
                            quantitySpan.textContent = newQuantity;
                            
                            // Update subtotal
                            const price = cartData[index].harga;
                            const newSubtotal = price * newQuantity;
                            const subtotalCell = control.closest('.cart-item').querySelector('.subtotal');
                            subtotalCell.textContent = `Rp ${newSubtotal.toLocaleString('id-ID')}`;
                            
                            // Update total price
                            updateTotalPrice();
                        } else {
                            console.error('Update failed:', result.message);
                            alert('Gagal mengupdate quantity: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengupdate quantity');
                    }
                }
            });
        });
    });
    
    function updateTotalPrice() {
        const subtotals = Array.from(document.querySelectorAll('.subtotal'))
            .map(cell => parseInt(cell.textContent.replace(/[^\d]/g, '')));
        const total = subtotals.reduce((sum, subtotal) => sum + subtotal, 0);
        document.getElementById('total-price').textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }
});
</script>
</body>
</html>