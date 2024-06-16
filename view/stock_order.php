<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require("../controller/connection.php");
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}

$errors = [];
$success = "";
$total_price = 0;

// Check if the form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $product_id = $_POST["product_id"];
    $qty = $_POST["qty"];
    $expired_date = $_POST["expired_date"];
    $order_date = $_POST["order_date"];
    $stocker_id = $_POST["stocker_id"];

    // Validation
    if (!$product_id) {
        $errors[] = "Product ID is required";
    }
    // Add more validation rules as needed

    // Fetch product price from database based on product_id
    $stmt_product = $pdo->prepare("SELECT price FROM ingredient WHERE id = :product_id AND status = 1");
    $stmt_product->bindParam(':product_id', $product_id);
    $stmt_product->execute();
    $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $errors[] = "Product not found or inactive";
    } else {
        $price = $product['price'];
    }

    // Calculate total price
    $total_price = $price * $qty;

    // If there are no validation errors, proceed with inserting into the database
    if (empty($errors)) {
        try {
            // Prepare the INSERT statement
            $stmt = $pdo->prepare("INSERT INTO stock_order (stocker_id, product_id, price, qty, order_date, expired_date, status) 
                                VALUES (:stocker_id, :product_id, :price, :qty, :order_date, :expired_date, 1)");

            // Bind parameters
            $stmt->bindParam(':stocker_id', $stocker_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':price', $total_price); // Use the product price fetched earlier
            $stmt->bindParam(':qty', $qty);
            $stmt->bindParam(':order_date', $order_date);
            $stmt->bindParam(':expired_date', $expired_date);

            // Execute the statement
            if ($stmt->execute()) {
                $success = "Stock information added successfully";
            } else {
                $errors[] = "Failed to add stock information";
            }
        } catch (PDOException $e) {
            // Handle database errors
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch product IDs and names from the ingredient table where status is 1
$products = [];
$stmt_products = $pdo->query("SELECT id, product_name, price FROM ingredient WHERE status = 1");
while ($row = $stmt_products->fetch(PDO::FETCH_ASSOC)) {
    $products[] = $row;
}

$stockers = [];
$stmt_stockers = $pdo->query("SELECT staff.id, staff.name 
FROM staff 
JOIN position ON staff.pos_id = position.id 
WHERE position.name = 'Stocker'");
while ($row = $stmt_stockers->fetch(PDO::FETCH_ASSOC)) {
    $stockers[] = $row;
}

?>
<?php include('head.php'); ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php'); ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h2>Add Stock</h2>
            <!-- Show error -->
            <?php require('alert.php'); ?>

            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="post" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3 row">
                    <label for="product_id" class="form-label col-md-3">Product Name</label>
                    <div class="col-md-9" style="width: 60%;">
                        <select id="product_id" class="form-control" name="product_id" onchange="updatePrice()">
                            <option value="">Select product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="qty" class="form-label col-md-3">Quantity</label>
                    <div class="col-md-9" style="width: 60%;">
                        <input type="text" id="qty" class="form-control" name="qty" placeholder="Enter quantity" oninput="updatePrice()" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="price" class="form-label col-md-3">Price</label>
                    <div class="col-md-9" style="width: 60%;">
                        <input type="text" id="price" class="form-control" name="price" value="<?php echo $total_price; ?>" readonly />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="expired_date" class="form-label col-md-3">Expired Date</label>
                    <div class="col-md-9" style="width: 60%;">
                        <input type="date" id="expired_date" class="form-control" name="expired_date" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="order_date" class="form-label col-md-3">Order Date</label>
                    <div class="col-md-9" style="width: 60%;">
                        <input type="date" id="order_date" class="form-control" name="order_date" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="stocker_id" class="form-label col-md-3">Stocker</label>
                    <div class="col-md-9" style="width: 60%;">
                        <select id="stocker_id" class="form-control" name="stocker_id">
                            <option value="">Select stocker</option>
                            <?php foreach ($stockers as $stocker): ?>
                                <option value="<?php echo htmlspecialchars($stocker['id']); ?>">
                                    <?php echo htmlspecialchars($stocker['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-9" style="width: 60%;">
                    <button type="submit" class="btn btn-success" style="float: left;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to update price based on quantity and selected product's price
    function updatePrice() {
        var product_id = document.getElementById('product_id').value;
        var qty = document.getElementById('qty').value;

        // Fetch price dynamically via JavaScript from $products array
        var products = <?php echo json_encode($products); ?>;
        var price = 0;

        // Find the selected product's price
        for (var i = 0; i < products.length; i++) {
            if (products[i].id == product_id) {
                price = parseFloat(products[i].price);
                break;
            }
        }

        var total_price = price * parseInt(qty);
        if (!isNaN(total_price)) {
            document.getElementById('price').value = total_price.toFixed(2);
        }
    }
</script>

<?php include('footer.php'); ?>