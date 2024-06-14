<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}
//1-Create connection
require("../controller/connection.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        //2-Prepare statement for delete
        $stRemove = $pdo->prepare("DELETE FROM sale WHERE id = :id");
        //3-bindvalue
        $id = $_REQUEST["id"];
        $stRemove->bindValue(':id', $id);
        //3-Execute
        $stRemove->execute();
        // Set success message
        $_SESSION['success_message'] = "Sale deleted successfully.";
        //Go to sale list
        header("Location: sales.php");
        exit();
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error_message'] = "Failed to delete sale. Error: " . $e->getMessage();
        // Redirect back to delete page
        header("Location: delete_sale.php?id=" . $id);
        exit();
    }
}

// Retrieve sale ID
$id = $_REQUEST["id"];

// Prepare statement to fetch sale details
$statement = $pdo->prepare("
    SELECT sale.*, coffee_table.name AS coffee_name, drink.name AS drink_name 
    FROM sale 
    JOIN coffee_table ON sale.table_id = coffee_table.id 
    JOIN drink ON sale.drink_id = drink.id 
    WHERE sale.id = :id AND sale.status = 1
");


// Bind sale ID parameter
$statement->bindValue(':id', $id);

// Execute statement
$statement->execute();

// Fetch sale details
$saleDoc = $statement->fetch(PDO::FETCH_ASSOC);

?>

<?php include('head.php') ?>
<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-md-9">
            <center>
                <?php
                // Display success message if set
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                    unset($_SESSION['success_message']); // Clear session message after displaying
                }
                // Display error message if set
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']); // Clear session message after displaying
                }
                ?>
                <h1>Delete Sale</h1>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="table_id">Table : <?php echo $saleDoc['coffee_name']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="cus_id">Customer ID: <?php echo $saleDoc['cus_id']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_id">Invoice ID: <?php echo $saleDoc['invoice_id']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="drink_id">Drink : <?php echo $saleDoc['drink_name']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="price">Price: <?php echo $saleDoc['price']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="qty">Quantity: <?php echo $saleDoc['qty']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="total">Total: <?php echo $saleDoc['total']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="total_payment">Total Payment: <?php echo $saleDoc['total_payment']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="change">Change: <?php echo $saleDoc['change']; ?></label>
                    </div>

                    <div class="mb-3">
                        <label for="sale_date">Sale Date: <?php echo $saleDoc['sale_date']; ?></label>
                    </div>
                    <input class="btn btn-danger" type="submit" value="Confirm Delete" />
                </form>
            </center>
        </div>
    </div>
</div>
