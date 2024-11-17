<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a; /* Latar belakang gelap */
            color: #ffffff; /* Teks putih */
            max-width: 400px;
            margin: auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #2c2c2c; /* Header gelap */
            border-bottom: 1px solid #444;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
            color: #ffffff; /* Teks putih */
        }

        .header a {
            text-decoration: none;
            color: #ffffff; /* Teks putih */
            font-size: 24px;
        }

        /* Review Section */
        .review-section {
            padding: 20px;
            background: #2c2c2c; /* Latar belakang gelap */
            margin-top: 10px;
            border-bottom: 1px solid #444;
        }

        .review-section .total {
            font-size: 16px;
            color: #bbb; /* Teks abu-abu */
        }

        .review-section .amount {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Business Info Section */
        .business-info {
            padding: 20px;
            background: #2c2c2c; /* Latar belakang gelap */
            margin-top: 10px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #444;
        }

        .business-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .business-info .details h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .business-info .details p {
            font-size: 14px;
            color: #bbb; /* Teks abu-abu */
        }

        .business-info .details .verified {
            color: #0d6efd; /* Warna biru untuk terverifikasi */
            font-size: 14px;
            display: inline-block;
            margin-left: 5px;
        }

        /* Payment Method */
        .payment-method {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c2c2c; /* Latar belakang gelap */
            margin-top: 10px;
        }

        .payment-method img {
            width: 50px;
        }

        .payment-method p {
            font-size: 16px;
            font-weight: bold;
        }

        /* Confirm Button Container */
        .confirm-btn-container {
            margin-top: auto; /* Posisikan tombol di bagian bawah */
            padding: 20px;
            background: #2c2c2c; /* Latar belakang gelap */
            border-top: 1px solid #444;
        }

        /* Confirm Button */
        .confirm-btn {
            background: linear-gradient(90deg, #9333EA, #7C3AED); /* Gradien ungu */
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .confirm-btn:hover {
            background: linear-gradient(90deg, #7C3AED, #9333EA); /* Gradien ungu        }

.confirm-btn span {
    flex-grow: 1;
    text-align: center;
    font-size: 16px;
}

.confirm-btn .price {
    font-size: 16px;
    font-weight: bold;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-around;
    padding: 10px 0;
    margin-top: auto;
    background-color: #2c2c2c; /* Latar belakang navbar gelap */
    border-top: 1px solid #444;
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
    color: #ffffff; /* Teks putih */
}

.navbar img {
    width: 25px;
    height: 25px;
    margin-bottom: 5px;
}
</style>
</head>
<body>
<!-- Header -->
<div class="header">
<a href="isinominal.php">&larr;</a>
<h1>Review Pembayaran</h1>
</div>

<!-- Review Section -->
<div class="review-section">
<p class="total">TOTAL PEMBAYARAN</p>
<p class="amount">Rp<?php echo number_format($_POST['nominal'] ?? 0, 0, ',', '.'); ?></p>
</div>

<!-- Business Info Section -->
<div class="business-info">
<img src="../assets/image/icon/icon_avatar.png" alt="Dimas - Siantar">
<div class="details">
    <h2>Dimas - Siantar</h2>
    <p>Siantar</p>
    <span class="verified">✔️ Terverifikasi</span>
</div>
</div>

<!-- Payment Method -->
<div class="payment-method">
<p><?php echo htmlspecialchars($_POST['payment_method'] ?? ''); ?></p>
<?php
// Tentukan gambar sesuai metode pembayaran
$payment_method = $_POST['payment_method'] ?? ''; // Ambil metode pembayaran dari form
$image_map = [
    'Paypal' => '../assets/image/paypal_confid.png',
    'GooglePay' => '../assets/image/google_pay.png',
    'ApplePay' => '../assets/image/pple_pay.png',
    'QRIS' => '../assets/image/scanbarcode.png',
];

// Cek apakah metode pembayaran ada di array, gunakan gambar default jika tidak ada
$payment_image = $image_map[$payment_method] ?? 'default_payment.png';
?>
<img src="<?php echo htmlspecialchars($payment_image); ?>" alt="<?php echo htmlspecialchars($payment_method); ?>">
</div>

<!-- Confirm Button -->
<form action="proses_pembayaran.php" method="POST">
<input type="hidden" name="nominal" value="<?php echo htmlspecialchars($_POST['nominal'] ?? ''); ?>">
<input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($_POST['payment_method'] ?? ''); ?>">
<div class="confirm-btn-container">
    <button type="submit" class="confirm-btn">
        <span>KONFIRMASI & BAYAR</span>
        <span class="price">Rp<?php echo number_format($_POST['nominal'] ?? 0, 0, ',', '.'); ?></span>
    </button>
</div>
</form>

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

</div>
</body>
</html>