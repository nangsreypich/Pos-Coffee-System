<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

require("../controller/connection.php");

$errors = [];
$success = '';
$product_name = "";
$price = "";
$imagePath = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            $success = "Ingredient added successfully";
            // Clear form values
            $product_name = "";
            $price = "";
            $imagePath = "";
        } else {
            $errors[] = "Failed to add ingredient";
        }
    }
}

$statement = $pdo->prepare("SELECT * from ingredient WHERE ingredient.status=1");
$statement->execute();
$ingredientList = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include('head.php') ?>

<style>
    .container-fluid {
        padding: 20px;
    }

    .table-responsive {
        margin-top: 20px;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table tbody tr {
        line-height: 1.2;
    }

    .dataTables_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
        margin-bottom: 10px;
    }
</style>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <?php include('sidebar_stocker.php') ?>
        <div class="col-md-10 mt-4">
            <h2>Add New Ingredient</h2>
            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
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
            <h1 class="mt-5">All Ingredients</h1>
            <div class="table-responsive">
                <a href="#addIngredientForm"><button style="float:right;" class="btn btn-primary mb-2">+Add Ingredient</button></a>
                <table id="example1" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Image</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ingredientList as $key => $pro) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td><?php echo $pro['product_name']; ?></td>
                                <td>$<?php echo $pro['price']; ?></td>
                                <td><img src="<?php echo $pro['image'] ?? "../image/No_Image_Available.jpg" ?>" alt="" width="50px" height="70px"></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-danger" type="button" onclick="showEditForm(<?php echo $pro['id'] ?>, '<?php echo htmlspecialchars(addslashes($pro['product_name'])); ?>', '<?php echo $pro['price']; ?>', '<?php echo $pro['image']; ?>')"><i class="fas fa-user-edit"></i></button>
                                        <button class="btn btn-primary" type="button" onclick="showDeleteForm(<?php echo $pro['id'] ?>, '<?php echo htmlspecialchars(addslashes($pro['product_name'])); ?>')"><i class="fas fa-trash-alt"></i></button>
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

<!-- Edit Ingredient Modal -->
<div class="modal fade" id="editIngredientModal" tabindex="-1" aria-labelledby="editIngredientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editIngredientForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editIngredientModalLabel">Edit Ingredient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editIngredientId">
                    <div class="mb-3">
                        <label for="editIngredientName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editIngredientName" name="product_name">
                    </div>
                    <div class="mb-3">
                        <label for="editIngredientPrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="editIngredientPrice" name="price">
                    </div>
                    <div class="mb-3">
                        <label for="editIngredientImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editIngredientImage" name="image">
                        <input type="hidden" id="editIngredientOldImage" name="oldImage">
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

<!-- Delete Ingredient Modal -->
<div class="modal fade" id="deleteIngredientModal" tabindex="-1" aria-labelledby="deleteIngredientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteIngredientForm" action="javascript:void(0);" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteIngredientModalLabel">Delete Ingredient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="delete_id" id="deleteIngredientId">
                    <p>Are you sure you want to delete the ingredient <strong id="deleteIngredientName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_ingredient">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showEditForm(id, product_name, price, cat_id, image) {
        document.getElementById('editIngredientId').value = id;
        document.getElementById('editIngredientName').value = product_name;
        document.getElementById('editIngredientPrice').value = price;
        document.getElementById('editIngredientOldImage').value = image;

        var myModal = new bootstrap.Modal(document.getElementById('editIngredientModal'));
        myModal.show();
    }

    function showDeleteForm(id, product_name) {
        document.getElementById('deleteIngredientId').value = id;
        document.getElementById('deleteIngredientName').innerText = product_name;

        var myModal = new bootstrap.Modal(document.getElementById('deleteIngredientModal'));
        myModal.show();
    }

    $(document).ready(function() {
        $('#editIngredientForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: 'edit_ingredient.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $('#editIngredientModal').modal('hide');
                        alert('Ingredient details updated successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error: ' + result.error);
                    }
                },
                error: function() {
                    alert('Error updating ingredient details. Please try again.');
                }
            });
        });

        $('#deleteIngredientForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'delete_ingredient.php',
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $('#deleteIngredientModal').modal('hide');
                        alert('Ingredient deleted successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error: ' + result.error);
                    }
                },
                error: function() {
                    alert('Error deleting ingredient. Please try again.');
                }
            });
        });
    });
</script>

<?php include('footer.php') ?>
