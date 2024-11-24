<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "restoran";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// models/User.php
class User {
    private $conn;
    private $table = "pelanggan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_pelanggan = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUser($id, $data) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table . " SET nama_pelanggan = ?, email = ?, image_face = ? WHERE id_pelanggan = ?");
        $stmt->bind_param("sssi", $data['nama_pelanggan'], $data['email'], $data['image_face'], $id);
        return $stmt->execute();
    }
}

// session.php
session_start();
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login.php");
    exit();
}

// profile.php
// require_once 'config/database.php';
// require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$id_pelanggan = $_SESSION['id_pelanggan'];
$userData = $user->getUserById($id_pelanggan);

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .settings-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 80px);
        }

        .profile-header {
            background: linear-gradient(135deg, #9333EA 0%, #7C3AED 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }

        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        .avatar-upload {
            position: absolute;
            bottom: 5px;
            right: 0;
            background: #9333EA;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .avatar-upload:hover {
            transform: scale(1.1);
            background: #7C3AED;
        }

        .input-group {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }

        .input-field {
            position: relative;
            margin-bottom: 15px;
        }

        .input-field:last-child {
            margin-bottom: 0;
        }

        .input-field input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            border-color: #9333EA;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
            outline: none;
        }

        .input-field input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .input-field i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .input-field i:hover {
            color: #9333EA;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1a1a1a;
            padding: 15px;
            box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-around;
            z-index: 1000;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #6B7280;
            transition: color 0.3s ease;
        }

        .nav-item.active {
            color: #9333EA;
        }

        .nav-icon {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .logout-button {
            background-color: rgba(220, 38, 38, 0.2);
            color: #ef4444;
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 30px;
        }

        .logout-button:hover {
            background-color: rgba(220, 38, 38, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .settings-container {
                padding: 15px;
            }

            .profile-header {
                border-radius: 15px;
                padding: 20px;
            }

            .avatar-container {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="settings-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
        </button>

        <!-- Profile Header -->
        <div class="profile-header">
            <form id="imageForm" enctype="multipart/form-data">
                <div class="avatar-container">
                    <?php if (!empty($userData['image_face'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_face']); ?>" alt="Profile" class="avatar" id="preview-image">
                    <?php else: ?>
                        <img src="../assets/image/icon/icon_avatar.png" alt="Profile" class="avatar" id="preview-image">
                    <?php endif; ?>
                    <label for="profile_image" class="avatar-upload">
                        <i class="bi bi-pencil-fill text-white text-sm"></i>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                </div>
            </form>
        </div>

        <!-- Settings Form -->
        <form method="POST" class="input-group">
            <div class="input-field">
                <input type="text" name="name" placeholder="Full Name" 
                    value="<?php echo htmlspecialchars($userData['nama_pelanggan']); ?>">
                <i class="bi bi-pencil"></i>
            </div>
            <div class="input-field">
            <input type="text" name="name" placeholder="Full Name" 
                value="<?php echo htmlspecialchars($userData['nama_pelanggan']); ?>" disabled>
            <i class="bi bi-pencil" data-field="name" onclick="editField(this)"></i>
            </div>
            <div class="input-field">
                <input type="email" name="email" placeholder="Email" 
                    value="<?php echo htmlspecialchars($userData['email']); ?>" disabled>
                <i class="bi bi-pencil" data-field="email" onclick="editField(this)"></i>
            </div>

            <button type="submit" name="update_profile" class="w-full mt-4 bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </form>

        <!-- Logout Button -->
        <form method="POST" action="">
            <button type="submit" name="logout" class="logout-button">
                <i class="bi bi-box-arrow-right mr-2"></i>
                Logout
            </button>
        </form>
    </div>

    <!-- Bottom Navigation -->
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
            <a href="profile.php" class="text-white flex flex-col items-center">
                <i class="bi bi-person"></i>
                <span class="text-sm">Profile</span>
            </a>
        </div>
    </nav>

    <script>
    async function updateProfile(field, value) {
        const response = await fetch("update_profile.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ field, value }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Profile berhasil diperbarui!");
        } else {
            alert("Gagal memperbarui profile: " + (result.message || "Unknown error"));
        }
    }

    function editField(element) {
        const field = element.getAttribute("data-field");
        const input = element.previousElementSibling;
        input.disabled = false;
        input.focus();

        input.addEventListener("blur", () => {
            updateProfile(field, input.value);
            input.disabled = true;
        });
    }
</script>


    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
                
                // Auto submit form when image is selected
                const formData = new FormData(document.getElementById('imageForm'));
                fetch('update_profile_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert('Profile image updated successfully');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        // Add focus effects to input fields
        document.querySelectorAll('.input-field input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i').style.color = '#9333EA';
            });

            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i').style.color = 'rgba(255, 255, 255, 0.5)';
            });
        });
    </script>
</body>
</html>