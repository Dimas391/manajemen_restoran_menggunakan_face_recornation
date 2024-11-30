<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "restoran";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mulai sesi
session_start();

// Periksa sesi id_pelanggan
if (!isset($_SESSION['id_pelanggan'])) {
    die("ID pelanggan tidak ditemukan. Silakan login kembali.");
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Query untuk mengambil data pelanggan
// Query untuk mengambil data pelanggan
$query = "SELECT nama_pelanggan, NoHp, email, alamat, image_face FROM pelanggan WHERE id_pelanggan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
} else {
    die("Data pelanggan tidak ditemukan.");
}

// Validasi data
$nama_pelanggan = $profile['nama_pelanggan'] ?? 'Tidak tersedia';
$no_hp = $profile['NoHp'] ?? 'Tidak tersedia';
$email = $profile['email'] ?? 'Tidak tersedia';
$alamat = $profile['alamat'] ?? 'Tidak tersedia';


// Query untuk mengambil data reservasi dan pickup
$query_orders = "
    (SELECT 
        'Reservasi' AS type, 
        r.id_reservasi AS id, 
        r.status, 
        CONCAT('Meja No ', r.id_meja, ', ', r.party_size, ' Orang') AS details,
        m.nama_menu, 
        m.harga
    FROM 
        reservasi r
    LEFT JOIN 
        menu m ON r.id_reservasi = m.id_menu
    WHERE 
        r.id_pelanggan = ?
    )
    UNION ALL
    (SELECT 
        'Pick Up' AS type, 
        p.id_pickup AS id, 
        'PENDING' AS status, 
        NULL AS details, 
        m.nama_menu, 
        m.harga
    FROM 
        pickup p
    LEFT JOIN 
        menu m ON p.id_pickup = m.id_menu
    WHERE 
        p.id_pelanggan = ?
    )
";
$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->bind_param("ii", $id_pelanggan, $id_pelanggan);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$orders = $result_orders->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* CSS Styling */
body {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
}

.container {
    display: flex;
    height: 100vh;
}

.sidebar {
    width: 250px; /* Lebar sidebar */
    height: 100%; /* Pastikan sidebar menutupi seluruh tinggi layar */
    background: linear-gradient(to bottom, #e5e7eb, #d6d6d6);
    transition: left 0.3s ease;
    z-index: 1000;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar.active {
            left: 0; /* Show sidebar */
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-toggle {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #FF00FF;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li a {
            display: block;
            padding: 20px;
            color: #FF00FF;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #d1d5db;
        }

.content {
    flex-grow: 1; /* Mengisi sisa ruang yang tersedia */
    padding: 30px;
}

.logo-container {
    display: flex;
    align-items: center;
    margin-bottom: 40px;
}

.logo-img {
    width: 50px;
    height: auto;
    margin-right: 10px;
}

.logo-text {
    line-height: 1.2;
}

.main-text {
    font-size: 20px;
    color: #a64ac9;
    font-weight: bold;
}

.sub-text {
    font-size: 14px;
    color: #333;
}

.sidebar nav ul {
    list-style: none;
    padding: 0;
}

.sidebar nav ul li {
    margin: 15px 0;
}

.sidebar nav ul li a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
}

.sidebar nav ul li a:hover {
    color: #a64ac9;
}

.content {
    flex-grow: 1;
    padding: 30px;
}

.title {
    font-size: 24px;
    color: #a64ac9;
    margin-bottom: 20px;
}

.profile {
    display: flex;
    align-items: center; /* Menyusun foto dan form di tengah secara vertikal */
    gap: 20px; /* Memberikan jarak antara foto dan form */
}

.profile-pic {
    flex-shrink: 0;
}

.profile-pic img {
    width: 100px; /* Ukuran gambar avatar */
    height: 100px;
    border-radius: 50%; /* Membuat gambar berbentuk lingkaran */
    border: 2px solid #ddd;
    object-fit: cover; /* Memastikan gambar terpusat dan tidak terdistorsi */
}

.profile-info {
    flex-grow: 1;
}

.profile-info p {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 5px 0;
}

.profile-info p strong {
    min-width: 120px; /* Sesuaikan lebar label */
    text-align: left;
}

.profile-info input {
    flex-grow: 1;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    font-size: 14px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Menambahkan shadow */
}

.orders .order {
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    position: relative; /* Membuat container ini sebagai referensi untuk positioning status */
}

.orders .order h3 {
    margin: 0 0 10px;
    color: #333;
    font-size: 16px;
}

.orders .order .status {
    display: inline;
    padding: 5px 15px;
    border-radius: 3px;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    position: absolute; /* Menempatkan status dalam container */
    top: 20px; /* Atur posisi status agar berada di atas */
    right: 20px; /* Menempatkan status di sebelah kanan */
}

.orders .order .status.pending {
    background-color: #f39c12;
}

.orders .order .status.completed {
    background-color: #27ae60;
}

 /* Base and Mobile-First Styles */
 body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Sidebar Mobile */
        .sidebar {
           
            top: 0;
            left: -250px; /* Hidden by default */
            width: 220px;
            height: 100%;
            background-color: #e5e7eb;
            transition: left 0.3s ease;
            /* z-index: 1000; */
            padding: 20px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active {
            left: 0; /* Show sidebar */
        }

        .sidebar-overlay {
            display: none;
           
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* z-index: 999; */
        }

        .sidebar-toggle {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: #FF00FF;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .content {
            margin-left: 250px; /* Memberikan ruang untuk sidebar */
            padding: 30px;
            flex-grow: 1; 

        }
        .main-content {
            margin-left: 0;
            padding: 15px;
            transition: margin-left 0.3s ease;
        }

        /* Tables */
        .responsive-table {
            overflow-x: auto;
            width: 100%;
        }

        .responsive-table table {
            width: 100%;
            min-width: 500px; /* Ensures horizontal scrolling on small screens */
        }

        .status-button {
            padding: 8px 16px;
            margin-right: 8px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        /* Responsive Buttons */
        .status-button, .action-button {
            width: 100%;
            margin-bottom: 5px;
        }

        /* Tablets and Larger Screens */
        @media (min-width: 768px) {
            .sidebar {
                left: 0; /* Always visible */
            }

            .sidebar-toggle {
                display: none; /* Hide toggle button */
            }

            .main-content {
                margin-left: 250px;
            }

            .status-button, .action-button {
                width: auto;
            }
        }

        /* Status Button Styles */
        .status-pending {
            background-color: gray;
            color: white;
        }

        .status-proses {
            background-color: orange;
            color: white;
        }

        .status-complete {
            background-color: green;
            color: white;
        }

        /* Logo and Navigation Styles */
        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-container img {
            width: 60px;
            height: 60px;
            margin-right: 10px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li a {
            display: block;
            padding: 10px;
            color: #FF00FF;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar nav ul li a:hover {
            background-color: #d1d5db;
        }
        
    </style>
</head>
<body>
    <div class="container">
    <div class="sidebar">
        <div class="logo-container">
            <img src="../assets/Image/logo.png" alt="Logo" class="logo-img">
            <div class="logo-text">
                <span class="main-text text-black font-bold">Restoran</span><br>
                <span class="sub-text text-gray-600">DriveThru</span>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="reservasi_pickup_results.php"><i class="bi bi-cart" style="margin: center;"></i> Pemesanan</a></li>
                <li><a href="admin_menu.php"><i class="bi bi-list-ul"></i> Menu</a></li>
                <li><a href="admin_profile.php"><i class="bi bi-calendar-check"></i> Detail Reservasi</a></li>
                <li><a href="#"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
            </ul>
        </nav>
    </div>

    <main class="content">
        <h1 class="title">Admin</h1>
        <h2><i class="bi bi-arrow-left-circle"></i></h2>
        <section class="profile">
            <div class="profile-pic">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($profile['image_face']); ?>" alt="Profile" class="avatar">
            </div>
            <div class="profile-info">
                <p>
                    <strong>Full Name:</strong>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?= isset($profile['nama_pelanggan']) ? $profile['nama_pelanggan'] : '' ?>" readonly>
                </p>
                <p>
                    <strong>Phone Number:</strong>
                    <input type="text" class="form-control" id="exampleFormControlInput2" value="<?= isset($profile['NoHp']) ? $profile['NoHp'] : '' ?>" readonly>
                </p>
                <p>
                    <strong>Email:</strong>
                    <input type="text" class="form-control" id="exampleFormControlInput3" value="<?= $profile['email'] ?>" readonly>
                </p>
                <p>
                    <strong>Account Type:</strong>
                    <input type="text" class="form-control" id="exampleFormControlInput4" value="User " readonly>
                </p>
                <p>
                    <strong>Created:</strong>
                    <input type="text" class="form-control" id="exampleFormControlInput5" value="August 18, 2021 - 15:20:56" readonly> <!-- Ini hanya contoh -->
                </p>
            </div>
        </section>

        <section class="orders">
            <?php foreach ($orders as $order): ?>
                <div class="order">
                    <h3>Order ID: <?= htmlspecialchars($order['id']) ?></h3>
                    <p><strong><?= htmlspecialchars($order['type']) ?></strong></p>
                    <?php if (!empty($order['details'])): ?>
                        <p><?= htmlspecialchars($order['details']) ?></p>
                    <?php endif; ?>
                    <p>Menu: <?= htmlspecialchars($order['nama_menu'] ?? 'N/A') ?></p>
                    <p>Harga: <?= htmlspecialchars($order['harga'] ?? 'N/A') ?></p>
                    <span class="status <?= strtolower(htmlspecialchars($order['status'])) ?>">
                        <?= htmlspecialchars($order['status']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</div>