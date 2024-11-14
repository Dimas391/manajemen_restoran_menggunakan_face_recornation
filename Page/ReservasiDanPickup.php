<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation and Pick UP</title>
    <link rel="stylesheet" href="../assets/style/ReservasiDanPickup.css">
</head>
<body>
    <div class="container">
        <header>
            <a href="#" class="back-btn">←</a>
            <div class="title">
                <a href="?page=reservasi" class="tab <?= (!isset($_GET['page']) || $_GET['page'] == 'reservasi') ? 'active' : '' ?>">Reservasi</a>
                <a href="?page=pickup" class="tab <?= (isset($_GET['page']) && $_GET['page'] == 'pickup') ? 'active' : '' ?>">Pick UP</a>
            </div>
        </header>

        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'reservasi';
        if ($page == 'reservasi') {
        ?>
            <!-- Form Reservasi -->
            <form action="menu.php" method="POST" class="reservation-form">
                <div class="form-group">
                    <label>Your Information</label>
                    <input type="text" name="full_name" placeholder="Full name" required>
                    <input type="tel" name="phone_number" placeholder="Phone number" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" name="time" required>
                </div>
                
                <div class="form-group">
                    <label for="party_size">Party Size</label>
                    <input type="number" name="party_size" min="1" max="10" value="1" required>
                </div>
                
                <div class="form-group">
                    <label>Table</label>
                    <div class="table-options">
                        <label><input type="radio" name="table" value="Inside" checked> Inside</label>
                        <label><input type="radio" name="table" value="Outside"> Outside</label>
                    </div>
                </div>
                
                <button type="submit" class="next-btn">Next</button>
            </form>
        <?php
        } else {
        ?>
            <!-- Form Pick UP -->
            <form action="menu.php" method="POST" class="pickup-form">
                <div class="form-group">
                    <label>Your Information</label>
                    <input type="text" name="full_name" placeholder="Full name" required>
                    <input type="tel" name="phone_number" placeholder="Phone number" required>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                
                <button type="submit" class="next-btn">Next</button>
            </form>
        <?php
        }
        ?>
    </div>
</body>
</html>
