<?php
require_once 'Database=user.php';
require_once 'Article=user.php';

$database = new Database();
$db = $database->getConnection();
$article = new Article($db);

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$title = '';
$content = '';
$image_url = '';
$publish_date = '';

if ($article_id > 0) {
    $query = "SELECT title, content, image_url, publish_date FROM articles WHERE article_id = :article_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $title = $row['title'];
        $content = $row['content'];
        $image_url = $row['image_url'];
        $publish_date = $row['publish_date'];
    } else {
        $message = "Artikel tidak ditemukan";
        $status = "error";
    }
}

// Split content into paragraphs
$paragraphs = array_filter(array_map('trim', explode("\n", $content)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="style=user.css">
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
                <a href="view_article=user.php" class="navbar-brand">
                    <img src="assets/image/logo.png" alt="Logo" class="navbar-logo">
                    <span>Daily AiPhone</span>
                </a>
                <div class="navbar-links">
                    <a href="view_article=user.php" class="nav-link logout-link">Kembali</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($message) && $status == 'error') : ?>
            <p class="popup-error"><?php echo $message; ?></p>
        <?php else : ?>
            <div class="article-detail">
                <?php if (!empty($image_url) && file_exists(__DIR__ . '/' . $image_url)) : ?>
                    <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Article Image" class="article-image">
                <?php else : ?>
                    <p class="no-image">No image available</p>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <p class="publish-date"> Dipublikasikan pada <?php echo htmlspecialchars($publish_date); ?></p>
                <div class="article-content">
                    <?php foreach ($paragraphs as $paragraph) : ?>
                        <p><?php echo htmlspecialchars($paragraph); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

     <div class="button-container">
        <a href="view_article=user.php" onclick="showLoading('Navigating to create article')"><button class="create-btn">Kembali ke daftar artikel</button></a>
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
                <?php if ($status == 'error') : ?>
                    setTimeout(() => { window.location.href = 'view_article=user.php'; }, 1500);
                <?php endif; ?>
            });
        <?php endif; ?>

        // Ensure spinner and popup are hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, hiding spinner and popup');
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
    </script>
</body>
</html>