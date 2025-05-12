<?php
require_once 'Database.php';
require_once 'Phone.php';

$database = new Database();
$db = $database->getConnection();
$phone = new Phone($db);

// Fetch all products
$stmt = $phone->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="list_product.php" class="navbar-brand">
                    <img src="assets\image\logo.png" alt="Logo" class="navbar-logo">
                    <span>AiPhone Manager</span>
                </a>
                <div class="navbar-links">
                    <a href="list_product.php" class="nav-link active">Daftar Produk</a>
                    <a href="list_order.php" class="nav-link">Daftar Pesanan</a>
                    <a href="#" class="nav-link logout-link" onclick="showLogoutConfirm()">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Logout</h3>
            <p>Apakah anda yakin ingin keluar?</p>
            <div class="modal-buttons">
                <button class="modal-btn confirm-btn" onclick="confirmLogout()">Ya</button>
                <button class="modal-btn cancel-btn" onclick="hideLogoutConfirm()">Tidak</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Produk</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Penyimpanan</th>
                    <th>Spesifikasi</th>
                    <th>Kelola</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr>
                        <td>
                            <?php
                            $imagePath = $row['ImageURL'];
                            $filePath = __DIR__ . '/' . $imagePath;
                            if (!empty($imagePath) && file_exists($filePath)) {
                                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Product Image" width="50">';
                            } else {
                                echo 'No image available';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td>Rp <?php echo number_format($row['Price'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['Storage']); ?> GB</td>
                        <td><?php echo htmlspecialchars($row['Specification']); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $row['aiphone_id']; ?>" class="edit-btn">✏️</a>
                            <a href="delete.php?id=<?php echo $row['aiphone_id']; ?>" class="delete-btn" onclick="return confirm('Apakah anda yakin untuk menghapus produk ini?')">🗑️</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="button-container">
        <a href="create.php"><button class="create-btn">Tambahkan Produk</button></a>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Kelompok 8 - XI SIJA 1. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Ensure modal is hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, hiding logout modal');
            document.getElementById('logoutConfirmModal').style.display = 'none';
        });

        function showLogoutConfirm() {
            console.log('Showing logout confirmation modal');
            document.getElementById('logoutConfirmModal').style.display = 'flex';
        }

        function hideLogoutConfirm() {
            console.log('Hiding logout confirmation modal');
            document.getElementById('logoutConfirmModal').style.display = 'none';
        }

        function confirmLogout() {
            console.log('Confirming logout, redirecting to logout.php');
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>