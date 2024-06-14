<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: user/login.php");
    exit();
}

// Connection
include('../controller/connection.php');

// Prepare Query for staff details
$statement = $pdo->prepare("SELECT staff.*, position.name AS position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.status=1");

// Check for query execution errors
if (!$statement) {
    die('Error in preparing the query: ' . $pdo->errorInfo()[2]);
}

// Execute Query
if (!$statement->execute()) {
    die('Error in executing the query: ' . $statement->errorInfo()[2]);
}

// Fetch Data
$staffList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch total staff
$totalStaffStmt = $pdo->prepare("SELECT COUNT(*) AS total_staff FROM staff WHERE status=1");
$totalStaffStmt->execute();
$totalStaff = $totalStaffStmt->fetch(PDO::FETCH_ASSOC)['total_staff'];

// Fetch total drinks
$totalDrinksStmt = $pdo->prepare("SELECT COUNT(*) AS total_drinks FROM drink WHERE status=1");
$totalDrinksStmt->execute();
$totalDrinks = $totalDrinksStmt->fetch(PDO::FETCH_ASSOC)['total_drinks'];

// Fetch total sales and revenue for the current month
$totalSalesStmt = $pdo->prepare("SELECT COUNT(*) AS total_sales, SUM(total) AS total_revenue FROM sale WHERE MONTH(sale_date) = MONTH(CURRENT_DATE()) AND YEAR(sale_date) = YEAR(CURRENT_DATE())");
$totalSalesStmt->execute();
$totalSalesData = $totalSalesStmt->fetch(PDO::FETCH_ASSOC);
$totalSales = $totalSalesData['total_sales'];
$totalRevenue = $totalSalesData['total_revenue'];

// Fetch all sales data
$salesStmt = $pdo->prepare("SELECT * FROM sale");

$salesStmt->execute();
$salesList = $salesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>
<style>
    .p-3 {
        padding: 0.75rem;
    }

    .border.rounded {
        border-radius: 15px;
    }
</style>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Main Content Area -->
        <div id="mainContent" class="col-md-10 mt-4">
            <h1>Dashboard</h1>

            <div class="row mb-4">
                <div class="col-md-3 p-3">
                    <div class="border rounded p-3" style="background-color: #8c6803; color:#fff;">
                        <h4><i class="fas fa-users"></i> Total Staff</h4>
                        <p><?php echo $totalStaff; ?></p>
                    </div>
                </div>
                <div class="col-md-3 p-3">
                    <div class="border rounded p-3" style="background-color: #302503; color:#fff;">
                        <h4><i class="fas fa-coffee"></i> Total Drinks</h4>
                        <p><?php echo $totalDrinks; ?></p>
                    </div>
                </div>
                <div class="col-md-3 p-3">
                    <div class="border rounded p-3" style="background-color: #9e8337; color:#fff;">
                        <h4><i class="fas fa-chart-line"></i> Total Sales</h4>
                        <p><?php echo $totalSales; ?></p>
                    </div>
                </div>
                <div class="col-md-3 p-3">
                    <div class="border rounded p-3" style="background-color: #241b01; color:#fff;">
                        <h4><i class="fas fa-dollar-sign"></i> Total Revenue</h4>
                        <p>$<?php echo $totalRevenue; ?></p>
                    </div>
                </div>
            </div>


            <div class="table-responsive mt-4">
                <h2>All Sales</h2>
                <table id="salesTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesList as $key => $sale) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td><?php echo $sale['cus_id']; ?></td>
                                <td><?php echo $sale['total']; ?></td>
                                <td><?php echo $sale['sale_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include('footer.php') ?>
</div>