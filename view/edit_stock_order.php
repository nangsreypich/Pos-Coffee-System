<?php
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}

$error = [];
$success = '';

// Include database connection
require_once("../controller/connection.php");

//====Update Operation====
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
      $update_statement->bindValue(':price', $price);
      $update_statement->bindValue(':qty', $qty);
      $update_statement->bindValue(':order_date', $order_date);
      $update_statement->bindValue(':expired_date', $expired_date);
      $update_statement->bindValue(':id', $id);

      if ($update_statement->execute()) {
         $success = "Stock order updated successfully!";
      } else {
         $error[] = "Error updating stock order. Please try again.";
      }
   }
}

// Retrieve stock order ID from POST
$id = $_POST["id"];

// Fetch updated stock order details for display
$statement = $pdo->prepare("SELECT stock_order.*, stocker.name as stocker_name, product.name as product_name FROM stock_order INNER JOIN stocker ON stock_order.stocker_id = stocker.id INNER JOIN product ON stock_order.product_id = product.id WHERE stock_order.id = :id");
$statement->bindValue(':id', $id);
$statement->execute();
$order = $statement->fetch(PDO::FETCH_ASSOC);

// Fetch stockers for dropdown
$stockersStatement = $pdo->query("SELECT * FROM stocker");
$stockers = $stockersStatement->fetchAll(PDO::FETCH_ASSOC);

// Fetch products for dropdown
$productsStatement = $pdo->query("SELECT * FROM product");
$products = $productsStatement->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response if AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
   if (empty($error)) {
      // Prepare data for JSON response
      $response = [
         'success' => true,
         'stocker_name' => $order['stocker_name'],
         'product_name' => $order['product_name'],
         'price' => $order['price'],
         'qty' => $order['qty'],
         'order_date' => $order['order_date'],
         'expired_date' => $order['expired_date']
      ];
      echo json_encode($response);
   } else {
      // Return error message
      $response = [
         'success' => false,
         'message' => 'Failed to update stock order: ' . implode(', ', $error)
      ];
      echo json_encode($response);
   }
   exit; // Make sure to exit after handling AJAX response
}
?>
