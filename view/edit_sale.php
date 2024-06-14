<?php
session_start();
require("../controller/connection.php");

$response = array('success' => false, 'error' => '');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['id'])) {
    $cus_id = $_POST["cus_id"] ?? '';
    $invoice_id = $_POST["invoice_id"] ?? '';
    $cat_id = $_POST["cat_id"] ?? '';
    $drink_id = $_POST["drink_id"] ?? '';
    $price = $_POST["price"] ?? '';
    $qty = $_POST["qty"] ?? '';
    $total = $_POST["total"] ?? '';
    $total_payment = $_POST["total_payment"] ?? '';
    $change = $_POST["change"] ?? '';
    $table_id = $_POST["table_id"] ?? ''; // Assuming you have added this field

    $id = $_POST["id"] ?? '';

    if (empty($cus_id) || empty($invoice_id) || empty($cat_id) || empty($drink_id) || empty($price) || empty($qty) || empty($total) || empty($total_payment) || empty($change) || empty($table_id)) {
        $response['error'] = "All fields are required";
        echo json_encode($response);
        exit();
    }

    $sale_date = date("Y-m-d"); // current date

    $upSt = $pdo->prepare("UPDATE sale SET cus_id=:cus_id, invoice_id=:invoice_id, cat_id=:cat_id, drink_id=:drink_id, price=:price, qty=:qty, total=:total, total_payment=:total_payment, `change`=:change, sale_date=:sale_date, table_id=:table_id WHERE id=:id");
    $upSt->bindValue(':cus_id', $cus_id);
    $upSt->bindValue(':invoice_id', $invoice_id);
    $upSt->bindValue(':cat_id', $cat_id);
    $upSt->bindValue(':drink_id', $drink_id);
    $upSt->bindValue(':price', $price);
    $upSt->bindValue(':qty', $qty);
    $upSt->bindValue(':total', $total);
    $upSt->bindValue(':total_payment', $total_payment);
    $upSt->bindValue(':change', $change);
    $upSt->bindValue(':sale_date', $sale_date);
    $upSt->bindValue(':table_id', $table_id); // Binding table_id
    $upSt->bindValue(':id', $id);

    if ($upSt->execute()) {
        $response['success'] = true;
    }
} else {
    $response['error'] = "Invalid request.";
}

echo json_encode($response);
?>
