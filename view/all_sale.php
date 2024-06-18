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

// Prepare Query for Sales
$statement = $pdo->prepare("SELECT sale.*, drink.name as drink_name, drink.id as drink_id
    FROM sale 
    INNER JOIN drink ON sale.drink_id = drink.id
    WHERE sale.status=1");
// Execute Query
$statement->execute();
// Get Data
$saleList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all drinks for the dropdown
$drinkStatement = $pdo->prepare("SELECT id, name, price FROM drink");
$drinkStatement->execute();
$drinkList = $drinkStatement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
   <?php include('header.php'); ?>
   <div class="row">
      <!-- Sidebar -->
      <?php include('sidebar_manager.php') ?>
      <!-- Main Content Area -->
      <div class="col-md-10 mt-4">
         <h1>All Sales</h1>
         <div class="table-responsive">
            <a href="add_sale.php"><button style="float:right;" class="btn btn-primary mb-2">+Add Sale</button></a>
            <table id="example1" class="table table-striped table-bordered mt-2"> <!-- Added table-bordered class -->
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
                  <?php foreach ($saleList as $key => $pro) { ?>
                     <tr>
                        <th scope="row"><?php echo $key + 1 ?></th>
                        <td><?php echo $pro['cus_id']; ?></td>
                        <td><?php echo $pro['invoice_id']; ?></td>
                        <td><?php echo $pro['drink_name']; ?></td>
                        <td><?php echo $pro['qty']; ?></td>
                        <td>$<?php echo $pro['total']; ?></td>
                        <td>$<?php echo $pro['total_payment']; ?></td>
                        <td>$<?php echo $pro['change']; ?></td>
                        <td><?php echo $pro['sale_date']; ?></td>
                        <td>
                           <div class="d-grid gap-2 d-md-block">
                              <!-- <button class="btn btn-danger" type="button" onclick="showEditSaleForm(
                                  '<?php //echo $pro['id']; ?>',
                                  '<?php //echo $pro['cus_id']; ?>',
                                  '<?php //echo $pro['invoice_id']; ?>',
                                  '<?php //echo $pro['drink_id']; ?>',
                                  '<?php //echo $pro['price']; ?>',
                                  '<?php //echo $pro['qty']; ?>',
                                  '<?php //echo $pro['total']; ?>',
                                  '<?php //echo $pro['total_payment']; ?>',
                                  '<?php //echo $pro['change']; ?>',
                                  '<?php //echo $pro['sale_date']; ?>'
                              )"><i class="fas fa-user-edit"></i></button> -->
                              <button class="btn btn-primary" type="button" onclick="showDeleteSaleForm('<?php echo $pro['id']; ?>')"><i class="fas fa-trash-alt"></i></button>
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
                    <input type="hidden" id="editSaleId" name="id"> <!-- Hidden field for sale ID -->
                    <div class="mb-3">
                        <label for="editSaleCustomerId" class="form-label">Customer ID</label>
                        <input type="text" class="form-control" id="editSaleCustomerId" name="cus_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleInvoiceId" class="form-label">Invoice ID</label>
                        <input type="text" class="form-control" id="editSaleInvoiceId" name="invoice_id">
                    </div>
                    <div class="mb-3">
                        <label for="editSaleDrinkId" class="form-label">Drink</label>
                        <select class="form-control" id="editSaleDrinkId" name="drink_id">
                            <?php foreach ($drinkList as $drink) { ?>
                                <option value="<?php echo $drink['id']; ?>" data-price="<?php echo $drink['price']; ?>"><?php echo $drink['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editSalePrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="editSalePrice" name="price" readonly>
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

<!-- Delete Sale Modal -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteSaleForm" action="javascript:void(0);" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSaleModalLabel">Delete Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteSaleId" name="delete_id"> <!-- Hidden field for sale ID -->
                    <p>Are you sure you want to delete this sale?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_sale">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showEditSaleForm(id, cus_id, invoice_id, drink_id, price, qty, total, total_payment, change, sale_date) {
    document.getElementById('editSaleId').value = id; // Set sale ID
    document.getElementById('editSaleCustomerId').value = cus_id;
    document.getElementById('editSaleInvoiceId').value = invoice_id;
    document.getElementById('editSaleDrinkId').value = drink_id;
    document.getElementById('editSalePrice').value = price;
    document.getElementById('editSaleQty').value = qty;
    document.getElementById('editSaleTotal').value = total;
    document.getElementById('editSaleTotalPayment').value = total_payment;
    document.getElementById('editSaleChange').value = change;
    document.getElementById('editSaleDate').value = sale_date;

    // Automatically update price based on selected drink
    var select = document.getElementById('editSaleDrinkId');
    var selectedOption = select.options[select.selectedIndex];
    var selectedPrice = selectedOption.getAttribute('data-price');
    document.getElementById('editSalePrice').value = selectedPrice;

    var myModal = new bootstrap.Modal(document.getElementById('editSaleModal'));
    myModal.show();
}

function showDeleteSaleForm(id) {
    document.getElementById('deleteSaleId').value = id; // Set sale ID

    var myModal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));
    myModal.show();
}

document.getElementById('editSaleForm').onsubmit = function() {
    var formData = new FormData(this);
    fetch('edit_sale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update sale.');
        }
    })
    .catch(error => console.error('Error:', error));
};

document.getElementById('deleteSaleForm').onsubmit = function() {
    var formData = new FormData(this);
    fetch('delete_sale.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to delete sale.');
        }
    })
    .catch(error => console.error('Error:', error));
};
</script>

<?php include('footer.php'); ?>

