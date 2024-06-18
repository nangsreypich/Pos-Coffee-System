<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}

// Connection
require("../controller/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $cus_id = htmlspecialchars($_POST['cus_id']);
    $invoice_id = htmlspecialchars($_POST['invoice_id']);
    $drink_id = filter_var($_POST['drink_id'], FILTER_VALIDATE_INT);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $qty = filter_var($_POST['qty'], FILTER_VALIDATE_INT);
    $total = filter_var($_POST['total'], FILTER_VALIDATE_FLOAT);
    $total_payment = filter_var($_POST['total_payment'], FILTER_VALIDATE_FLOAT);
    $change = filter_var($_POST['change'], FILTER_VALIDATE_FLOAT);
    $sale_date = htmlspecialchars($_POST['sale_date']);

    if (!$id || !$drink_id || !$price || !$qty || !$total || !$total_payment || !$change || !$sale_date) {
        echo json_encode(['success' => false, 'error' => 'Invalid or missing data']);
        exit();
    }

    try {
        // Prepare update query
        $statement = $pdo->prepare("UPDATE sale SET 
            cus_id = :cus_id,
            invoice_id = :invoice_id,
            drink_id = :drink_id,
            price = :price,
            qty = :qty,
            total = :total,
            total_payment = :total_payment,
            change = :change,
            sale_date = :sale_date
            WHERE id = :id");

        // Bind parameters
        $statement->bindParam(':cus_id', $cus_id);
        $statement->bindParam(':invoice_id', $invoice_id);
        $statement->bindParam(':drink_id', $drink_id);
        $statement->bindParam(':price', $price);
        $statement->bindParam(':qty', $qty);
        $statement->bindParam(':total', $total);
        $statement->bindParam(':total_payment', $total_payment);
        $statement->bindParam(':change', $change);
        $statement->bindParam(':sale_date', $sale_date);
        $statement->bindParam(':id', $id);

        // Execute query
        if ($statement->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update sale.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
