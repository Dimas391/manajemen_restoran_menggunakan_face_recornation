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
$sql = "SELECT k.id_keranjang, k.id_menu, k.quantity, m.nama_menu, m.harga, m.gambar_menu, m.diskon
        FROM keranjang k
        JOIN menu m ON k.id_menu = m.id_menu
        WHERE k.id_pelanggan = $id_pelanggan";

$result = $conn->query($sql);

// Jika produk ditemukan
$products = [];
$totalPrice = 0; // Inisialisasi total harga
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;

        // Hitung subtotal dengan diskon
        $subtotal = (int)$row['harga'] * (int)$row['quantity'];
        if ($row['diskon'] > 0) {
            $subtotal *= (1 - ($row['diskon'] / 100)); // Terapkan diskon
        }
        $totalPrice += $subtotal; // Tambahkan ke total harga
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

        .delete-button {
            background-color: #dc2626;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            margin-top: -8px;
        }
        .delete-button:hover {
            transform: scale(1.1);
            background-color: #b91c1c;
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
    <?php foreach ($products as $index => $product) : 
         $originalSubtotal = (int)$product['harga'] * (int)$product['quantity'];
         $discountedSubtotal = $originalSubtotal;
         if ($product['diskon'] > 0) {
             $discountedSubtotal = $originalSubtotal * (1 - ($product['diskon'] / 100));
         }
         ?>
    <div class="cart-item rounded-xl p-4 relative">
            <button type="button" 
                    class="delete-button absolute top-2 right-2" 
                    data-id-menu="<?= $product['id_menu'] ?>">
                <i class="bi bi-trash"></i>
            </button>
        <div class="cart-item-content flex items-center justify-between">
            <div class="cart-item-details flex items-center space-x-4">
                <img src="../assets/allmenu/<?= htmlspecialchars($product['gambar_menu']) ?>" 
                     alt="<?= htmlspecialchars($product['nama_menu']) ?>"
                     class="w-20 h-20 rounded-lg object-cover">
                
                     <div>
                <h3 class="text-lg font-semibold text-white">
                    <?= htmlspecialchars($product['nama_menu']) ?>
                </h3>
                <!-- Price with discount -->
                <div class="price-container">
                    <?php if ($product['diskon'] > 0): ?>
                        <span class="text-gray-400 line-through mr-2">
                            Rp <?= number_format($originalSubtotal, 0, ',', '.') ?>
                        </span>
                        <span class="text-purple-400 font-semibold">
                            Rp <?= number_format($discountedSubtotal, 0, ',', '.') ?>
                        </span>
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                            -<?= $product['diskon'] ?>%
                        </span>
                    <?php else: ?>
                        <span class="text-purple-400">
                            Rp <?= number_format($originalSubtotal, 0, ',', '.') ?>
                        </span>
                    <?php endif; ?>
                </div>
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
                    Rp <?= number_format($discountedSubtotal, 0, ',', '.') ?>
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
        <div>
            <?php 
            $totalOriginalPrice = array_sum(array_map(function($p) { 
                return $p['harga'] * $p['quantity']; 
            }, $products));
            $totalDiscountedPrice = array_sum(array_map(function($p) { 
                $subtotal = $p['harga'] * $p['quantity'];
                return $p['diskon'] > 0 ? $subtotal * (1 - ($p['diskon'] / 100)) : $subtotal; 
            }, $products));
            ?>
            <?php if ($totalOriginalPrice != $totalDiscountedPrice): ?>
                <div>
                    <span class="text-gray-400 line-through mr-2">
                        Rp <?= number_format($totalOriginalPrice, 0, ',', '.') ?>
                    </span>
                    <span class="text-2xl font-bold text-purple-400">
                        Rp <?= number_format($totalDiscountedPrice, 0, ',', '.') ?>
                    </span>
                </div>
                <div class="text-right text-green-500 text-sm">
                    Hemat Rp <?= number_format($totalOriginalPrice - $totalDiscountedPrice, 0, ',', '.') ?>
                </div>
            <?php else: ?>
                <span id="total-price" class="text-2xl font-bold text-purple-400">
                    Rp <?= number_format($totalDiscountedPrice, 0, ',', '.') ?>
                </span>
            <?php endif; ?>
        </div>
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
        // Fetch Midtrans Snap token
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
                    try {
                        const processResponse = await fetch('process_payment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });

                        const processResult = await processResponse.json();

                        if (processResult.status === 'success') {
                    const totalHargaAsli = processResult.total_harga_asli || 0; // Default ke 0 jika undefined
                    const totalHargaDiskon = processResult.total_harga_diskon || 0; // Default ke 0 jika undefined
                    const hemat = processResult.hemat || 0; // Default ke 0 jika undefined

                    // Detailed success alert
                    const alertMessage = `
                    Pembayaran Berhasil!
                    Nomor Pesanan: ${processResult.order_number}

                    Total Harga Awal: Rp ${totalHargaAsli.toLocaleString()}
                    Total Harga Diskon: Rp ${totalHargaDiskon.toLocaleString()}
                    Hemat: Rp ${hemat.toLocaleString()}
                    `;

                    alert(alertMessage);
                    window.location.href = 'riwayat_pembelian.php';
                } else {
                    alert('Gagal memproses pesanan: ' + processResult.message);
                }
                    } catch (processError) {
                        console.error('Processing Error:', processError);
                        alert('Gagal memproses pesanan');
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
    
    document.querySelectorAll('.quantity-controls').forEach(control => {
        const index = control.dataset.index;
        const idMenu = control.dataset.idMenu;
        
        control.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const action = button.dataset.action;
                const quantitySpan = control.querySelector('.quantity-value');
                if (!quantitySpan) {
                    console.error('Quantity span not found');
                    return;
                }

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
                        
                        console.log('Server Response:', result);
                        
                        if (result.status === 'success') {
                            // Update displayed quantity
                            quantitySpan.textContent = newQuantity;
                            
                            // Safely find the cart item and subtotal elements
                            const cartItem = control.closest('.cart-item');
                            if (!cartItem) {
                                console.error('Cart item not found');
                                return;
                            }
                            
                            const subtotalCell = cartItem.querySelector('.subtotal');
                            if (!subtotalCell) {
                                console.error('Subtotal cell not found');
                                return;
                            }
                            
                            // Update subtotal
                            const price = cartData[index].harga;
                            const discountPercentage = cartData[index].diskon || 0;
                            const originalSubtotal = price * newQuantity;
                            const newSubtotal = discountPercentage > 0 
                                ? originalSubtotal * (1 - (discountPercentage / 100)) 
                                : originalSubtotal;
                            
                            subtotalCell.textContent = `Rp ${Math.round(newSubtotal).toLocaleString('id-ID')}`;
                            
                            // Update total price
                            updateTotalPrice();
                        } else {
                            console.error('Update failed:', result.message);
                            alert('Gagal mengupdate quantity: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Fetch Error:', error);
                        alert('Terjadi kesalahan saat mengupdate quantity');
                    }
                }
            });
        });
    });

    document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const idMenu = button.dataset.idMenu;
                const cartItem = button.closest('.cart-item');

                try {
                    const response = await fetch('delete_keranjang.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_menu: idMenu
                        })
                    });
                    
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        // Remove the cart item from the DOM
                        cartItem.remove();
                        
                        // Update total price and items count
                        updateTotalPrice();
                        updateItemCount();
                    } else {
                        console.error('Delete failed:', result.message);
                        alert('Gagal menghapus item: ' + result.message);
                    }
                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('Terjadi kesalahan saat menghapus item');
                }
            });
        });

        function updateItemCount() {
            // Update total items count
            const cartItems = document.querySelectorAll('.cart-item');
            const itemCountElement = document.querySelector('div.bg-gray-800 .flex:first-child span:last-child');
            
            if (itemCountElement) {
                itemCountElement.textContent = cartItems.length;
            }

            // If no items, maybe show an empty cart message
            if (cartItems.length === 0) {
                const cartContainer = document.querySelector('.space-y-4.mb-8');
                cartContainer.innerHTML = `
                    <div class="text-center text-gray-400 py-12">
                        <i class="bi bi-cart text-6xl mb-4 block"></i>
                        <p>Keranjang Anda kosong</p>
                        <a href="ReservasiDanPickup.php" class="action-button inline-block mt-4 px-6 py-2">
                            Mulai Belanja
                        </a>
                    </div>
                `;
            }
        }
    });
    
    function updateTotalPrice() {
    try {
        const subtotals = Array.from(document.querySelectorAll('.subtotal'))
            .map(cell => {
                const value = cell.textContent.replace(/[^\d]/g, '');
                return value ? parseInt(value) : 0;
            });

        const total = subtotals.reduce((sum, subtotal) => sum + subtotal, 0);

        // Pastikan elemen total harga ada
        const totalPriceElement = document.getElementById('total-price');
        
        if (totalPriceElement) {
            totalPriceElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        } else {
            console.error('Total price element not found');
        }
    } catch (error) {
        console.error('Error in updateTotalPrice:', error);
    }
}
</script>
</body>
</html>