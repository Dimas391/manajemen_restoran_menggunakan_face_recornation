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
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            error_log("Connection error: " . $e->getMessage());
            throw $e;
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
        try {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->table . " WHERE id_pelanggan = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return null;
            }
        } catch (Exception $e) {
            error_log("Error getting user: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateUser($id, $data) {
        try {
            $stmt = $this->conn->prepare("UPDATE " . $this->table . " 
                SET nama_pelanggan = ?, 
                    email = ?, 
                    image_face = ? 
                WHERE id_pelanggan = ?");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("sssi", 
                $data['nama_pelanggan'], 
                $data['email'], 
                $data['image_face'], 
                $id
            );

            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error updating user: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfile($id, $nama_pelanggan, $email, $image = null) {
        try {
            $sql = "UPDATE " . $this->table . " SET nama_pelanggan = ?, email = ?";
            $params = [$nama_pelanggan, $email];
            $types = "ss";

            if ($image !== null) {
                $sql .= ", image_face = ?";
                $params[] = $image;
                $types .= "s";
            }

            $sql .= " WHERE id_pelanggan = ?";
            $params[] = $id;
            $types .= "i";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param($types, ...$params);
            
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error updating profile: " . $e->getMessage());
            throw $e;
        }
    }
}


function checkAuth() {
    if (!isset($_SESSION['id_pelanggan'])) {
        header("Location: login.php");
        exit();
    }
}

// profile.php
// require_once 'config/database.php';
// require_once 'models/User.php';
require_once 'session.php';

// Check authentication
checkAuth();

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    // Get user data
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $userData = $user->getUserById($id_pelanggan);

    if (!$userData) {
        throw new Exception("User not found");
    }

    // Handle logout
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        // Perform any cleanup needed before logout
        session_destroy();
        header("Location: login.php");
        exit();
    }

} catch (Exception $e) {
    error_log("Error in profile.php: " . $e->getMessage());
    // Redirect to error page or show error message
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header("Location: error.php");
    exit();
}

// settings.php
// require_once 'config/database.php';
// require_once 'models/User.php';
require_once 'session.php';

checkAuth();

try {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_pelanggan = $_SESSION['id_pelanggan'];
        $nama_pelanggan = $_POST['nama_pelanggan'] ?? '';
        $email = $_POST['email'] ?? '';
        
        // Handle image upload if provided
        $image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $image = file_get_contents($_FILES['profile_image']['tmp_name']);
        }

        $result = $user->updateProfile($id_pelanggan, $nama_pelanggan, $email, $image);
        
        if ($result) {
            $_SESSION['success'] = "Profile updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update profile";
        }
        
        header("Location: profile.php");
        exit();
    }

} catch (Exception $e) {
    error_log("Error in settings.php: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while updating your profile";
    header("Location: profile.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Restaurant App</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .profile-container {
            max-width: 768px;
            margin: 0 auto;
            padding: 20px;
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

        .status-badge {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            background-color: #10B981;
            border-radius: 50%;
            border: 3px solid #1a1a1a;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .menu-card:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.15);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
            color: #ffffff;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.1);
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

        @media (max-width: 640px) {
            .profile-container {
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
    <div class="profile-container pb-24">
        <!-- Back Button -->
        <button onclick="window.history.back()" class="mb-4 flex items-center text-gray-400 hover:text-white">
            <i class="bi bi-arrow-left text-xl mr-2"></i>
            <span>Back</span>
        </button>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="avatar-container">
                <?php if (!empty($userData['image_face'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($userData['image_face']); ?>" alt="Profile" class="avatar">
                <?php else: ?>
                    <img src="../assets/image/icon/icon_avatar.png" alt="Profile" class="avatar">
                <?php endif; ?>
                <div class="status-badge"></div>
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($userData['nama_pelanggan']); ?></h1>
                <p class="text-gray-200"><?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
        </div>

        <!-- Menu Section -->
        <div class="menu-card">
            <h2 class="text-lg font-semibold mb-4 text-purple-300">Account Settings</h2>
            <a href="settigs.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-person text-purple-400"></i>
                </div>
                <span class="flex-grow">Edit Profile</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
        </div>

        <div class="menu-card">
            <h2 class="text-lg font-semibold mb-4 text-purple-300">Preferences</h2>
            <a href="language.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-translate text-purple-400"></i>
                </div>
                <span class="flex-grow">Language</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
            <a href="notifications.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-bell text-purple-400"></i>
                </div>
                <span class="flex-grow">Notifications</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
        </div>

        <div class="menu-card">
            <h2 class="text-lg font-semibold mb-4 text-purple-300">About</h2>
            <a href="feedback.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-chat-dots text-purple-400"></i>
                </div>
                <span class="flex-grow">Send Feedback</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
            <a href="rate-us.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-star text-purple-400"></i>
                </div>
                <span class="flex-grow">Rate Us</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
            <a href="new-version.php" class="menu-item">
                <div class="menu-icon">
                    <i class="bi bi-download text-purple-400"></i>
                </div>
                <span class="flex-grow">Check for Updates</span>
                <i class="bi bi-chevron-right text-gray-400"></i>
            </a>
        </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && href !== '#') {
                        e.preventDefault();
                        const animation = this.animate([
                            { transform: 'scale(1)' },
                            { transform: 'scale(0.95)' },
                            { transform: 'scale(1)' }
                        ], {
                            duration: 300
                        });
                        
                        animation.onfinish = () => {
                            window.location.href = href;
                        };
                    }
                });
            });
        });
    </script>
</body>
</html>