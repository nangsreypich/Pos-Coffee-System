<?php
// delete_stock_order.php
require("../controller/connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    try {
        $statement = $pdo->prepare("UPDATE stock_order SET status = 0 WHERE id = :id");
        $statement->execute(array(":id" => $id));

        // Assuming success, you might want to return a JSON response
        echo json_encode(array("success" => true));
        exit();
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(array("success" => false, "error" => "Database error: " . $e->getMessage()));
        exit();
    }
} else {
    // Invalid request
    echo json_encode(array("success" => false, "error" => "Invalid request"));
    exit();
}
?>
