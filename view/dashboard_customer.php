<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: user/login.php");
    exit();
}

// Include database connection
include('../controller/connection.php');

try {
    // Prepare Query for staff details
    $statement = $pdo->prepare("SELECT staff.*, position.name AS position_name 
                                FROM staff 
                                INNER JOIN position ON staff.pos_id = position.id 
                                WHERE staff.status = 1");

    if (!$statement) {
        throw new Exception('Error in preparing the query: ' . $pdo->errorInfo()[2]);
    }

    // Execute Query
    if (!$statement->execute()) {
        throw new Exception('Error in executing the query: ' . $statement->errorInfo()[2]);
    }

    // Fetch Data
    $staffList = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Fetch total staff
    $totalStaffStmt = $pdo->prepare("SELECT COUNT(*) AS total_staff FROM staff WHERE status = 1");
    $totalStaffStmt->execute();
    $totalStaff = $totalStaffStmt->fetch(PDO::FETCH_ASSOC)['total_staff'];

    // Fetch total drinks
    $totalDrinksStmt = $pdo->prepare("SELECT COUNT(DISTINCT drink_id) AS total_drinks 
                                      FROM orders 
                                      WHERE cus_id = (SELECT id FROM users WHERE username = :username)");
    $totalDrinksStmt->execute(array(':username' => $_SESSION['username']));
    $totalDrinks = $totalDrinksStmt->fetch(PDO::FETCH_ASSOC)['total_drinks'];

    // Fetch total orders
    $totalOrdersStmt = $pdo->prepare("SELECT COUNT(*) AS total_orders 
                                      FROM orders 
                                      WHERE status = 1 AND order_by = :username");
    $totalOrdersStmt->execute(array(':username' => $_SESSION['username']));
    $totalOrders = $totalOrdersStmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

    // Fetch total payment
    $totalPaymentStmt = $pdo->prepare("SELECT SUM(total_payment) AS total_payment 
                                       FROM orders 
                                       WHERE status = 1 AND order_by = :username");
    $totalPaymentStmt->execute(array(':username' => $_SESSION['username']));
    $totalPayment = $totalPaymentStmt->fetch(PDO::FETCH_ASSOC)['total_payment'];

    // Prepare Query to fetch sales details
    $saleStatement = $pdo->prepare("SELECT orders.*, drink.name AS drink_name 
                                    FROM orders 
                                    INNER JOIN drink ON orders.drink_id = drink.id 
                                    WHERE orders.status = 1 AND order_by = :username");
    $saleStatement->execute(array(':username' => $_SESSION['username']));
    $saleList = $saleStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php include('head.php'); ?>
<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_customer.php'); ?>
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
                        <h4><i class="fas fa-coffee"></i> Total Orders</h4>
                        <p><?php echo $totalOrders; ?></p>
                    </div>
                </div>
                <div class="col-md-3 p-3">
                    <div class="border rounded p-3" style="background-color: #9e8337; color:#fff;">
                        <h4><i class="fas fa-chart-line"></i> Total Payment</h4>
                        <p>$<?php echo $totalPayment; ?></p>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <h2>My Orders</h2>
                <table id="example1" class="table table-striped table-bordered mt-2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Customer ID</th>
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
                        <?php foreach ($saleList as $key => $pro) : ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1; ?></th>
                                <td><?php echo $pro['cus_id']; ?></td>
                                <td><?php echo $pro['invoice_id']; ?></td>
                                <td><?php echo $pro['drink_name']; ?></td>
                                <td><?php echo $pro['qty']; ?></td>
                                <td>$<?php echo $pro['total']; ?></td>
                                <td>$<?php echo $pro['total_payment']; ?></td>
                                <td>$<?php echo $pro['change']; ?></td>
                                <td><?php echo $pro['sale_date']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include('footer.php'); ?>
</div>
