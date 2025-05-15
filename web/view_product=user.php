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
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner"></div>
    </div>

    <!-- Success/Error Popup -->
    <div id="successPopup" class="popup" aria-live="polite">
        <div class="popup-content">
            <p id="popupMessage"></p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="view_article.php" class="navbar-brand">
                    <img src="assets/image/logo.png" alt="Logo" class="navbar-logo">
                    <span>Daily AiPhone</span>
                </a>
                <div class="navbar-links">
                    <a href="home=user.html" class="nav-link">Beranda</a>
                    <a href="view_article=user.php" class="nav-link">Daftar Artikel</a>
                    <a href="view_product=user.php" class="nav-link active">Daftar Produk</a>
                    <a href="about=user.html" class="nav-link">Tentang Kami</a>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus Produk</h3>
            <p>Apakah anda yakin ingin menghapus produk ini?</p>
            <div class="modal-buttons">
                <button class="modal-btn confirm-btn" onclick="confirmDeleteAction()">Ya</button>
                <button class="modal-btn cancel-btn" onclick="hideDeleteConfirm()">Tidak</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="greeting_h2">Daftar Produk</h2>
        <p class="greeting_p">
            Temukan berbagai pilihan AiPhone dengan spesifikasi dan harga yang beragam. Setiap produk yang kami tampilkan dilengkapi dengan informasi detail mengenai model, kapasitas penyimpanan, dan harga terkini. Jelajahi daftar produk kami dan temukan iPhone yang paling sesuai dengan kebutuhan dan anggaran Anda.
        </p>
        <table border="1">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Penyimpanan</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr data-id="<?php echo $row['aiphone_id']; ?>">
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
                        <td><?php echo htmlspecialchars($row['Storage']); ?> GB</td>
                        <td>Rp <?php echo number_format($row['Price'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Kelompok 8 - XI SIJA 1. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Ensure modal, spinner, and popup are hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, hiding modals, spinner, and popup');
            document.getElementById('logoutConfirmModal').style.display = 'none';
            document.getElementById('deleteConfirmModal').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('successPopup').style.display = 'none';
        });

        function showLoading(message) {
            console.log('Showing loading spinner');
            document.body.setAttribute('aria-busy', 'true');
            document.getElementById('loadingSpinner').style.display = 'flex';
            window.currentActionMessage = message || 'Action completed';
        }

        function hideLoading() {
            console.log('Hiding loading spinner');
            document.body.setAttribute('aria-busy', 'false');
            document.getElementById('loadingSpinner').style.display = 'none';
            showPopup(window.currentActionMessage, window.currentActionStatus || 'success');
        }

        function showPopup(message, status = 'success') {
            console.log('Showing popup with message:', message, 'status:', status);
            const popup = document.getElementById('successPopup');
            const popupContent = document.querySelector('.popup-content');
            document.getElementById('popupMessage').textContent = message;
            popup.classList.remove('popup-success', 'popup-error');
            popup.classList.add(`popup-${status}`);
            popup.style.display = 'flex';
            setTimeout(hidePopup, 1500); // Auto-hide after 1.5 seconds
        }

        function hidePopup() {
            console.log('Hiding popup');
            document.getElementById('successPopup').style.display = 'none';
        }

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
            showLoading('Logged out successfully');
            setTimeout(() => {
                hideLoading();
                window.location.href = 'logout.php';
            }, 1500);
        }

        let deleteLink = null;
        function showDeleteConfirm(element, message) {
            console.log('Showing delete confirmation modal');
            deleteLink = element.href;
            window.currentActionMessage = message;
            document.getElementById('deleteConfirmModal').style.display = 'flex';
            return false;
        }

        function hideDeleteConfirm() {
            console.log('Hiding delete confirmation modal');
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        function confirmDeleteAction() {
            console.log('Confirming delete, proceeding with deletion');
            showLoading(window.currentActionMessage);
            fetch(deleteLink, { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    window.currentActionStatus = data.success ? 'success' : 'error';
                    window.currentActionMessage = data.message;
                    hideLoading();
                    if (data.success) {
                        setTimeout(() => {
                            window.location.reload(); // Refresh to update table
                        }, 1500);
                    }
                })
                .catch(error => {
                    window.currentActionStatus = 'error';
                    window.currentActionMessage = 'An error occurred: ' + error.message;
                    hideLoading();
                });
        }

        // Simulate loading for edit and create buttons
        document.querySelectorAll('.edit-btn, .create-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                setTimeout(hideLoading, 1500);
            });
        });
    </script>
</body>
</html>