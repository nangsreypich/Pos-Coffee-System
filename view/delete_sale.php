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
    // Get POST data
    $id = $_POST['delete_id'];

    // Prepare update query to set status to 0
    $statement = $pdo->prepare("UPDATE sale SET status = 0 WHERE id = :id");

    // Bind parameter
    $statement->bindParam(':id', $id);

    // Execute query
    if ($statement->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update sale status.']);
    }
}
?>
