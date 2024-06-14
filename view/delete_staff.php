<?php 
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}

// Create connection
require("../controller/connection.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        // Prepare statement for delete
        $email = $_REQUEST["email"];
        $id = $_REQUEST["id"];

        // Fetch the position of the staff
        $stmt_position = $pdo->prepare("SELECT pos_id FROM staff WHERE id = :id");
        $stmt_position->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt_position->execute();
        $position_row = $stmt_position->fetch(PDO::FETCH_ASSOC);
        $position_id = $position_row['pos_id'];

        // Update status in staff table
        $stRemove = $pdo->prepare("UPDATE staff SET status = 0 WHERE id = :id");
        $stRemove->bindValue(':id', $id, PDO::PARAM_INT);
        $stRemove->execute();

        // Update status in position-specific table if not a staff position
        if ($position_id !== "Staff") {
            $table_name = strtolower($position_id);
            $stmt_remove_position = $pdo->prepare("UPDATE $table_name SET status = 0 WHERE email = :email");
            $stmt_remove_position->bindValue(':email', $email, PDO::PARAM_INT);
            $stmt_remove_position->execute();
        }

        // Set success message
        $_SESSION['success_message'] = "Staff deleted successfully.";
        // Go to staff list
        header("Location: all_staff.php");
        exit();
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error_message'] = "Failed to delete staff. Error: " . $e->getMessage();
        // Redirect back to delete page
        header("Location: delete_staff.php?id=" . $id);
        exit();
    }
}

// This code runs first
$id = $_REQUEST["id"];
// Prepare statement
$statement = $pdo->prepare("SELECT staff.*, position.name AS position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.status=1");

// Execute
$statement->execute();

$proDoc = $statement->fetch(PDO::FETCH_ASSOC);

?>
