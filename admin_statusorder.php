<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Reset dan Tata Letak Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 180px;
            background-color: #f4f4f4;
            height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0);
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
            display: flex;
            align-items: center;
        }

        .sidebar nav ul li a:hover {
            color: #a64ac9;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0);
            border-radius: 8px;
            margin: 20px;
        }

        .main-content h1 {
            font-size: 24px;
            color: #a64ac9;
            margin-bottom: 20px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        table td:last-child {
            text-align: center;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            color: #fff;
            cursor: pointer;
        }

        .status.pending {
            background-color: #ffb700;
        }

        .status.process {
            background-color: #007bff;
        }

        .status.completed {
            background-color: #28a745;
        }

        .status:hover {
            opacity: 0.8;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .modal-content button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-pending {
            background-color: #ffb700;
            color: #fff;
        }

        .btn-process {
            background-color: #007bff;
            color: #fff;
        }

        .btn-completed {
            background-color: #28a745;
            color: #fff;
        }

        .btn-close {
            background-color: #ccc;
            color: #333;
        }

        .btn-close:hover {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="./Image/logo.png" alt="Logo" class="logo-img">
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
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Admin</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Created</th>
                    <th>Account Type</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Dimas</td>
                    <td>Aug 18 2021<br>15:20:56</td>
                    <td>User</td>
                    <td><span class="status pending" onclick="openModal(this)">Pending</span></td>
                    <td>➔</td>
                </tr>
                <tr>
                    <td>Alifrah</td>
                    <td>Aug 1 2021<br>19:50:01</td>
                    <td>User</td>
                    <td><span class="status process" onclick="openModal(this)">Process</span></td>
                    <td>➔</td>
                </tr>
                <tr>
                    <td>Rafli</td>
                    <td>Jul 22 2021<br>07:07:07</td>
                    <td>User</td>
                    <td><span class="status completed" onclick="openModal(this)">Completed</span></td>
                    <td>➔</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3>Ubah Status</h3>
            <button class="btn-pending" onclick="updateStatus('pending')">Pending</button>
            <button class="btn-process" onclick="updateStatus('process')">Process</button>
            <button class="btn-completed" onclick="updateStatus('completed')">Completed</button>
            <button class="btn-close" onclick="closeModal()">Batal</button>
        </div>
    </div>

    <script>
        let currentStatusElement = null;

        function openModal(element) {
            // Simpan elemen status yang diklik
            currentStatusElement = element;
            // Tampilkan modal
            document.getElementById('statusModal').style.display = 'flex';
        }

        function closeModal() {
            // Sembunyikan modal
            document.getElementById('statusModal').style.display = 'none';
        }

        function updateStatus(newStatus) {
            if (currentStatusElement) {
                // Ubah kelas status dan teks elemen
                currentStatusElement.className = `status ${newStatus}`;
                currentStatusElement.textContent = capitalize(newStatus);
            }
            // Sembunyikan modal
            closeModal();
        }

        function capitalize(word) {
            return word.charAt(0).toUpperCase() + word.slice(1);
        }
    </script>
</body>
</html>
