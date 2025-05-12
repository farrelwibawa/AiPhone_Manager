<?php
class Phone {
    private $conn;
    public $aiphone_id;
    public $Name;
    public $Price;
    public $Storage;
    public $Specification;
    public $ImageURL;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO product (Name, Price, Storage, Specification, ImageURL) 
                  VALUES (:Name, :Price, :Storage, :Specification, :ImageURL)";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->Name = htmlspecialchars(strip_tags($this->Name));
        $this->Price = floatval($this->Price);
        $this->Storage = htmlspecialchars(strip_tags($this->Storage));
        $this->Specification = htmlspecialchars(strip_tags($this->Specification));
        $this->ImageURL = htmlspecialchars(strip_tags($this->ImageURL));

        $stmt->bindParam(':Name', $this->Name);
        $stmt->bindParam(':Price', $this->Price);
        $stmt->bindParam(':Storage', $this->Storage);
        $stmt->bindParam(':Specification', $this->Specification);
        $stmt->bindParam(':ImageURL', $this->ImageURL);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM product";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readSingle() {
        $query = "SELECT * FROM product WHERE aiphone_id = :aiphone_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':aiphone_id', $this->aiphone_id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE product 
                  SET Name = :Name, Price = :Price, Storage = :Storage, 
                      Specification = :Specification, ImageURL = :ImageURL 
                  WHERE aiphone_id = :aiphone_id";
        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->Name = htmlspecialchars(strip_tags($this->Name));
        $this->Price = floatval($this->Price);
        $this->Storage = htmlspecialchars(strip_tags($this->Storage));
        $this->Specification = htmlspecialchars(strip_tags($this->Specification));
        $this->ImageURL = htmlspecialchars(strip_tags($this->ImageURL));
        $this->aiphone_id = intval($this->aiphone_id);

        $stmt->bindParam(':Name', $this->Name);
        $stmt->bindParam(':Price', $this->Price);
        $stmt->bindParam(':Storage', $this->Storage);
        $stmt->bindParam(':Specification', $this->Specification);
        $stmt->bindParam(':ImageURL', $this->ImageURL);
        $stmt->bindParam(':aiphone_id', $this->aiphone_id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM product WHERE aiphone_id = :aiphone_id";
        $stmt = $this->conn->prepare($query);
        $this->aiphone_id = intval($this->aiphone_id);
        $stmt->bindParam(':aiphone_id', $this->aiphone_id);
        return $stmt->execute();
    }
}
?>