<?php
require_once 'Database.php';
require_once 'Phone.php';

$database = new Database();
$db = $database->getConnection();
$phone = new Phone($db);

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $phone->aiphone_id = intval($_GET['id']);
    if ($phone->delete()) {
        $response['success'] = true;
        $response['message'] = 'Barang berhasil dihapus';
    } else {
        $response['message'] = 'Unable to delete phone.';
    }
} else {
    $response['message'] = 'Invalid phone ID!';
}

echo json_encode($response);
exit;
?>