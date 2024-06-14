<?php 
session_start();
if (!isset($_SESSION["username"])) {
   // Redirect to the login page if not logged in
   header("Location: ../user/login.php");
   exit(); // Make sure to exit after redirection
}

// 1-Create connection
require("../controller/connection.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        // 2-Prepare statement for delete
        $stRemove = $pdo->prepare("UPDATE users SET status=0 WHERE id = :id");
        // 3-bindvalue
        $id = $_REQUEST["id"];
        $stRemove->bindValue(':id', $id, PDO::PARAM_INT);
        // 3-Execute
        $stRemove->execute();
        // Set success message
        $_SESSION['success_message'] = "Position deleted successfully.";
        // Go to position list
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error_message'] = "Failed to delete position. Error: " . $e->getMessage();
        // Redirect back to delete page
        header("Location: delete_user.php?id=" . $id);
        exit();
    }
}

// This code runs first
$id = $_REQUEST["id"];
// 2-Prepare statement
$statement = $pdo->prepare("SELECT * FROM users WHERE id = :id AND status=1");

// 3-BindValue
$statement->bindValue(':id', $id, PDO::PARAM_INT);

// 3-Execute
$statement->execute();

$proDoc = $statement->fetch(PDO::FETCH_ASSOC);

?>

<?php include('head.php'); ?>
<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php'); ?>
        <!-- Content -->
        <div class="col-md-9">
            <center>
                <?php
                // Display success message if set
                if(isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                    unset($_SESSION['success_message']); // Clear session message after displaying
                }
                // Display error message if set
                if(isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']); // Clear session message after displaying
                }
                ?>
                <h1>Delete User</h1>
                <p>User Name: <?php echo htmlspecialchars($proDoc['username']); ?></p>
                <p>Password: <?php echo htmlspecialchars($proDoc['password']); ?></p>
                <p>Type: <?php echo htmlspecialchars($proDoc['type']); ?></p>
                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($proDoc['id']); ?>"/>
                    <input class="btn btn-danger" type="submit" value="Confirm Delete" />
                </form>
            </center>
        </div>
    </div>
</div>
