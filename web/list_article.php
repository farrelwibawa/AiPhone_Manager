<?php
require_once 'Database.php';
require_once 'Article.php';

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

// Fetch all articles
$stmt = $article->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Artikel</title>
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
                <a href="list_article.php" class="navbar-brand">
                    <img src="assets/image/logo.png" alt="Logo" class="navbar-logo">
                    <span>AiPhone Manager</span>
                </a>
                <div class="navbar-links">
                    <a href="list_article.php" class="nav-link active">Kelola Artikel</a>
                    <a href="list_product.php" class="nav-link">Kelola Produk</a>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus Artikel</h3>
            <p>Apakah anda yakin ingin menghapus artikel ini?</p>
            <div class="modal-buttons">
                <button class="modal-btn confirm-btn" onclick="confirmDeleteAction()">Ya</button>
                <button class="modal-btn cancel-btn" onclick="hideDeleteConfirm()">Tidak</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Daftar Artikel</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Artikel</th>
                    <th>Tanggal Publikasi</th>
                    <th>Kelola</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr data-id="<?php echo $row['article_id']; ?>">
                        <td>
                            <?php
                            $imagePath = $row['image_url'];
                            $filePath = __DIR__ . '/' . $imagePath;
                            if (!empty($imagePath) && file_exists($filePath)) {
                                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Article Image" width="50">';
                            } else {
                                echo 'No image available';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['publish_date']); ?></td>
                        <td>
                            <a href="edit_article.php?id=<?php echo $row['article_id']; ?>" class="edit-btn" onclick="showLoading('Navigating to edit article')">✏️</a>
                            <a href="delete_article.php?id=<?php echo $row['article_id']; ?>" class="delete-btn" onclick="return showDeleteConfirm(this, 'Artikel berhasil dihapus')">🗑️</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="button-container">
        <a href="create_article.php" onclick="showLoading('Navigating to create article')"><button class="create-btn">Tambahkan Artikel</button></a>
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