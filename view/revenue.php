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
if (!$isPrinting && isset($_GET['sale_date'])) {
    $date = $_GET['sale_date'];
} elseif ($isPrinting && isset($_GET['date'])) {
    $date = $_GET['date'];
}

// Prepare Query
$statement = $pdo->prepare("SELECT sale.*, drink.name as drink_name 
    FROM sale 
    INNER JOIN drink ON sale.drink_id = drink.id
    WHERE sale.status=1 AND DATE(sale.sale_date) = :sale_date
    AND so.status = 1");
$statement->bindParam(':sale_date', $date);
$statement->execute();

// Get Data
$saleList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Calculate total sales for the selected date
$totalSales = 0;
foreach ($saleList as $sale) {
    $totalSales += $sale['total'];
}

if ($isPrinting) {
    // If printing, show only the print layout
    include('head.php');
    ?>
    <div style="text-align:center;margin-top:20px">
        <img src="../image/Logo_ASSIGMENT.jpg" alt="Logo" style="width:70px;height: 70px;border-radius:35px">
        <h1>Third Coffee Shop</h1>
        <h5>Sale Report</h5>
        <h3><?php echo $date; ?></h3>
    </div>

    <div class="container-fluid mt-4">
        <table class="table table-striped table-bordered mt-2">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Invoice</th>
                    <th scope="col">Drink</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Change</th>
                    <th scope="col">Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saleList as $key => $pro) { ?>
                    <tr>
                        <td><?php echo $key + 1 ?></td>
                        <td><?php echo $pro['cus_id']; ?></td>
                        <td><?php echo $pro['invoice_id']; ?></td>
                        <td><?php echo $pro['drink_name']; ?></td>
                        <td><?php echo $pro['qty']; ?></td>
                        <td>$<?php echo $pro['total']; ?></td>
                        <td>$<?php echo $pro['total_payment']; ?></td>
                        <td>$<?php echo $pro['change']; ?></td>
                        <td><?php echo $pro['sale_date']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6"><strong>Total Sales for <?php echo $date; ?>:</strong></td>
                    <td colspan="4"><strong>$<?php echo number_format($totalSales, 2); ?></strong></td>
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
            <?php include('sidebar_manager.php') ?>
            <!-- Main Content Area -->
            <div class="col-md-10 mt-4">
                <h1>Sales Report</h1>
                <form method="get" class="mb-3">
                    <label for="sale_date">Select Date:</label>
                    <input type="date" id="sale_date" name="sale_date" value="<?php echo $date; ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <div class="table-responsive">
                    <a href="add_sale.php"><button style="float:right;" class="btn btn-primary mb-2">+ Add Sale</button></a>
                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?print=true&date=' . urlencode($date); ?>" target="_blank" class="btn btn-secondary mb-2" style="float:right; margin-right: 10px;">Print Report</a>
                    <table id="example1" class="table table-striped table-bordered mt-2">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Invoice</th>
                                <th scope="col">Drink</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Total</th>
                                <th scope="col">Payment</th>
                                <th scope="col">Change</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($saleList as $key => $pro) { ?>
                                <tr>
                                    <td><?php echo $key + 1 ?></td>
                                    <td><?php echo $pro['cus_id']; ?></td>
                                    <td><?php echo $pro['invoice_id']; ?></td>
                                    <td><?php echo $pro['drink_name']; ?></td>
                                    <td><?php echo $pro['qty']; ?></td>
                                    <td>$<?php echo $pro['total']; ?></td>
                                    <td>$<?php echo $pro['total_payment']; ?></td>
                                    <td>$<?php echo $pro['change']; ?></td>
                                    <td><?php echo $pro['sale_date']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"><strong>Total Sales for <?php echo $date; ?>:</strong></td>
                                <td colspan="4"><strong>$<?php echo number_format($totalSales, 2); ?></strong></td>
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
            var saleDate = document.getElementById('sale_date').value;
            window.location.href = '<?php echo $_SERVER['PHP_SELF'] . '?print=false'; ?>&sale_date=' + encodeURIComponent(saleDate);
        });
    });
    </script>
    <?php
}
?>
