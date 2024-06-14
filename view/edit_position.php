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

    // Check if position with the same name and status = 1 already exists
    $checkStatement = $pdo->prepare("SELECT * FROM position WHERE name = :name AND status = 1");
    $checkStatement->bindValue(':name', $name);
    $checkStatement->execute();
    $existingPosition = $checkStatement->fetch(PDO::FETCH_ASSOC);

    if ($existingPosition && $existingPosition['id'] != $id) {
        echo json_encode(["success" => false, "message" => "Name already exists in table with status 1"]);
        exit();
    }

    // Update position
    $statement = $pdo->prepare("UPDATE position SET name = :name WHERE id = :id");
    $statement->bindValue(':name', $name);
    $statement->bindValue(':id', $id);
    if ($statement->execute()) {
        // Fetch the updated position
        $statement = $pdo->prepare("SELECT * FROM position WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();
        $updatedPosition = $statement->fetch(PDO::FETCH_ASSOC);

        // Send the updated position name back to the client
        echo json_encode(["success" => true, "name" => $updatedPosition['name']]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update position"]);
    }
}
?>
