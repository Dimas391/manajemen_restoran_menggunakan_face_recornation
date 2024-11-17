<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Nominal</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 20px;
        }

        .header {
            padding: 15px;
            background-color: #2c2c2c;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #ffffff;
            text-align: center;
        }

        .profile-section {
            text-align: center;
            margin: 20px 0;
        }

        .profile-section h2 {
            font-size: 20px;
            font-weight: bold;
        }

        .profile-section p {
            font-size: 14px;
            color: #bbb;
        }

        .amount {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 20px;
        }

        .keypad button {
            font-size: 20px;
            background-color: #2c2c2c;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            color: #ffffff;
            transition: background-color 0.3s;
        }

        .keypad button:hover {
            background-color: #444;
        }

        .confirm-btn {
            background: linear-gradient(90deg, #9333EA, #7C3AED);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            margin: 20px 0;
            cursor: pointer;
            text-align: center;
            width: 100%;
            transition: background 0.3s;
        }

        .confirm-btn:hover {
            background: linear-gradient(90deg, #7C3AED, #9333EA);
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            margin-top: auto;
            background-color: #2c2c2c;
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
            color: #ffffff;
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
        <h1>Isi Nominal</h1>
    </div>

    <!-- Profile Section -->
    <div class="profile-section">
        <h2>Dimas - Siantar</h2>
        <p>Siantar</p>
        <div class="amount">Rp<span id="display">0</span></div>
    </div>

    <!-- Keypad Section -->
    <div class="keypad">
        <button onclick="appendNumber(1)">1</button>
        <button onclick="appendNumber(2)">2</button>
        <button onclick="appendNumber(3)">3</button>
        <button onclick="appendNumber(4)">4</button>
        <button onclick="appendNumber(5)">5</button>
        <button onclick="appendNumber(6)">6</button>
        <button onclick="appendNumber(7)">7</button>
        <button onclick="appendNumber(8)">8</button>
        <button onclick="appendNumber(9)">9</button>
        <button        <button onclick="appendNumber(0)">0</button>
        <button onclick="appendNumber('000')">000</button>
        <button onclick="clearDisplay()">❌</button>
    </div>

    <!-- Form untuk Konfirmasi -->
    <form action="konfirmasi_bayar.php" method="POST">
        <!-- Input tersembunyi untuk nominal -->
        <input type="hidden" id="nominalInput" name="nominal" value="0">

        <!-- Input tersembunyi untuk metode pembayaran -->
        <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($_POST['payment_method'] ?? ''); ?>">

        <!-- Tombol Konfirmasi -->
        <div class="confirm-btn-container">
            <button class="confirm-btn" type="submit" onclick="setNominalValue()">Konfirmasi</button>
        </div>
    </form>

    <!-- Navbar -->
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
        let displayElement = document.getElementById("display");
        let nominalInput = document.getElementById("nominalInput");

        function appendNumber(num) {
            if (displayElement.innerText === "0") {
                displayElement.innerText = num;
            } else {
                displayElement.innerText += num;
            }
        }

        function clearDisplay() {
            displayElement.innerText = "0";
        }

        function setNominalValue() {
            // Atur nilai input tersembunyi sebelum formulir dikirim
            nominalInput.value = displayElement.innerText;
        }
    </script>
</body>
</html>