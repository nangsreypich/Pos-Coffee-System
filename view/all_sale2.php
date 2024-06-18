<?php
// all_sales.php

// Ensure error reporting is enabled for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and check if user is logged in
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

// Include database connection
require("../controller/connection.php");

// Handle AJAX request to fetch sale details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fetch_sale' && isset($_POST['id'])) {
        $saleId = $_POST['id'];

        // Prepare SQL query to fetch sale details
        $statement = $pdo->prepare("SELECT orders.*, drink.name as drink_name 
                                    FROM orders
                                    INNER JOIN drink ON orders.drink_id = drink.id
                                    WHERE orders.id = :saleId");

        // Bind parameter
        $statement->bindParam(':saleId', $saleId, PDO::PARAM_INT);
        
        // Execute query
        $statement->execute();

        // Fetch sale details
        $sale = $statement->fetch(PDO::FETCH_ASSOC);

        // Return JSON response
        echo json_encode($sale);
        exit();
    }
}

// Prepare SQL query to fetch all sales data for the logged-in user
$statement = $pdo->prepare("SELECT orders.*, drink.name as drink_name 
                            FROM orders
                            INNER JOIN drink ON orders.drink_id = drink.id
                            WHERE orders.status = 1 AND orders.order_by = :username");

// Bind parameter
$statement->bindParam(':username', $_SESSION["username"], PDO::PARAM_STR);

// Execute query
$statement->execute();

// Fetch all sale data
$saleList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_customer.php') ?>
        <!-- Main Content Area -->
        <div class="col-md-10 mt-4">
            <h1>All Orders</h1>
            <div class="table-responsive">
                <a href="add_sale.php"><button style="float:right;" class="btn btn-primary mb-2">+ Add Sale</button></a>
                <table id="example1" class="table table-striped table-bordered mt-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Customer ID</th>
                            <th scope="col">Invoice</th>
                            <th scope="col">Drink</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Total</th>
                            <th scope="col">Total Payment</th>
                            <th scope="col">Change</th>
                            <th scope="col">Sale Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saleList as $key => $sale) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td><?php echo $sale['cus_id']; ?></td>
                                <td><?php echo $sale['invoice_id']; ?></td>
                                <td><?php echo $sale['drink_name']; ?></td>
                                <td><?php echo $sale['qty']; ?></td>
                                <td>$<?php echo $sale['total']; ?></td>
                                <td>$<?php echo $sale['total_payment']; ?></td>
                                <td>$<?php echo $sale['change']; ?></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-primary viewBtn" type="button" data-id="<?php echo $sale["id"]; ?>"><i class="fas fa-eye"></i> </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Sale Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSaleModalLabel">View Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="viewSaleCustomerId" class="form-label">Customer ID</label>
                    <input type="text" class="form-control" id="viewSaleCustomerId" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleInvoiceId" class="form-label">Invoice ID</label>
                    <input type="text" class="form-control" id="viewSaleInvoiceId" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleDrinkName" class="form-label">Drink Name</label>
                    <input type="text" class="form-control" id="viewSaleDrinkName" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleQty" class="form-label">Quantity</label>
                    <input type="text" class="form-control" id="viewSaleQty" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleTotal" class="form-label">Total</label>
                    <input type="text" class="form-control" id="viewSaleTotal" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleTotalPayment" class="form-label">Total Payment</label>
                    <input type="text" class="form-control" id="viewSaleTotalPayment" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleChange" class="form-label">Change</label>
                    <input type="text" class="form-control" id="viewSaleChange" readonly>
                </div>
                <div class="mb-3">
                    <label for="viewSaleDate" class="form-label">Sale Date</label>
                    <input type="text" class="form-control" id="viewSaleDate" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- JavaScript to handle AJAX request and populate modal -->
<script>
    $(document).ready(function() {
        $('.viewBtn').click(function() {
            var saleId = $(this).data('id');

            // AJAX request to fetch sale details
            $.ajax({
                url: 'all_sale2.php',
                method: 'post',
                data: { action: 'fetch_sale', id: saleId },
                dataType: 'json',
                success: function(response) {
                    // Populate modal with fetched data
                    $('#viewSaleModal #viewSaleCustomerId').val(response.cus_id);
                    $('#viewSaleModal #viewSaleInvoiceId').val(response.invoice_id);
                    $('#viewSaleModal #viewSaleDrinkName').val(response.drink_name);
                    $('#viewSaleModal #viewSaleQty').val(response.qty);
                    $('#viewSaleModal #viewSaleTotal').val('$' + response.total);
                    $('#viewSaleModal #viewSaleTotalPayment').val('$' + response.total_payment);
                    $('#viewSaleModal #viewSaleChange').val('$' + response.change);
                    $('#viewSaleModal #viewSaleDate').val(response.sale_date);
                    
                    // Show the modal
                    $('#viewSaleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Error occurred while fetching sale details.');
                }
            });
        });
    });
</script>
