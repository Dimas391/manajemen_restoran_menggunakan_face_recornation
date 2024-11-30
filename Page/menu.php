<?php
include "session.php";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Search and category filtering
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$selected_category = isset($_GET['kategori']) ? $_GET['kategori'] : 'All';

// Prepare SQL query with search and category filtering
$sql = "SELECT id_menu, nama_menu, keterangan, harga, gambar_menu, kategori, diskon 
        FROM menu 
        WHERE (nama_menu LIKE ? OR keterangan LIKE ?)";

if ($selected_category !== 'All') {
    $sql .= " AND kategori = ?";
}

$stmt = $conn->prepare($sql);

if ($selected_category !== 'All') {
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("sss", $search_param, $search_param, $selected_category);
} else {
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$result = $stmt->get_result();

$menu_by_category = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $menu_by_category[$row['kategori']][] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            margin: 0;  
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .header {
            background: linear-gradient(50deg, #9333EA 0%, #7C3AED 100%);
            padding: 2rem 1rem;
            border-radius: 0 0 30px 30px;
            margin-bottom: 2rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .menu-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .menu-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .menu-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
        }

        .category-tabs {
            display: flex;
            overflow-x: auto;
            gap: 0.5rem;
            padding: 0.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin-bottom: 1rem;
        }

        .category-tabs::-webkit-scrollbar {
            display: none;
        }

        .category-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            white-space: nowrap;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #ffffff;
            text-decoration: none;
        }

        .category-tab.active {
            background: #9333EA;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
        }

        .category-tab:not(.active) {
            background: rgba(255, 255, 255, 0.1);
        }

        .category-tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #262626;
            padding: 1rem;
            display: flex;
            justify-content: space-around;
            border-top: 1px solid rgba(255, 255,         255, 0.1);
            z-index: 1000;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #9CA3AF;
            font-size: 0.875rem;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .nav-item.active {
            color: #9333EA;
        }

        .nav-icon {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .search-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            margin: 1rem;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
        }

        .search-input {
            background: transparent;
            border: none;
            color: white;
            width: 100%;
            padding: 0.5rem;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-input:focus {
            outline: none;
        }

        .empty-message {
            text-align: center;
            color: #9CA3AF;
            padding: 2rem;
            grid-column: 1 / -1;
        }

        .original-price {
            text-decoration: line-through;
            color: #9CA3AF;
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }

        .discount-badge {
            background-color: #FF6B6B;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .discounted-price {
            color: #9333EA;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="flex justify-between items-center mb-4">
            <a href="home.php" class="text-white">
                <i class="bi bi-arrow-left text-2xl"></i>
            </a>
            <h1 class="text-2xl font-bold">Our Menu</h1>
            <div class="w-8"></div>
        </div>
        
        <div class="search-bar">
            <form method="GET" action="menu.php" class="flex items-center w-full">
                <i class="bi bi-search text-gray-400 mr-2"></i>
                <input type="text" name="search" placeholder="Search menu..." class="search-input" 
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="hidden">Search</button>
            </form>
        </div>
    </div>

    <div class="category-tabs">
        <a href="menu.php" class="category-tab <?php echo !isset($_GET['kategori']) ? 'active' : ''; ?>">
            All Menu
        </a>
        <?php
        $categories = ['Chicken', 'Beef', 'Vegetarian', 'Drink', 'Dessert'];
        foreach ($categories as $category) {
            $isActive = isset($_GET['kategori']) && $_GET['kategori'] === $category;
            echo '<a href="menu.php?kategori='.$category.'" class="category-tab '.($isActive ? 'active' : '').'">'.$category.'</a>';
        }
        ?>
    </div>

    <div class="menu-grid">
        <?php
        function renderMenuItem($menu) {
            $originalPrice = $menu['harga'] ;
            $hasDiscount = $menu['diskon'] > 0;
            $discountedPrice = $hasDiscount 
                ? $originalPrice * (1 - ($menu['diskon'] / 100)) 
                : $originalPrice;

            $menuHtml = '
            <a href="detail_order.php?id_menu='.$menu['id_menu'].'" class="menu-item relative">
                <img src="../assets/allmenu/'.$menu['gambar_menu'].'" alt="'.$menu['nama_menu'].'">';
            
            if ($hasDiscount) {
                $menuHtml .= '
                    <div class="discount-badge">
                        -'.$menu['diskon'].'%
                    </div>';
            }
            
            $menuHtml .= '
                <h3 class="font-semibold text-white mb-1">'.$menu['nama_menu'].'</h3>
                <p class="text-gray-400 text-sm mb-2">'.$menu['keterangan'].'</p>
                <div class="price-container">';
            
            if ($hasDiscount) {
                $menuHtml .= '
                    <span class="original-price"> Rp'.number_format($originalPrice, 0, ',', '.').'
                    </span>
                    <span class="discounted-price"><br>
                        Rp '.number_format($discountedPrice, 0, ',', '.').'
                    </span>';
            } else {
                $menuHtml .= '
                    <span class="text-purple-400 font-bold">
                        Rp '.number_format($originalPrice, 0, ',', '.').'
                    </span>';
            }
            
            $menuHtml .= '
                </div>
            </a>';

            return $menuHtml;
        }

        // Render menu items
        if (empty($menu_by_category)) {
            echo '<div class="empty-message">
                <i class="bi bi-inbox text-4xl mb-2 block"></i>
                <p>Tidak ada menu yang tersedia.</p>
            </div>';
        } else {
            foreach ($menu_by_category as $category => $menus) {
                foreach ($menus as $menu) {
                    echo renderMenuItem($menu);
                }
            }
        }
        ?>
    </div>

    <!-- Bottom Navigation -->
    <nav class="nav-bottom">
        <a href="home.php" class="nav-item">
            <i class="nav-icon bi bi-house"></i>
            Home
        </a>
        <a href="scan.php" class="nav-item">
            <i class="nav-icon bi bi-qr-code"></i>
            Scan
        </a>
        <a href="keranjang.php" class="nav-item">
            <i class="nav-icon bi bi-cart"></i>
            Keranjang
        </a>
        <a href="profile.php" class="nav-item">
            <i class="nav-icon bi bi-person"></i>
            Profile
        </a>
    </nav>
</body>
</html>