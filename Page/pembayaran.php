<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            padding-bottom: 80px;
        }

        .payment-option {
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .payment-option {
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
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

        .payment-image {
            width: 100px;
            height: 40px;
            object-fit: contain;
            background: white;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .payment-image.dark {
            background: transparent;
            filter: brightness(0) invert(1);
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
    </style>
</head>

<body class="bg-gray-900">
    <div class="max-w-screen-xl mx-auto p-4">
        <!-- Header with Back Button and Title -->
        <div class="flex items-center gap-4 mb-8">
            <button onclick="history.back()" class="text-white text-xl">
                <i class="bi bi-arrow-left"></i>
            </button>
            <h1 class="text-xl font-semibold text-white">Pembayaran</h1>
        </div>

        <!-- Payment Instructions -->
        <div class="mb-6">
            <p class="text-gray-300 text-sm">Pilih metode pembayaran yang Anda inginkan:</p>
        </div>

        <!-- Payment Form -->
        <form action="isinominal.php" method="POST" class="space-y-4">
            <!-- Payment Options -->
            <!-- Payment Options -->
<div class="space-y-4">
    <!-- Paypal -->
    <div class="payment-option rounded-xl p-4 border border-gray-700">
        <label class="flex items-center justify-between cursor-pointer">
            <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="Paypal" required
                       class="form-radio h-5 w-5 text-purple-600">
                <span class="text-white text-lg">Dana</span>
            </div>
            <div class="bg-white p-2 rounded-lg flex items-center justify-center" style="width: 50px; height: 50px;">
                <img src="../assets/image/danaicon.png" alt="Paypal" 
                     class="h-full w-full object-contain">
            </div>
        </label>
    </div>

    <!-- Google Pay -->
    <div class="payment-option rounded-xl p-4 border border-gray-700">
        <label class="flex items-center justify-between cursor-pointer">
            <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="GooglePay" required
                       class="form-radio h-5 w-5 text-purple-600">
                <span class="text-white text-lg">Gopayy</span>
            </div>
            <div class="bg-white p-2 rounded-lg flex items-center justify-center" style="width: 50px; height: 50px;">
                <img src="../assets/image/gopayicon.png" alt="Google Pay" 
                     class="h-full w-full object-contain">
            </div>
        </label>
    </div>

    <!-- Apple Pay -->
    <div class="payment-option rounded-xl p-4 border border-gray-700">
        <label class="flex items-center justify-between cursor-pointer">
            <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="ApplePay" required
                       class="form-radio h-5 w-5 text-purple-600">
                <span class="text-white text-lg">GooglePay</span>
            </div>
            <div class="bg-white p-2 rounded-lg flex items-center justify-center" style="width: 50px; height: 50px;">
                <img src="../assets/image/google_pay.png" alt="ApplePay" 
                     class="h-full w-full object-contain">
            </div>
        </label>
    </div>

    <!-- QRIS -->
    <div class="payment-option rounded-xl p-4 border border-gray-700">
        <label class="flex items-center justify-between cursor-pointer">
            <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="QRIS" required
                       class="form-radio h-5 w-5 text-purple-600">
                <span class="text-white text-lg">ApplePay</span>
            </div>
            <div class="bg-white p-2 rounded-lg flex items-center justify-center" style="width: 50px; height: 50px;">
                <img src="../assets/image/apple_pay.png" alt="QRIS" 
                     class="h-full w-full object-contain">
            </div>
        </label>
    </div>
    <div class="payment-option rounded-xl p-4 border border-gray-700">
        <label class="flex items-center justify-between cursor-pointer">
            <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="QRIS" required
                       class="form-radio h-5 w-5 text-purple-600">
                <span class="text-white text-lg">QRIS</span>
            </div>
            <div class="bg-white p-2 rounded-lg flex items-center justify-center" style="width: 50px; height: 50px;">
                <img src="../assets/image/scan.png" alt="QRIS" 
                     class="h-full w-full object-contain">
            </div>
        </label>
    </div>
</div>
            <!-- Next Button -->
            <button type="submit" 
                    class="action-button w-full py-4 px-6 text-white font-semibold text-center mt-8 text-lg">
                Lanjutkan Pembayaran
            </button>
        </form>
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
</body>
</html>