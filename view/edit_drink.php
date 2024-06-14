<?php
session_start();
require("../controller/connection.php");

$response = array('success' => false, 'error' => '');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['id'])) {
    $name = $_POST["name"] ?? '';
    $price = $_POST["price"] ?? '';
    $description = $_POST["description"] ?? '';
    $category = $_POST["cat_id"] ?? '';
    $id = $_POST["id"] ?? '';

    if (empty($name) || empty($price) || empty($category)) {
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

    $upSt = $pdo->prepare("UPDATE drink SET name=:name, price=:price, description=:description, image=:image, cat_id=:cat_id WHERE id=:id");
    $upSt->bindValue(':name', $name);
    $upSt->bindValue(':price', $price);
    $upSt->bindValue(':description', $description);
    $upSt->bindValue(':cat_id', $category);
    $upSt->bindValue(':id', $id);
    $upSt->bindValue(':image', $imagePath);

    if ($upSt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = "Failed to update drink.";
    }
} else {
    $response['error'] = "Invalid request.";
}

echo json_encode($response);
?>
