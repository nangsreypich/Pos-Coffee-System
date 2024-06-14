<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    require("../controller/connection.php");

    // Get data from form
    $id = $_POST["id"];

    // Delete category
    $statement = $pdo->prepare("UPDATE category SET status=0 where id = :id");
    $statement->bindValue(':id', $id);
    if ($statement->execute()) {
        echo "Category deleted successfully";
    } else {
        echo "Failed to delete category";
    }
}
?>