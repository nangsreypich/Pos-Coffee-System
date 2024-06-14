<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}

require("../controller/connection.php");

$error = [];
$success = '';

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
        <?php include('sidebar_manager.php') ?>
        <div class="col-md-10 mt-4">
            <h1>All Ingredients</h1>
            <div class="table-responsive">
                <a href="ingredients.php"><button style="float:right;" class="btn btn-primary mb-2">+Add Ingredient</button></a>
                <?php if ($success) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php foreach ($error as $err) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $err; ?>
                    </div>
                <?php endforeach; ?>
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
