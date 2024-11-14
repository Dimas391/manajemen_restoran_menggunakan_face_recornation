<?php
// Koneksi ke database MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data menu yang gambar menunya berisi kata "dessert"
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu FROM menu WHERE gambar_menu LIKE '%dessert%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <style>
                * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f3f3f3;
            color: #333;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }
        .header {
            position: relative;
            background-image: url('banner.jpg');
            background-size: cover;
            height: 150px;
            border-radius: 0 0 20px 20px;
            overflow: hidden;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #fff;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
        }
        .back-button img {
            width: 18px;
            height: 18px;
        }
        .menu-title {
            padding: 20px 0 0;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }
        .tabs {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            overflow-x: auto;
        }
        .tab {
            padding: 10px 15px;
            border-radius: 20px;
            font-size: 14px;
            background-color: #eeeeee;
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
        }
        .tab.active {
            background-color: #C828DD;
            color: white;
        }
        .menu-scroll {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .menu-item {
            display: block;
            background-color: #1a1a1a;
            color: white;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 8px rgb(0, 0, 0, 0.1);
            text-decoration: none;
            transition: transform 0.2s;
        }
        .menu-item:hover {
            transform: scale(1.05);
            background-color: #333333;
        }
        .menu-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .menu-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .menu-item p {
            font-size: 12px;
            color: #c7c7c7;
            margin-bottom: 10px;
        }
        .menu-item .price {
            font-size: 16px;
            font-weight: bold;
            background-color: #333;
            color: #ffffff;
            padding: 8px;
            border-radius: 10px;
            display: inline-block;
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
        .navbar a {
        text-decoration: none;
        color: inherit; /* Agar warna teks mengikuti warna elemen induknya */
        }

    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="back-button">
            <a href="home.php">
                <img src="../assets/image/icon/Arrow_Left.png" alt="Back" />
            </a>
        </div>
    </div>
    <div class="menu-title">Menu</div>

    <div class="tabs">
        <button class="tab"><a href="menu.php" style="text-decoration: none; color: inherit;">All</a></button>
        <button class="tab"><a href="chicken.php" style="text-decoration: none; color: inherit;">Chicken</a></button>
        <button class="tab"><a href="beef.php" style="text-decoration: none; color: inherit;">Beef</a></button>
        <button class="tab"><a href="vegan.php" style="text-decoration: none; color: inherit;">Vegetarian</a></button>
        <button class="tab"><a href="drink.php" style="text-decoration: none; color: inherit;">drink</a></button>
        <button class="tab active"><a href="dessert.php" style="text-decoration: none; color: inherit;">dessert</a></button>
    </div>

    <div class="menu-scroll">
        <div class="menu-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '
                    <a href="detail_order.php?id_menu='.$row['id_menu'].'" class="menu-item">
                        <img src="../assets/allmenu/'.$row['gambar_menu'].'" alt="'.$row['nama_menu'].'">
                        <h3>'.$row['nama_menu'].'</h3>
                        <p>'.$row['keterangan'].'</p>
                        <div class="price">'.$row['harga'].'</div>
                    </a>';
                }
            } else {
                echo "<p>Menu tidak ditemukan</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <div class="navbar">
        <a href="home.php">
            <button><img src="../assets/image/icon/Home.png" alt="Home" /><span>Home</span></button>
        </a>
        <a href="scan.php">
            <button><img src="../assets/image/icon/Scan.svg" alt="QR" /><span>Scan</span></button>
        </a>
        <a href="keranjang.php">
            <button><img src="../assets/image/icon/Document.png" alt="Pesan" /><span>Pesan</span></button>
        </a>
        <a href="profile.php">
            <button><img src="../assets/image/icon/Profile.png" alt="Profile" /><span>Profile</span></button>
    </a>
</div>

</div>
</body>
</html>
