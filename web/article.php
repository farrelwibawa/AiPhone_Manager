<?php
   class Article {
       private $conn;
       private $table_name = "articles";
       private $comments_table = "comments";

       public function __construct($db) {
           $this->conn = $db;
       }

       // Read all articles
       public function readAll() {
           $query = "SELECT article_id, image_url, title, publish_date, content 
                     FROM " . $this->table_name . " 
                     ORDER BY publish_date DESC";
           $stmt = $this->conn->prepare($query);
           $stmt->execute();
           return $stmt;
       }

       // Get comments for a specific article
       public function getComments($article_id) {
           $query = "SELECT comment_text, user_name 
                     FROM " . $this->comments_table . " 
                     WHERE article_id = :article_id 
                     ORDER BY comment_date DESC";
           $stmt = $this->conn->prepare($query);
           $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
           $stmt->execute();
           return $stmt;
       }
   }
   ?>