<?php 
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

// Create connection
require("../controller/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Prepare statement for delete
        $stocker_id = $_REQUEST["stocker_id"];
        $product_id = $_REQUEST["product_id"];

        // Update status in stock_order table
        $stmtRemove = $pdo->prepare("UPDATE stock_order SET status = 0 WHERE stocker_id = :stocker_id AND product_id = :product_id");
        $stmtRemove->bindValue(':stocker_id', $stocker_id, PDO::PARAM_INT);
        $stmtRemove->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmtRemove->execute();

        // Set success message
        $_SESSION['success_message'] = "Stock order deleted successfully.";
        // Go to stock order list
        header("Location: all_stock_orders.php");
        exit();
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error_message'] = "Failed to delete stock order. Error: " . $e->getMessage();
        // Redirect back to delete page
        header("Location: delete_stock_order.php?stocker_id=" . $stocker_id . "&product_id=" . $product_id);
        exit();
    }
}

// This code runs first
$stocker_id = $_REQUEST["stocker_id"];
$product_id = $_REQUEST["product_id"];
// Prepare statement
$statement = $pdo->prepare("SELECT * FROM stock_order WHERE stocker_id = :stocker_id AND product_id = :product_id AND status = 1");

// Bind parameters
$statement->bindValue(':stocker_id', $stocker_id, PDO::PARAM_INT);
$statement->bindValue(':product_id', $product_id, PDO::PARAM_INT);

// Execute
$statement->execute();

$stockOrder = $statement->fetch(PDO::FETCH_ASSOC);

?>