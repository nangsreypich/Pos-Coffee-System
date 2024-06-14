<?php include('head.php') ?>
<?php
require("../controller/connection.php");

// Fetch the details of the most recent sale
$stmt = $pdo->query("SELECT * FROM sale ORDER BY id DESC LIMIT 1");
$latestSale = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if sale exists
if ($latestSale) {
    $invoice_id = $latestSale['invoice_id'];
    // Fetch customer ID based on the invoice ID
    $stmt = $pdo->prepare("SELECT cus_id FROM sale WHERE invoice_id = ?");
    $stmt->execute([$invoice_id]);
    $customer_id = $stmt->fetchColumn();
} else {
    $invoice_id = "No sales found";
    $customer_id = "No sales found";
}

// Fetch sale details along with drink name from the drink table
$stmt = $pdo->prepare("SELECT s.*, d.name AS drink_name FROM sale s 
                        JOIN drink d ON s.drink_id = d.id 
                        WHERE invoice_id = ?");
$stmt->execute([$invoice_id]);
$orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

$shop_name = "Third Coffee Shop";
$logo_url = "../image/Logo_ASSIGMENT.jpg";
$current_date = date('Y-m-d');
?>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    .invoice {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .totals {
        margin-top: 20px;
    }

    /* Styles for printing */
    @media print {
        table {
            font-size: 12px;
        }

        th,
        td {
            padding: 6px;
        }

        body {
            margin: 20px;
        }

        .invoice {
            border: none;
            box-shadow: none;
            padding: 0;
        }

        .totals {
            border: none;
        }
    }

    /* Additional Styles */
    .header h1,
    .header img,
    .header p {
        display: inline;
        vertical-align: middle;
        margin: 0;
    }

    .header img {
        max-width: 50px;
        max-height: 50px;
        border-radius: 25px;
    }

    .header p {
        margin-left: 10px;
    }
</style>
<div class="invoice">
    <div class="header">
        <div style="text-align: center;">
        <img src="<?php echo $logo_url; ?>" alt="Shop Logo" style="max-width: 200px; display: inline; vertical-align: middle;">
        <h1 style="display: inline;"><?php echo $shop_name; ?></h1><br>
            <p style="display: inline; margin-left: 10px;">Date: <?php echo $current_date; ?></p>
        </div>
    </div>
    <div class="customer-details">
        <div style="float: left;">
            <p>Invoice ID: <?php echo $invoice_id; ?></p>
            <p>Customer ID: <?php echo $customer_id; ?></p>
        </div>
    </div>
    <div class="order-details">
        <table>
            <thead>
                <tr>
                    <th>Drink Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through order details and display -->
                <?php foreach ($orderDetails as $orderItem) : ?>
                    <tr>
                        <td><?php echo $orderItem['drink_name']; ?></td>
                        <td>$<?php echo $orderItem['price']; ?></td>
                        <td><?php echo $orderItem['qty']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="totals">
    <table>
        <tr>
            <td>Total:</td>
            <td>$<?php echo $latestSale['total']; ?></td>
        </tr>
        <tr>
            <td>Total Payment:</td>
            <td>$<?php echo $latestSale['total_payment']; ?></td>
        </tr>
        <tr>
            <td>Payment Type:</td>
            <td><?php echo $latestSale['payment_type']; ?></td>
        </tr>
        <tr>
            <td>Change:</td>
            <td>$<?php echo $latestSale['change']; ?></td>
        </tr>
    </table>
</div>