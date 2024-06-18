<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

$errors = [];
$success = '';
$product_name = "";
$price = "";
$imagePath = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    require("../controller/connection.php");
// Get data from form
$product_name = $_POST["product_name"];
$price = $_POST["price"];

// Validation
if (!$product_name) {
    $errors[] = "Product name is required";
}
if (!$price) {
    $errors[] = "Price is required";
}

    if (empty($errors)) {
        require_once("globalFunction.php");
        // Upload Picture
        $image = $_FILES['image'] ?? null;
        $imagePath = "";
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $imagePath = "../image/" . date("YmdHis") . '_' . basename($image['name']);
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        // Prepare and execute statement
        $statement = $pdo->prepare("INSERT INTO ingredient(product_name, price, image, status) VALUES(:product_name, :price, :image, 1)");
        $statement->bindValue(':product_name', $product_name);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':image', $imagePath);

        if ($statement->execute()) {
            $_SESSION['success_message'] = "Ingredient added successfully";
            // Redirect to prevent form resubmission on refresh
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $errors[] = "Failed to ingredient drink";
        }
    }
}

require("../controller/connection.php");
// Fetch categories from the database
$categories = []; // Initialize the array
$statement = $pdo->prepare("SELECT * from ingredient WHERE status=1");
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-xl-6 mt-4">
            <h2>Add New Ingredient</h2>
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>
            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form action="" method="post" enctype="multipart/form-data" class="mt-3">
            <div class="mb-3 row">
                    <label for="product_name" class="form-label col-md-3">Product Name</label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="product_name" class="form-control" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" placeholder="Enter product name" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="price" class="form-label col-md-3">Price</label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="price" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>" placeholder="Enter price" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="image" class="form-label col-md-3">Photo</label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="file" id="image" class="form-control" name="image" />
                    </div>
                </div>
                <div class="col-md-9" style="width:60%;">
                    <button class="btn btn-success mb-4" style="float:left;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
