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

// Handle date selection
$date = isset($_POST['sale_date']) ? $_POST['sale_date'] : date('Y-m-d');

// Prepare Query
$statement = $pdo->prepare("SELECT sale.*, coffee_table.name AS table_name, drink.name as drink_name 
    FROM sale 
    INNER JOIN coffee_table ON sale.table_id = coffee_table.id
    INNER JOIN drink ON sale.drink_id = drink.id
    WHERE sale.status=1 AND DATE(sale.sale_date) = :sale_date");
$statement->bindParam(':sale_date', $date);
$statement->execute();

// Get Data
$saleList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Calculate total sales for the selected date
$totalSales = 0;
foreach ($saleList as $sale) {
    $totalSales += $sale['total'];
}
?>

<?php include('head.php') ?>

<div class="container-fluid">
   <?php include('header.php'); ?>
   <div class="row">
      <!-- Sidebar -->
      <?php include('sidebar_manager.php') ?>
      <!-- Main Content Area -->
      <div class="col-md-10 mt-4">
         <h1>Sales Report</h1>
         <form method="post" class="mb-3">
            <label for="sale_date">Select Date:</label>
            <input type="date" id="sale_date" name="sale_date" value="<?php echo $date; ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
         </form>
         <div class="table-responsive">
            <a href="add_sale.php"><button style="float:right;" class="btn btn-primary mb-2">+ Add Sale</button></a>
            <button onclick="printReport()" class="btn btn-secondary mb-2" style="float:right; margin-right: 10px;">Print Report</button>
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
                     </tr>
                  <?php } ?>
               </tbody>
               <tfoot>
                  <tr>
                     <td colspan="6"><strong>Total Sales for <?php echo $date; ?>:</strong></td>
                     <td colspan="5"><strong>$<?php echo number_format($totalSales, 2); ?></strong></td>
                  </tr>
               </tfoot>
            </table>
         </div>
      </div>
   </div>
</div>


<script>
function printReport() {
    var printContents = document.querySelector('.container-fluid').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <div style="text-align:center;">
            <img src="../image/logo_ASSIGNMENT.jpg" alt="Logo" style="max-height: 100px;">
            <h1>Your Company Name</h1>
            <h3>Sales Report for <?php echo $date; ?></h3>
        </div>
        ${printContents}
    `;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();  // Reload the page to restore the original contents
}
</script>
