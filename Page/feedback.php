<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restoran";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

include "session.php"; // Pastikan session.php mengatur id_pelanggan

// Proses pengiriman komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];
    $id_pelanggan = $_SESSION['id_pelanggan']; // Ambil dari session

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO feedback (rating, comment, id_pelanggan) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $rating, $feedback, $id_pelanggan);
    $stmt->execute();
    $stmt->close();
}

// Ambil semua komentar dari database
$result = $conn->query("SELECT rating, comment FROM feedback ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Restoran Siantar Top</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        .feedback-container {
            background-color: #2d2d2d;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }
        .star {
            font-size: 40px;
            color: #FFD700;
            cursor: pointer;
        }
        .star:hover {
            color: #FFC107;
        }
        .submit-button {
            background-color: #9333EA;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
        }
        .comment {
            background-color: #3a3a3a;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="max-w-screen-lg mx-auto p-4 min-h-screen flex flex-col items-center justify-center">
        <div class="feedback-container w-full">
            <h2 class="text-xl font-bold mb-4">Feedback</h2>
            
            <!-- Tampilkan komentar yang sudah ada -->
            <div class="mb-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="comment">
                        <strong>Rating: 
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $row['rating']) {
                                    echo '&#9733;'; // Bintang terisi
                                } else {
                                    echo '&#9734;'; // Bintang kosong
                                }
                            }
                            ?>
                        </strong>
                        <p><?php echo htmlspecialchars($row['comment']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>

            <form action="" method="post">
                <div class="mb-4">
                    <div                    <div class="text-lg font-semibold mb-2">Rating</div>
                    <div class="flex space-x-1" id="rating">
                        <span class="star" data-value="1">&#9734;</span>
                        <span class="star" data-value="2">&#9734;</span>
                        <span class="star" data-value="3">&#9734;</span>
                        <span class="star" data-value="4">&#9734;</span>
                        <span class="star" data-value="5">&#9734;</span>
                    </div>
                    <input type="hidden" name="rating" id="rating-value" value="0">
                </div>

                <div class="mb-4">
                    <textarea class="w-full h-32 p-2 rounded-lg border border-gray-600 bg-gray-800 text-white" name="feedback" placeholder="Jika Anda memiliki masukan kritik dan saran, silakan ketik di sini..."></textarea>
                </div>

                <button type="submit" class="submit-button w-full">Submit</button>
            </form>
        </div>
    </div>

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
        const stars = document.querySelectorAll('.star');
        const ratingValue = document.getElementById('rating-value');

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                ratingValue.value = value; // Set the hidden input value

                // Change star colors based on selected rating
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.innerHTML = '&#9733;'; // Bintang terisi
                    } else {
                        s.innerHTML = '&#9734;'; // Bintang kosong
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close(); // Menutup koneksi database
?>