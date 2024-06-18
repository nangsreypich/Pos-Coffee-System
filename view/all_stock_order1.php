<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}
require("../controller/connection.php");

// Fetch all stock information with associated staff (stocker)
$statement = $pdo->prepare("
    SELECT 
        stock_order.*, 
        staff.name AS stocker_name,
        ingredient.product_name AS product_name
    FROM 
        stock_order
    LEFT JOIN 
        staff ON stock_order.stocker_id = staff.id
    LEFT JOIN 
        ingredient ON stock_order.product_id = ingredient.id 
    WHERE 
        stock_order.status = 1
");

$statement->execute();
$stockInList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all positions for the dropdown (if needed)
$positionStatement = $pdo->prepare("SELECT id, name FROM position");
$positionStatement->execute();
$positions = $positionStatement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all staff for the stocker dropdown
$staffStatement = $pdo->prepare("SELECT id, name FROM staff");
$staffStatement->execute();
$staffList = $staffStatement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all ingredients for the product dropdown
$ingredientStatement = $pdo->prepare("SELECT id, product_name as name FROM ingredient");
$ingredientStatement->execute();
$ingredientsList = $ingredientStatement->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include("head.php") ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <?php include('sidebar_stocker.php') ?>
        <div class="col-md-10 mt-4">
            <h1>All Stock</h1>
            <div id="success-message" class="alert alert-success" role="alert" style="display: none;"></div>

            <div class="table-responsive">
                <a href="stock_order.php"><button style="float:right;" class="btn btn-primary mb-2">+ Add Stock
                        </button></a>
                <table id="example1" class="table table-striped table-bordered mt-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Stocker Name</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Stock Date</th>
                            <th scope="col">Expired Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="stock-in-table-body">
                        <?php foreach ($stockInList as $key => $stockIn) { ?>
                            <tr id="stock-in-row-<?php echo $stockIn['id']; ?>">
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td class="stocker-name"><?php echo $stockIn['stocker_name']; ?></td>
                                <td class="product-name"><?php echo $stockIn['product_name']; ?></td>
                                <td class="quantity"><?php echo $stockIn['qty']; ?></td>
                                <td class="stock-date"><?php echo $stockIn['order_date']; ?></td>
                                <td class="expired-date"><?php echo $stockIn['expired_date']; ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-primary viewStockBtn"
                                            data-id="<?php echo $stockIn["id"] ?>"><i class="fas fa-eye"></i> 
                                        </button>
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

<!-- View Stock Modal -->
<div class="modal fade" id="viewStockModal" tabindex="-1" role="dialog" aria-labelledby="viewStockModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewStockModalLabel">View Stock</h5>
            </div>
            <div class="modal-body">
                <form id="viewStockForm">
                    <div class="form-group">
                        <label for="view_stocker_name">Stocker</label>
                        <input type="text" class="form-control" id="view_stocker_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="view_product_name">Product Name</label>
                        <input type="text" class="form-control" id="view_product_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="view_quantity">Quantity</label>
                        <input type="text" class="form-control" id="view_quantity" readonly>
                    </div>
                    <div class="form-group">
                        <label for="view_order_date">Order Date</label>
                        <input type="text" class="form-control" id="view_order_date" readonly>
                    </div>
                    <div class="form-group">
                        <label for="view_expired_date">Expired Date</label>
                        <input type="text" class="form-control" id="view_expired_date" readonly>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        // View Stock Modal
        $(".viewStockBtn").click(function() {
            var id = $(this).data("id");
            var stockerName = $(this).closest("tr").find(".stocker-name").text();
            var productName = $(this).closest("tr").find(".product-name").text();
            var quantity = $(this).closest("tr").find(".quantity").text();
            var stockDate = $(this).closest("tr").find(".stock-date").text();
            var expiredDate = $(this).closest("tr").find(".expired-date").text();

            $("#view_stocker_name").val(stockerName);
            $("#view_product_name").val(productName);
            $("#view_quantity").val(quantity);
            $("#view_order_date").val(stockDate);
            $("#view_expired_date").val(expiredDate);

            $("#viewStockModal").modal("show");
        });
    });
</script>


