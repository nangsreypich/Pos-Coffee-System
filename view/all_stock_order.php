<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}
require ("../controller/connection.php");

// Fetch all stock in information with associated staff (stocker)
$statement = $pdo->prepare("
    SELECT 
        stock_order.*, 
        staff.name AS stocker_name,
        ingredient.product_name AS product_name,
        position.name AS position_name
    FROM 
        stock_order
    LEFT JOIN 
        staff ON stock_order.stocker_id = staff.id
    LEFT JOIN 
        position ON staff.pos_id = position.id
    LEFT JOIN
        ingredient ON stock_order.product_id = ingredient.id WHERE stock_order.status=1
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

<?php include ('head.php') ?>

<div class="container-fluid">
    <?php include ('header.php'); ?>
    <div class="row">
        <?php include ('sidebar_manager.php') ?>
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
                            <th scope="col">Product ID</th>
                            <!--<th scope="col">Price</th>-->
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
                                <td class="stocker-name" data-stocker-id="<?php echo $stockIn['stocker_id']; ?>"><?php echo $stockIn['stocker_name']; ?></td>
                                <td class="product-name" data-product-id="<?php echo $stockIn['product_id']; ?>"><?php echo $stockIn['product_name']; ?></td>
                                <!--<td class="price">$<?php //echo number_format($stockIn['price'], 2); ?></td>-->
                                <td class="quantity"><?php echo $stockIn['qty']; ?></td>
                                <td class="stock-date"><?php echo $stockIn['order_date']; ?></td>
                                <td class="expired-date"><?php echo $stockIn['expired_date']; ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-primary editStockInBtn"
                                            data-id="<?php echo $stockIn["id"] ?>"><i class="fas fa-edit"></i> </button>
                                        <button class="btn btn-danger deleteStockInBtn"
                                            data-id="<?php echo $stockIn["id"] ?>"><i class="fas fa-trash-alt"></i>
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

<!-- Edit Stock In Modal -->
<div class="modal fade" id="editStockInModal" tabindex="-1" role="dialog" aria-labelledby="editStockInModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockInModalLabel">Edit Stock</h5>
            </div>
            <div class="modal-body">
                <form id="editStockInForm" method="post">
                    <div class="form-group">
                        <label for="edit_stocker_id">Stocker</label>
                        <select class="form-control" id="edit_stocker_id" name="stocker_id" required>
                            <?php foreach ($staffList as $staff) { ?>
                                <option value="<?php echo $staff['id']; ?>" <?php echo ($stockIn['stocker_id'] == $staff['id']) ? 'selected' : ''; ?>>
                                    <?php echo $staff['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_id">Ingredient</label>
                        <select class="form-control" id="edit_product_id" name="product_id" required>
                            <?php foreach ($ingredientsList as $ingredient) { ?>
                                <option value="<?php echo $ingredient['id']; ?>" <?php echo ($stockIn['product_id'] == $ingredient['id']) ? 'selected' : ''; ?>>
                                    <?php echo $ingredient['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_price">Price</label>
                        <input type="text" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_quantity">Quantity</label>
                        <input type="text" class="form-control" id="edit_quantity" name="qty" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_order_date">Order Date</label>
                        <input type="date" id="edit_order_date" class="form-control" name="order_date" required />
                    </div>
                    <div class="form-group">
                        <label for="edit_expired_date">Expired Date</label>
                        <input type="date" id="edit_expired_date" class="form-control" name="expired_date" required />
                    </div>
                    <input type="hidden" id="edit_stock_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Stock In Modal -->
<div class="modal fade" id="deleteStockInModal" tabindex="-1" role="dialog" aria-labelledby="deleteStockInModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStockInModalLabel">Confirm Delete Stock In</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this stock in entry?</p>
                <form id="deleteStockInForm" method="post">
                    <input type="hidden" id="delete_stock_order_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mt-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit Stock In Modal
        $(".editStockInBtn").click(function() {
            var id = $(this).data("id");
            var stockerId = $(this).closest("tr").find(".stocker-name").data("stocker-id");
            var productId = $(this).closest("tr").find(".product-name").data("product-id");
            var price = $(this).closest("tr").find(".price").text();
            var quantity = $(this).closest("tr").find(".quantity").text();
            var stockDate = $(this).closest("tr").find(".stock-date").text();
            var stockStatus = $(this).closest("tr").find(".stock-status").text();
            var expiredDate = $(this).closest("tr").find(".expired-date").text();

            $("#edit_stock_id").val(id);
            $("#edit_price").val(price);
            $("#edit_quantity").val(quantity);
            $("#edit_order_date").val(stockDate);
            $("#edit_expired_date").val(expiredDate);
            
            $("#edit_stocker_id").val(stockerId);
            $("#edit_product_id").val(productId);

            $("#editStockInModal").modal("show");
        });

        // Submit Edit Stock In Form
        $("#editStockInForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'edit_stock_order.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var id = $("#edit_stock_id").val();
                        var newStockerId = response.stocker_id;
                        var newProductId = response.product_id;
                        var newPrice = response.price;
                        var newQuantity = response.qty; // Use 'qty' instead of 'quantity'
                        var newStockDate = response.order_date; // Check if 'order_date' is returned correctly
                        var newExpiredDate = response.expired_date;

                        $("#stock-in-row-" + id).find(".stocker-name").text(newStockerId);
                        $("#stock-in-row-" + id).find(".product-name").text(newProductId);
                        $("#stock-in-row-" + id).find(".price").text(newPrice);
                        $("#stock-in-row-" + id).find(".quantity").text(newQuantity);
                        $("#stock-in-row-" + id).find(".stock-date").text(newStockDate);
                        $("#stock-in-row-" + id).find(".stock-status").text(newStockStatus);
                        $("#stock-in-row-" + id).find(".expired-date").text(newExpiredDate);

                        $("#editStockInModal").modal("hide");
                        $("#success-message").text("Stock updated successfully").show().delay(3000).fadeOut();
                    } else {
                        alert("Failed to update stock: " + response.errors.join(', '));
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    alert("Failed to update stock");
                }
            });
        });
    
        // Submit Delete Stock In Form
        $("#deleteStockInForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'delete_stock_order.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data) {
                    var id = form.find('#delete_stock_order_id').val();
                    $("#stock-in-row-" + id).remove();
                    $("#deleteStockInModal").modal("hide");
                    alert("Stock in entry deleted successfully");
                },
                error: function(data) {
                    console.log('Error:', data);
                    alert("Failed to delete stock in entry");
                }
            });
        });
    });
</script>
