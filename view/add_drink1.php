<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

$errors = [];
$name = "";
$price = "";
$description = "";
$category = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    require("../controller/connection.php");

    // Get data from form
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $category = $_POST["cat_id"];

    // Validation
    if (!$name) {
        $errors[] = "Name is required";
    }
    if (!$price) {
        $errors[] = "Price is required";
    }
    if (!$category) {
        $errors[] = "Category is required";
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
        $statement = $pdo->prepare("INSERT INTO drink(name, price, cat_id, description, image, status) VALUES(:name, :price, :cat_id, :description, :image, 1)");
        $statement->bindValue(':name', $name);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':cat_id', $category);
        $statement->bindValue(':image', $imagePath);

        if ($statement->execute()) {
            $_SESSION['success_message'] = "Drink added successfully";
            // Redirect to prevent form resubmission on refresh
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $errors[] = "Failed to add drink";
        }
    }
}

require("../controller/connection.php");
// Fetch categories from the database
$categories = []; // Initialize the array
$stmt = $pdo->query("SELECT id, name FROM category WHERE status=1");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[] = $row;
}
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_cashier.php') ?>
        <!-- Content -->
        <div class="col-xl-6 mt-4">
            <h2>Add New Drink</h2>
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php } ?>
            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>
            <form action="" method="post" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3 row">
                    <label for="name" class="form-label col-md-3">Drink Name </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="name" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter drink name" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="price" class="form-label col-md-3">Price </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="price" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>" placeholder="Enter price" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="cat_id" class="form-label col-md-3">Category</label>
                    <div class="col-md-9" style="width:60%;">
                        <select id="cat_id" class="form-control" name="cat_id">
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat) : ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="image" class="form-label col-md-3">Photo </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="file" id="image" class="form-control" name="image" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="description" class="form-label col-md-3">Description </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="description" class="form-control" name="description" value="<?php echo htmlspecialchars($description); ?>" placeholder="Enter description" />
                    </div>
                </div>
                <div class="col-md-9" style="width:60%;">
                    <button class="btn btn-success" style="float:left;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
