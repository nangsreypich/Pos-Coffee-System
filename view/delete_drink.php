<?php
session_start();
require("../controller/connection.php");

$response = array('success' => false, 'error' => '');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];
    $deleteStatement = $pdo->prepare("UPDATE drink SET status=0 where id = :id");
    $deleteStatement->bindValue(':id', $deleteId);

    if ($deleteStatement->execute()) {
        $response['success'] = true;
    }
} else {
    $response['error'] = "Invalid request.";
}

echo json_encode($response);
?>
