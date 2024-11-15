<?php
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

// Query untuk mengambil data detail menu berdasarkan id_menu
$sql = "SELECT nama_menu, keterangan, harga, gambar_menu FROM menu WHERE id_menu = $id_menu";
$result = $conn->query($sql);

// Memeriksa apakah data ditemukan
if ($result->num_rows > 0) {
    $menu = $result->fetch_assoc();
} else {
    echo "Menu tidak ditemukan.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $menu['nama_menu']; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        /* Style untuk detail_order */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 10px;
        }
        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 20px;
            color: #555;
        }
        .menu-image {
            width: 100%;
            border-radius: 20px 20px 0 0;
        }
        .product-title {
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }
        .product-price {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        .description {
            text-align: left;
            font-size: 12px;
            color: #777;
            margin: 15px 0;
        }
        .ingredients {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }
        .ingredient {
            text-align: center;
            font-size: 12px;
        }
        .ingredient img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }
        .quantity-control button {
            background-color: #a64af7;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
        }
        .add-to-cart {
            background-color: #a64af7;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }
        .add-to-cart:hover {
            background-color: #8b39d6;
        }

        .ingredients {
        display: flex;
        justify-content: space-around;
        gap: 10px; /* Jarak antar elemen ingredient */
        flex-wrap: wrap; /* Membungkus elemen agar tidak melampaui batas */
        margin-top: 10px;
    }

    .ingredient {
        text-align: center;
        font-size: 12px;
        flex-basis: 20%; /* Lebar tiap elemen agar lebih rapi */
        max-width: 60px; /* Membatasi lebar maksimal elemen */
    }

    .ingredient img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    </style>
</head>
<body>
    <div class="container">
        <a class="back-button" href="menu.php">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img src="../assets/allmenu/<?php echo $menu['gambar_menu']; ?>" alt="<?php echo $menu['nama_menu']; ?>" class="menu-image" />
        <div class="product-title"><?php echo $menu['nama_menu']; ?></div>
        <div class="product-price">Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?></div>
        <div class="description"><?php echo $menu['keterangan']; ?></div>

        <!-- Section Ingredients -->
        <div class="ingredients">
            <div class="ingredient">
                <img src="https://storage.googleapis.com/a1aa/image/d0AyAzRSUCIrOF5nAMvWSNOWgiPMhkOJSzb5ZYATn3UFY07E.jpg" alt="Olive Oil">
                <span>Olive Oil</span>
            </div>
            <div class="ingredient">
                <img src="https://storage.googleapis.com/a1aa/image/xn8WH7kOSBKnHVI7Xkdhz94iCEqPi6fKlUKzPXNhMrlJwo3JA.jpg" alt="Egg">
                <span>Egg</span>
            </div>
            <div class="ingredient">
                <img src="https://storage.googleapis.com/a1aa/image/SkGS6Z3nJrooHt7FQbVO8K85Im9cGSN92UWNp6EEgMhGY07E.jpg" alt="Tomato">
                <span>Tomato</span>
            </div>
            <div class="ingredient">
                <img src="https://www.buildrestfoods.com/wp-content/uploads/2020/08/lettuce.jpg" alt="Lettuce">
                <span>Lettuce</span>
            </div>
        </div>

        <div class="description">
        <?php echo $menu['keterangan']; ?>
        <br><br>
        Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero's De Finibus Bonorum et Malorum for use in a type specimen book. It usually begins with “Lorem ipsum dolor sit amet...”.
    </div>


        <!-- Quantity Control -->
        <div class="quantity-control">
            <button onclick="decreaseQuantity()">-</button>
            <span id="quantity">1</span>
            <button onclick="increaseQuantity()">+</button>
        </div>

        <button class="add-to-cart" onclick="addToCart()">Tambahkan ke Keranjang</button>
    </div>

    <script>
        let quantity = 1;

        function increaseQuantity() {
            quantity++;
            document.getElementById('quantity').innerText = quantity;
        }

        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                document.getElementById('quantity').innerText = quantity;
            }
        }

        function addToCart() {
            alert('Menambahkan ' + quantity + ' item "' + "<?php echo $menu['nama_menu']; ?>" + '" ke keranjang.');
        }
    </script>
</body>
</html>
