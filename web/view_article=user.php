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
    <link rel="stylesheet" href="style=user.css">
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner"></div>
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
                    <a href="view_article.php" class="nav-link active">Daftar Artikel</a>
                    <a href="view_product=user.php" class="nav-link">Daftar Produk</a>
                    <a href="about=user.html" class="nav-link">Tentang Kami</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Daftar Artikel</h2>

        <p class="centered-text">
            Temukan informasi terkini, ulasan mendalam, dan berita terbaru seputar teknologi, termasuk peluncuran produk terbaru dari Apple. Jelajahi artikel pilihan kami untuk mendapatkan wawasan lengkap tentang perangkat terkini, fitur unggulan, dan inovasi terbaru di dunia teknologi.
        </p>

            
        <div class="article-list">
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="article-item">
                    <div class="article-image-container">
                        <div class="article-image-wrapper">
                            <?php
                            $imagePath = $row['image_url'];
                            $filePath = __DIR__ . '/' . $imagePath;
                            if (!empty($imagePath) && file_exists($filePath)) {
                                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Article Image" class="article-image">';
                            } else {
                                echo '<p class="no-image">No image available</p>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="article-info">
                        <a href="article_detail=user.php?id=<?php echo $row['article_id']; ?>" class="article-link" onclick="showLoading('Memuat artikel...')">
                            <h3 class="article-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                        </a>
                        <p class="article-date"><?php echo htmlspecialchars($row['publish_date']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> Kelompok 8 - XI SIJA 1. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Ensure spinner is hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, hiding spinner');
            document.getElementById('loadingSpinner').style.display = 'none';
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
        }

        // Simulate loading for article links
        document.querySelectorAll('.article-link').forEach(link => {
            link.addEventListener('click', function(e) {
                setTimeout(hideLoading, 1500);
            });
        });
    </script>
</body>
</html>