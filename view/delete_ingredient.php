<?php
// delete_ingredient.php
require("../controller/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Perform soft delete (update status to 0)
    $statement = $pdo->prepare("UPDATE ingredient SET status = 0 WHERE id = ?");
    $statement->execute([$id]);

    if ($statement) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete ingredient']);
    }
}
?>
