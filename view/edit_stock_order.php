<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit(); // Exit after redirection
}

// Include database connection
require_once("../controller/connection.php");

$error = [];
$success = '';

// Update Operation
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    // Get data from form
    $stocker_id = $_POST["stocker_id"];
    $product_id = $_POST["product_id"];
    $price = $_POST["price"];
    $qty = $_POST["qty"];
    $order_date = $_POST["order_date"];
    $expired_date = $_POST["expired_date"];
    $id = $_POST["id"]; // Don't forget to retrieve the ID

    // Validation
    if (!$stocker_id || !$product_id || !$price || !$qty || !$order_date || !$expired_date) {
        $error[] = "All fields are required";
    }

    // Prepare and execute update query
   if (empty($error)) {
    $update_statement = $pdo->prepare("UPDATE stock_order SET stocker_id=:stocker_id, product_id=:product_id, price=:price, qty=:qty, order_date=:order_date, expired_date=:expired_date WHERE id=:id");

    $update_statement->bindValue(':stocker_id', $stocker_id);
    $update_statement->bindValue(':product_id', $product_id);
    $update_statement->bindValue(':price', $price, PDO::PARAM_STR); // Adjust PARAM_STR or PARAM_INT based on your needs
    $update_statement->bindValue(':qty', $qty);
    $update_statement->bindValue(':order_date', $order_date);
    $update_statement->bindValue(':expired_date', $expired_date);
    $update_statement->bindValue(':id', $id);

    if ($update_statement->execute()) {
            // Fetch updated data for response
            $fetch_statement = $pdo->prepare("SELECT stock_order.*, staff.name AS stocker_name, ingredient.product_name AS product_name FROM stock_order LEFT JOIN staff ON stock_order.stocker_id = staff.id LEFT JOIN ingredient ON stock_order.product_id = ingredient.id WHERE stock_order.id = :id");
            $fetch_statement->bindValue(':id', $id);
            $fetch_statement->execute();
            $updated_data = $fetch_statement->fetch(PDO::FETCH_ASSOC);

            // Prepare data for JSON response
            $response = [
                'success' => true,
                'stocker_id' => $stocker_id,
                'product_id' => $product_id,
                'price' => $price,
                'qty' => $qty,
                'order_date' => $order_date,
                'expired_date' => $expired_date
            ];
            echo json_encode($response);
            exit; // Exit after JSON response
        } else {
            $error[] = "Error updating stock order. Please try again.";
        }
    }
}

// Return error message if not handled by AJAX
$response = [
    'success' => false,
    'errors' => $error // Return all errors as an array
];
echo json_encode($response);
exit;
?>
