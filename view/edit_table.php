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
    $name = $_POST["name"];
    $id = $_POST["id"];

    // Validation
    if (!$name) {
        echo json_encode(["success" => false, "message" => "Name is required"]);
        exit();
    }

    // Check if table with the same name and status = 1 already exists
    $checkStatement = $pdo->prepare("SELECT * FROM coffee_table WHERE name = :name AND status = 1");
    $checkStatement->bindValue(':name', $name);
    $checkStatement->execute();
    $existingTable = $checkStatement->fetch(PDO::FETCH_ASSOC);

    if ($existingTable && $existingTable['id'] != $id) {
        echo json_encode(["success" => false, "message" => "Name already exists in table with status 1"]);
        exit();
    }

    // Update table
    $statement = $pdo->prepare("UPDATE coffee_table SET name = :name WHERE id = :id");
    $statement->bindValue(':name', $name);
    $statement->bindValue(':id', $id);
    if ($statement->execute()) {
        // Fetch the updated table
        $statement = $pdo->prepare("SELECT * FROM coffee_table WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();
        $updatedTable = $statement->fetch(PDO::FETCH_ASSOC);

        // Send the updated table name back to the client
        echo json_encode(["success" => true, "name" => $updatedTable['name']]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update table"]);
    }
}
?>
