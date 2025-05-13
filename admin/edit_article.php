<?php
require_once 'Database.php';
require_once 'Article.php';

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$title = '';
$content = '';
$image_url = '';

if ($article_id > 0) {
    $query = "SELECT title, content, image_url FROM articles WHERE article_id = :article_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $title = $row['title'];
        $content = $row['content'];
        $image_url = $row['image_url'];
    } else {
        $message = "Artikel tidak ditemukan";
        $status = "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $new_image_url = $image_url;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $new_image_url = $target_file;
        }
    }

    // Update article
    $query = "UPDATE articles SET title = :title, content = :content, image_url = :image_url WHERE article_id = :article_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':image_url', $new_image_url);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $message = "Artikel berhasil diperbarui";
        $status = "success";
    } else {
        $message = "Gagal memperbarui artikel";
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel</title>
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
            <p id="popupMessage"><?php echo isset($message) ? $message : ''; ?></p>
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
                    <a href="list_article.php" class="nav-link logout-link">Kembali</a>
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
        <div class="form">
            <h2>Edit Artikel</h2>
            <?php if (isset($message) && $status == 'error') : ?>
                <p class="popup-error"><?php echo $message; ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Judul Artikel</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Isi Artikel</label>
                    <textarea id="content" name="content" class="article-content" required><?php echo htmlspecialchars($content); ?></textarea>
                </div>
                <div class="form-group">
                    <div class="file-input-wrapper">
                        <label for="image" class="file-input-label">Gambar Artikel</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <?php if ($image_url) : ?>
                        <p class="current-image">Gambar saat ini: <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Current Image" style="width: 100px; margin-top: 10px;"></p>
                    <?php endif; ?>
                </div>
                <div class="button-container">
                    <button type="submit" class="edit-btn" onclick="showLoading('Menyimpan perubahan...')">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Kelompok 8 - XI SIJA 1. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Show popup if message exists
        <?php if (isset($message)) : ?>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup('<?php echo $message; ?>', '<?php echo $status; ?>');
                <?php if ($status == 'success') : ?>
                    setTimeout(() => { window.location.href = 'list_article.php'; }, 1500);
                <?php endif; ?>
            });
        <?php endif; ?>

        // Ensure modals, spinner, and popup are hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, hiding modals and spinner');
            document.getElementById('logoutConfirmModal').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'none';
            <?php if (!isset($message)) : ?>
                document.getElementById('successPopup').style.display = 'none';
            <?php endif; ?>
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
            setTimeout(hidePopup, 1500);
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
    </script>
</body>
</html>