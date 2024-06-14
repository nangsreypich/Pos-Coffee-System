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
//Connection
require("../controller/connection.php");
//Prepare Query
$statement = $pdo->prepare("SELECT sale.*, coffee_table.name AS table_name, drink.name as drink_name 
    FROM sale 
    INNER JOIN coffee_table ON sale.table_id = coffee_table.id
    INNER JOIN drink ON sale.drink_id = drink.id
    WHERE sale.status=1");
//Execute Query
$statement->execute();
//Get Data
$saleList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
   <?php include('header.php'); ?>
   <div class="row">
      <!-- Sidebar -->
      <?php include('sidebar_manager.php') ?>
      <!-- Main Content Area -->
      <div class="col-md-10 mt-4">
         <h1>All Sale</h1>
         <div class="table-responsive">
            <a href="add_sale.php"><button style="float:right;" class="btn btn-primary mb-2">+Add Sale</button></a>
            <table id="example1" class="table table-striped table-bordered mt-2"> <!-- Added table-bordered class -->
               <thead>
                  <tr>
                     <th scope="col">#</th>
                     <th scope="col">Customer ID</th>
                     <th scope="col">Invoice</th>
                     <th scope="col">Table</th>
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
                  <?php foreach ($saleList as $key => $pro) { ?>
                     <tr>
                        <th scope="row"><?php echo $key + 1 ?></th>
                        <td><?php echo $pro['cus_id']; ?></td>
                        <td><?php echo $pro['invoice_id']; ?></td>
                        <td><?php echo $pro['table_name']; ?></td>
                        <td><?php echo $pro['drink_name']; ?></td>
                        <td><?php echo $pro['qty']; ?></td>
                        <td>$<?php echo $pro['total']; ?></td>
                        <td>$<?php echo $pro['total_payment']; ?></td>
                        <td>$<?php echo $pro['change']; ?></td>
                        <td><?php echo $pro['sale_date']; ?></td>
                        <td>
                           <div class="d-grid gap-2 d-md-block">
                              <a class="btn btn-danger" type="button" href="edit_sale.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-user-edit"></i></a>
                              <a class="btn btn-primary" type="button" href="delete_sale.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-trash-alt"></i></a>
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

<!-- Edit Sale Modal -->
<div class="modal fade" id="editSaleModal" tabindex="-1" aria-labelledby="editSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSaleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSaleModalLabel">Edit Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="table_id" id="editSaleTableId">
                    <div class="mb-3">
                        <label for="editSaleCustomerId" class="form-label">Customer ID</label>
                        <input type="text" class="form-control" id="editSaleCustomerId" name="cus_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleInvoiceId" class="form-label">Invoice ID</label>
                        <input type="text" class="form-control" id="editSaleInvoiceId" name="invoice_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleCategoryId" class="form-label">Category ID</label>
                        <input type="text" class="form-control" id="editSaleCategoryId" name="cat_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleDrinkId" class="form-label">Drink ID</label>
                        <input type="text" class="form-control" id="editSaleDrinkId" name="drink_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSalePrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="editSalePrice" name="price">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleQty" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="editSaleQty" name="qty">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleTotal" class="form-label">Total</label>
                        <input type="text" class="form-control" id="editSaleTotal" name="total">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleTotalPayment" class="form-label">Total Payment</label>
                        <input type="text" class="form-control" id="editSaleTotalPayment" name="total_payment">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleChange" class="form-label">Change</label>
                        <input type="text" class="form-control" id="editSaleChange" name="change">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleDate" class="form-label">Sale Date</label>
                        <input type="text" class="form-control" id="editSaleDate" name="sale_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Drink Modal -->
<div class="modal fade" id="deleteDrinkModal" tabindex="-1" aria-labelledby="deleteDrinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteDrinkForm" action="javascript:void(0);" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDrinkModalLabel">Delete Drink</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="delete_id" id="deleteDrinkId">
                    <p>Are you sure you want to delete the drink <strong id="deleteDrinkName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_drink">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
function showEditSaleForm(table_id, cus_id, invoice_id, cat_id, drink_id, price, qty, total, total_payment, change, sale_date) {
    document.getElementById('editSaleTableId').value = table_id;
    document.getElementById('editSaleCustomerId').value = cus_id;
    document.getElementById('editSaleInvoiceId').value = invoice_id;
    document.getElementById('editSaleCategoryId').value = cat_id;
    document.getElementById('editSaleDrinkId').value = drink_id;
    document.getElementById('editSalePrice').value = price;
    document.getElementById('editSaleQty').value = qty;
    document.getElementById('editSaleTotal').value = total;
    document.getElementById('editSaleTotalPayment').value = total_payment;
    document.getElementById('editSaleChange').value = change;
    document.getElementById('editSaleDate').value = sale_date;

    var myModal = new bootstrap.Modal(document.getElementById('editSaleModal'));
    myModal.show();
}

function showDeleteSaleForm(table_id, cus_id, invoice_id, cat_id, drink_id, price, qty, total, total_payment, change, sale_date) {
    document.getElementById('deleteSaleTableId').value = table_id;
    document.getElementById('deleteSaleCustomerId').value = cus_id;
    document.getElementById('deleteSaleInvoiceId').value = invoice_id;
    document.getElementById('deleteSaleCategoryId').value = cat_id;
    document.getElementById('deleteSaleDrinkId').value = drink_id;
    document.getElementById('deleteSalePrice').value = price;
    document.getElementById('deleteSaleQty').value = qty;
    document.getElementById('deleteSaleTotal').value = total;
    document.getElementById('deleteSaleTotalPayment').value = total_payment;
    document.getElementById('deleteSaleChange').value = change;
    document.getElementById('deleteSaleDate').value = sale_date;

    var myModal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));
    myModal.show();
}

$(document).ready(function() {
    $('#editSaleForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'edit_sale.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    $('#editSaleModal').modal('hide');
                    alert('Sale details updated successfully!');
                    location.reload();  // Reload the page to reflect changes
                } else {
                    alert('Error: ' + result.error);
                }
            },
            error: function() {
                alert('Error updating sale details. Please try again.');
            }
        });
    });

    $('#deleteSaleForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'delete_sale.php',
            data: $(this).serialize(),
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    $('#deleteSaleModal').modal('hide');
                    alert('Sale deleted successfully!');
                    location.reload();  // Reload the page to reflect changes
                } else {
                    alert('Error: ' + result.error);
                }
            },
            error: function() {
                alert('Error deleting sale. Please try again.');
            }
        });
    });
});
</script>

