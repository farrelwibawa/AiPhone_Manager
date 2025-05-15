<?php
    require_once 'Database.php';
    require_once 'Article.php';

    $database = new Database();
    $db = $database->getConnection();
    $article = new Article($db);

    $article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($article_id > 0) {
        $query = "DELETE FROM articles WHERE article_id = :article_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Artikel berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus artikel']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID artikel tidak valid']);
    }
    ?>