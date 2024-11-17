<!DOCTYPE html>
<html lang="id">
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <style>
        /* Reset dan Tampilan Mobile */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            width: 100%;
            max-width: 400px;
            margin: auto;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: space-between;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #fff;
            border-bottom: 1px solid #eee;
        }

        .header h1 {
            font-size: 18px;
            color: #555;
            margin-left: auto;
            margin-right: auto;
        }

        /* Order Summary */
        .order-summary {
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .order-summary h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .order-summary .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background-color: #fff;
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .order-summary .item img {
            width: 60px;
            height: auto;
            margin-right: 15px;
            border-radius: 10px;
        }

        .order-summary .details {
            flex: 1;
        }

        .order-summary .details p {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .order-summary .details .price {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        /* Pricing Details */
        .pricing-details {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .pricing-details p {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .pricing-details p:last-child {
            font-weight: bold;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            background-color: #fff;
            border-top: 1px solid #eee;
        }

        .navbar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            margin-top: 20px;
            background-color: white;
            border-top: 1px solid #ccc;
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
            color: #333;
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
        <h1>Order Summary</h1>
    </div>

    <!-- Order Summary -->
    <div class="order-summary">
        <h2>Show Order Summary</h2>
        <div class="item">
            <img src="../assets/allmenu/beef1.png" alt="Beef Burger">
            <div class="details">
                <p>Beef</p>
                <p>PCS : 1</p>
            </div>
            <p class="price">Rp 30.000</p>
        </div>

        <!-- Pricing Details -->
        <div class="pricing-details">
            <p>Subtotal <span>Rp 30.000</span></p>
            <p>Shipping Cost <span>Rp 1.000</span></p>
            <p>Discount (10%) <span>Rp -2.000</span></p>
            <p>Total <span>Rp 29.000</span></p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white-800 p-4">
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
