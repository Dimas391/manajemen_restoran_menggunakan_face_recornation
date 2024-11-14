<?php
if (isset($_GET['username'])) {
    $username = htmlspecialchars($_GET['username']);
} else {
    $username = 'Guest';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Siantar Top</title>
    <link rel="stylesheet" href="..\assets\style\style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Menambahkan Bootstrap Icons -->
    <style>
        /* Resetting some defaults */
        /* Resetting some defaults */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #111;
    color: #fff;
}

/* Header styling */
header {
    background-color: #222;
    padding: 20px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.header-content {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: space-between;
}

header .logo h1 {
    color: #f4f;
    font-size: 24px;
}

header .logo p {
    color: #aaa;
    font-size: 14px;
    margin-top: 5px;
}

.logo {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.location {
    color: white;
    font-size: 14px;
    padding: 20px 50px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
}

.location-icon {
    width: 44px;
    height: 44px;
}

.location-details p {
    margin: 0;
}

.sub-location p {
    margin-top: 25px;
    margin-bottom: 0;
}

.search-bar {
    display: flex;
    align-items: center;
    margin-top: 10px;
    padding: 0 50px;
    width: 100%;
}

.search-input {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    outline: none;
    border-radius: 30px 0 0 30px;
    width: 250px;
}

.search-button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    background-color: #f4f;
    color: #111;
    cursor: pointer;
    font-weight: bold;
    border-radius: 0 30px 30px 0;
}

.search-button:hover {
    background-color: #e3e;
}

header .contact button {
    background-color: #f4f;
    border: none;
    padding: 8px 16px;
    color: #111;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
}

/* Hero Section */
.hero {
    text-align: center;
    padding: 50px;
    display: block;
    margin-right: 830px;
}

.hero h2 {
    font-size: 36px;
    color: #f4f;
    margin-bottom: 10px;
}

.hero p {
    font-size: 18px;
    margin-bottom: 10px;
}

.hero button {
    background-color: #f4f;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    cursor: pointer;
    color: #111;
    border-radius: 5px;
    font-size: 16px;
}

/* Favourite Food Section */
.favourite-food {
    padding: 40px 20px;
    text-align: center;
    margin-left: 400px;
    margin-top: -320px;
}

.food-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    justify-content: center;
    margin-top: -110px;
}

.food-gallery .image2 {
    margin-top: -100px;
}

.food-item {
    text-align: center;
    max-width: 150px;
}

.food-item img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.food-item p {
    margin-top: 10px;
}

/* What We Serve Section */
.what-we-serve {
    padding: 40px 20px;
    background-color: #333;
    text-align: center;
}

.serve-items {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    justify-content: center;
    padding-top: 20px;
}

.serve-item {
    text-align: center;
    min-width: 300px;
    max-width: 350px;
    padding: 20px;
    background-color: #444;
    border-radius: 8px;
}

.serve-item img {
    width: 290px;
    height: 290px;
    margin-bottom: 10px;
    border-radius: 8px;
}

.serve-item h4 {
    margin-top: 10px;
    color: #f4f;
}

/* Map Section */
.map {
    padding: 20px;
    text-align: center;
    background-color: #222;
    margin-top: 40px;
}

.map iframe {
    width: 100%;
    max-width: 1500px;
    height: 450px;
    border-radius: 8px;
    margin-top: 20px;
}

.map-info {
    margin-top: 20px;
    font-size: 14px;
    color: #fff;
    text-align: center;
}

.map-info p {
    margin: 0;
    font-size: 16px;
    color: #aaa;
}

/* Footer */
footer {
    background-color: #222;
    text-align: center;
    padding: 20px;
}

.footer-content a {
    color: #f4f;
    margin: 0 5px;
    text-decoration: none;
}

.footer-content a:hover {
    text-decoration: underline;
}

.footer-content i {
    font-size: 30px;
    margin: 0 15px;
    cursor: pointer;
    color: #f4f;
}

.footer-content i:hover {
    color: #aaa;
}

/* Media Queries for responsiveness */
@media (max-width: 1200px) {
    .hero {
        margin-right: 0;
        padding: 20px;
    }

    .food-gallery .image1 {
        width: 80%;
    }

    .food-gallery .image2 {
        width: 70%;
    }

    header .logo h1 {
        font-size: 20px;
    }

    header .logo p {
        font-size: 12px;
    }

    .search-input {
        width: 200px;
    }

    .search-bar {
        padding: 0 20px;
    }
}

@media (max-width: 992px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .location {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px;
    }

    .search-bar {
        width: 100%;
    }

    .hero h2 {
        font-size: 28px;
    }

    .hero p {
        font-size: 16px;
    }

    .favourite-food {
        margin-left: 0;
        margin-top: 20px;
    }

    .food-gallery {
        flex-direction: column;
        margin-top: -60px;
    }

    .food-item img {
        width: 80%;
    }

    .map iframe {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .hero h2 {
        font-size: 24px;
    }

    .hero p {
        font-size: 14px;
    }

    .hero button {
        font-size: 14px;
    }

    .food-gallery {
        flex-direction: column;
        gap: 20px;
    }

    .serve-items {
        flex-direction: column;
    }

    .serve-item {
        min-width: 80%;
        max-width: 100%;
    }

    .map iframe {
        height: 250px;
    }

    .footer-content i {
        font-size: 24px;
    }
}

@media (max-width: 576px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }

    .location {
        flex-direction: column;
        align-items: center;
    }

    .hero {
        padding: 20px;
        margin-right: 0;
    }

    .food-gallery .image1, .food-gallery .image2 {
        width: 100%;
    }

    .serve-items {
        flex-direction: column;
        align-items: center;
    }

    .serve-item {
        width: 90%;
        margin: 10px 0;
    }

    .map iframe {
        height: 200px;
    }

    .footer-content i {
        font-size: 20px;
    }
}
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">
                <img src="..\assets\image\logo.png" alt="">
            </div>
            <div class="contact">
                <button><?php echo $username; ?></button>
            </div>
        </div>
    </header>

    <!-- Location Section -->
    <div class="location">
        <img src="..\assets\image\location.png" alt="Location Icon" class="location-icon">
        <div>
            <p>Lokasi</p>
            <p>Indonesia, Aceh</p>
            <p>Lhokseumawe, Bukit Rata</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar">
        <form action="/search" method="get">
            <input type="text" name="query" placeholder="Search..." class="search-input">
            <button type="submit" class="search-button">Search</button>
        </form>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <h2>Welcome</h2>
        <p>Enjoy Our Delicious Food</p>
        <p>Best Food made by our Passionate Chefs</p>
        <button class="tab active">
        <a href="menu.php" style="text-decoration: none; color: inherit;">Menu</a>
        </button>
        <button>
        <a href="ReservasiDanPickup.php" style="text-decoration: none; color: inherit;">Pesan</button>
    </section>

    <!-- Favourite Food Section -->
    <section class="favourite-food">
        <div class="food-gallery">
            <img class="image1" src="..\assets\image\Group 154.png" alt="" width="920px">
            <img class="image2" src="..\assets\image\makanan.png" alt="" width="320px">
        </div>
    </section>

    <!-- What We Serve Section -->
    <section class="what-we-serve">
        <h3>Your Favourite Food</h3>
        <div class="serve-items">
            <div class="serve-item">
                <img src="..\assets\image\iklan.png" alt="Mudah Digunakan">
                <h4>Mudah Digunakan</h4>
                <p>Anda hanya perlu menyentuh beberapa langkah untuk melakukan pemesanan</p>
            </div>
            <div class="serve-item">
                <img src="..\assets\image\iklan2.png" alt="Kualitas Terbaik">
                <h4>Kualitas Terbaik</h4>
                <p>Tidak hanya cepat tapi kami kualitas juga nomor satu</p>
            </div>
            <div class="serve-item">
                <img src="..\assets\image\iklan.png" alt="Mudah Digunakan">
                <h4>Mudah Digunakan</h4>
                <p>Anda hanya perlu menyentuh beberapa langkah untuk melakukan pemesanan</p>
            </div>
        </div>
    </section>


    <!-- Map Section -->
    <section class="map">
        <iframe src="https://maps.google.com/maps?q=Lhokseumawe,%20Bukit%20Rata&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
        <div class="map-info">
            <p>Lokasi Kami: Lhokseumawe, Bukit Rata</p>
            <p>Tel: +62 81234567890</p>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <a href="https://www.facebook.com" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://www.instagram.com" target="_blank"><i class="bi bi-instagram"></i></a>
            <a href="https://twitter.com" target="_blank"><i class="bi bi-twitter"></i></a>
        </div>
        <p>&copy; 2024 Restoran Siantar Top</p>
    </footer>
</body>
</html>
