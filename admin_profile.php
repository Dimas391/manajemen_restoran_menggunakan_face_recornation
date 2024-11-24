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
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
}

.container {
    display: flex;
    height: 100vh;
}

.sidebar {
    width: 180px;
    background-color: #f4f4f4;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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

        
    </style>
</head>
<body>
    <?php
    // Data profil (bisa diganti dengan data dari database)
    $profile = [
        'full_name' => 'Dimas',
        'phone_number' => '0906006699',
        'email' => 'dimas@gmail.com',
        'account_type' => 'User',
        'created' => 'August 18, 2021 - 15:20:56'
    ];

    // Data pesanan (bisa diganti dengan data dari database)
    $orders = [
        [
            'id' => 1,
            'type' => 'Pick Up',
            'items' => [
                'Beef Burger' => '1 Pcs',
                'Chicken Wings' => '2 Pcs'
            ],
            'status' => 'PENDING'
        ],
        [
            'id' => 2,
            'type' => 'Reservasi',
            'items' => [
                'Beef Burger' => '5 Pcs',
                'Chicken Wings' => '4 Pcs'
            ],
            'details' => 'Meja No 12, 2 Orang',
            'status' => 'COMPLETED'
        ]
    ];
    ?>
    <div class="container">
        <aside class="sidebar">
        <div class="logo-container">
    <img src="../assets/image/logo.png" alt="Restoran Logo" class="logo-img">
    <div class="logo-text">
        <span class="main-text">Restoran</span><br>
        <span class="sub-text">DriveThru</span>
    </div>
</div>



            <nav>   
            <ul>
    <li><a href="#"><i class="bi bi-house-door"></i> Dashboard</a></li>
    <li><a href="#"><i class="bi bi-list-ul"></i> Menu</a></li>
    <li><a href="#"><i class="bi bi-calendar-check"></i> Reservasi</a></li>
    <li><a href="#"><i class="bi bi-box-arrow-right"></i> Log Out</a></li>
</ul>

            </nav>
        </aside>
        <main class="content">
            <h1 class="title">Admin
            </h1>
            <h2  <i class="bi bi-arrow-left-circle"></i>  </h2>
            <section class="profile">
                <div class="profile-pic">
                    <img src="../assets/image/icon/icon_avatar.png" alt="Profile Picture">
                </div>
                <div class="profile-info">
                <p>
    <strong>Full Name:</strong>
    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?= $profile['full_name'] ?>" readonly>
</p>
<p>
    <strong>Phone Number:</strong>
    <input type="text" class="form-control" id="exampleFormControlInput2" value="<?= $profile['phone_number'] ?>" readonly>
</p>
<p>
    <strong>Email:</strong>
    <input type="text" class="form-control" id="exampleFormControlInput3" value="<?= $profile['email'] ?>" readonly>
</p>
<p>
    <strong>Account Type:</strong>
    <input type="text" class="form-control" id="exampleFormControlInput4" value="<?= $profile['account_type'] ?>" readonly>
</p>
<p>
    <strong>Created:</strong>
    <input type="text" class="form-control" id="exampleFormControlInput5" value="<?= $profile['created'] ?>" readonly>
</p>

                </div>
            </section>
            <section class="orders">
                <?php foreach ($orders as $order): ?>
                    <div class="order">
                        <h3>ID: <?= $order['id'] ?></h3>
                        <p><strong><?= $order['type'] ?></strong></p>
                        <?php foreach ($order['items'] as $item => $quantity): ?>
                            <p><?= $item ?>: <?= $quantity ?></p>
                        <?php endforeach; ?>
                        <?php if (isset($order['details'])): ?>
                            <p><?= $order['details'] ?></p>
                        <?php endif; ?>
                        <span class="status <?= strtolower($order['status']) ?>"><?= $order['status'] ?></span>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>
</body>
</html>