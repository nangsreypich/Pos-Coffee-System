<?php
session_start();
require("../controller/connection.php");

$response = array('success' => false, 'error' => '');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['id'])) {
    $product_name = $_POST["product_name"] ?? '';
    $price = $_POST["price"] ?? '';
    $id = $_POST["id"] ?? '';

    if (empty($product_name) || empty($price)) {
        $response['error'] = "All fields are required";
        echo json_encode($response);
        exit();
    }

    $image = $_FILES['image'] ?? null;
    $imagePath = "";
    if ($image && $image["name"]) {
        $imagePath = "../image/" . date("YmdHis") . basename($image['name']);
        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            $response['error'] = "Failed to upload image.";
            echo json_encode($response);
            exit();
        }
    } else {
        $imagePath = $_POST["oldImage"];
    }

    $upSt = $pdo->prepare("UPDATE ingredient SET product_name=:product_name, price=:price,  image=:image WHERE id=:id");
    $upSt->bindValue(':product_name', $product_name);
    $upSt->bindValue(':price', $price);
    $upSt->bindValue(':id', $id);
    $upSt->bindValue(':image', $imagePath);

    if ($upSt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = "Failed to update ingredient.";
    }
} else {
    $response['error'] = "Invalid request.";
}

echo json_encode($response);
?>
