<?php
require_once 'Database.php';
require_once 'Phone.php';

$database = new Database();
$db = $database->getConnection();
$phone = new Phone($db);

// Check if ID is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $phone->aiphone_id = $_GET['id'];
    if ($phone->delete()) {
        header("Location: list_product.php");
        exit;
    } else {
        echo "Unable to delete phone.";
    }
} else {
    echo "Invalid phone ID!";
    exit;
}
?>