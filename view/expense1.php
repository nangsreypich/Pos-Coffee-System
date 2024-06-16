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

// Determine if the page is being accessed for filtering or printing
$isPrinting = isset($_GET['print']) && $_GET['print'] == 'true';

// Initialize date variable based on whether it's for filtering or printing
$date = date('Y-m-d');
if (!$isPrinting && isset($_GET['order_date'])) {
    $date = $_GET['order_date'];
} elseif ($isPrinting && isset($_GET['date'])) {
    $date = $_GET['date'];
}

// Prepare Query for Expense Report
$statement = $pdo->prepare("SELECT so.*, ingredient.product_name
    FROM stock_order AS so 
    INNER JOIN ingredient ON so.product_id = ingredient.id
    WHERE DATE(so.order_date) = :order_date");
$statement->bindParam(':order_date', $date);
$statement->execute();

// Get Data
$orderList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Calculate total expense for the selected date
$totalExpense = 0;
foreach ($orderList as $order) {
    $totalExpense += $order['price'];
}

if ($isPrinting) {
    // If printing, show only the print layout
    include('head.php');
    ?>
    <div style="text-align:center;margin-top:20px">
        <img src="../image/Logo_ASSIGMENT.jpg" alt="Logo" style="width:70px;height: 70px;border-radius:35px">
        <h1>Third Coffee Shop</h1>
        <h5>Expense Report</h5>
        <h3><?php echo $date; ?></h3>
    </div>

    <div class="container-fluid mt-4">
        <table class="table table-striped table-bordered mt-2">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Stocker</th>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Order Date</th>
                    <th scope="col">Expired Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderList as $key => $order) { ?>
                    <tr>
                        <td><?php echo $key + 1 ?></td>
                        <td><?php echo $order['stocker_id']; ?></td>
                        <td><?php echo $order['product_name']; ?></td>
                        <td>$<?php echo $order['price']; ?></td>
                        <td><?php echo $order['qty']; ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><?php echo $order['expired_date']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"><strong>Total Expense for <?php echo $date; ?>:</strong></td>
                    <td colspan="3"><strong>$<?php echo number_format($totalExpense, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
    // JavaScript to close the print window after printing
    window.onload = function() {
        window.print();
        setTimeout(window.close, 0); // Close the print window after printing
    }
    </script>
    <?php
} else {
    // If not printing, show the filter and table display
    include('head.php');
    ?>
    <div class="container-fluid">
        <?php include('header.php'); ?>
        <div class="row">
            <!-- Sidebar -->
            <?php include('sidebar_stocker.php') ?>
            <!-- Main Content Area -->
            <div class="col-md-10 mt-4">
                <h1>Expense Report</h1>
                <form method="get" class="mb-3">
                    <label for="order_date">Select Date:</label>
                    <input type="date" id="order_date" name="order_date" value="<?php echo $date; ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <div class="table-responsive">
                    <a href="add_expense.php"><button style="float:right;" class="btn btn-primary mb-2">+ Add Expense</button></a>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?print=true&date=' . urlencode($date); ?>" target="_blank" class="btn btn-secondary mb-2" style="float:right; margin-right: 10px;">Print Report</a>
                    <table id="example1" class="table table-striped table-bordered mt-2">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Stocker</th>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Order Date</th>
                                <th scope="col">Expired Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderList as $key => $order) { ?>
                                <tr>
                                    <td><?php echo $key + 1 ?></td>
                                    <td><?php echo $order['stocker_id']; ?></td>
                                    <td><?php echo $order['product_name']; ?></td>
                                    <td>$<?php echo $order['price']; ?></td>
                                    <td><?php echo $order['qty']; ?></td>
                                    <td><?php echo $order['order_date']; ?></td>
                                    <td><?php echo $order['expired_date']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"><strong>Total Expense for <?php echo $date; ?>:</strong></td>
                                <td colspan="3"><strong>$<?php echo number_format($totalExpense, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    // JavaScript for handling the form submission
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var orderDate = document.getElementById('order_date').value;
            window.location.href = '<?php echo $_SERVER['PHP_SELF'] . '?print=false'; ?>&order_date=' + encodeURIComponent(orderDate);
        });
    });
    </script>
    <?php
}
?>
