<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}

require("../controller/connection.php");

$error = [];
$success = '';

$statement = $pdo->prepare("SELECT drink.*, category.name AS category_name FROM drink INNER JOIN category ON drink.cat_id = category.id WHERE drink.status=1");
$statement->execute();
$drinkList = $statement->fetchAll(PDO::FETCH_ASSOC);

$positionsStatement = $pdo->query("SELECT * FROM category WHERE category.status=1");
$positions = $positionsStatement->fetchAll(PDO::FETCH_ASSOC);
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
            <h1>All Drinks</h1>
            <div class="table-responsive">
                <a href="add_drink.php"><button style="float:right;" class="btn btn-primary mb-2">+Add Drink</button></a>
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
                <table id="drinkTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Drink Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drinkList as $key => $pro) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td><?php echo $pro['name']; ?></td>
                                <td>$<?php echo $pro['price']; ?></td>
                                <td><?php echo $pro['category_name']; ?></td>
                                <td><?php echo $pro['description']; ?></td>
                                <td><img src="<?php echo $pro['image'] ?? "../image/No_Image_Available.jpg" ?>" alt="" width="50px" height="70px"></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-danger" type="button" onclick="showEditForm(<?php echo $pro['id'] ?>, '<?php echo htmlspecialchars(addslashes($pro['name'])); ?>', '<?php echo $pro['price']; ?>', '<?php echo $pro['cat_id']; ?>', '<?php echo htmlspecialchars(addslashes($pro['description'])); ?>', '<?php echo $pro['image']; ?>')"><i class="fas fa-user-edit"></i></button>
                                        <button class="btn btn-primary" type="button" onclick="showDeleteForm(<?php echo $pro['id'] ?>, '<?php echo htmlspecialchars(addslashes($pro['name'])); ?>')"><i class="fas fa-trash-alt"></i></button>
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

<!-- Edit Drink Modal -->
<div class="modal fade" id="editDrinkModal" tabindex="-1" aria-labelledby="editDrinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDrinkForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDrinkModalLabel">Edit Drink</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editDrinkId">
                    <div class="mb-3">
                        <label for="editDrinkName" class="form-label">Drink Name</label>
                        <input type="text" class="form-control" id="editDrinkName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="editDrinkPrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="editDrinkPrice" name="price">
                    </div>
                    <div class="mb-3">
                        <label for="editDrinkCategory" class="form-label">Category</label>
                        <select class="form-control" id="editDrinkCategory" name="cat_id">
                            <?php foreach ($positions as $category) : ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDrinkDescription" class="form-label">Description</label>
                        <input type="text" class="form-control" id="editDrinkDescription" name="description">
                    </div>
                    <div class="mb-3">
                        <label for="editDrinkImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="editDrinkImage" name="image">
                        <input type="hidden" id="editDrinkOldImage" name="oldImage">
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

<!-- Delete Drink Modal -->
<div class="modal fade" id="deleteDrinkModal" tabindex="-1" aria-labelledby="deleteDrinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteDrinkForm" action="javascript:void(0);" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDrinkModalLabel">Delete Drink</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="delete_id" id="deleteDrinkId">
                    <p>Are you sure you want to delete the drink <strong id="deleteDrinkName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_drink">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showEditForm(id, name, price, cat_id, description, image) {
        document.getElementById('editDrinkId').value = id;
        document.getElementById('editDrinkName').value = name;
        document.getElementById('editDrinkPrice').value = price;
        document.getElementById('editDrinkCategory').value = cat_id;
        document.getElementById('editDrinkDescription').value = description;
        document.getElementById('editDrinkOldImage').value = image;

        var myModal = new bootstrap.Modal(document.getElementById('editDrinkModal'));
        myModal.show();
    }

    function showDeleteForm(id, name) {
        document.getElementById('deleteDrinkId').value = id;
        document.getElementById('deleteDrinkName').innerText = name;

        var myModal = new bootstrap.Modal(document.getElementById('deleteDrinkModal'));
        myModal.show();
    }

    $(document).ready(function() {
    $('#editDrinkForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: 'edit_drink.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    $('#editDrinkModal').modal('hide');
                    alert('Drink details updated successfully!');
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Error: ' + result.error);
                }
            },
            error: function() {
                alert('Error updating drink details. Please try again.');
            }
        });
    });

        $('#deleteDrinkForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'delete_drink.php',
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $('#deleteDrinkModal').modal('hide');
                        alert('Drink deleted successfully!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Error: ' + result.error);
                    }
                },
                error: function() {
                    alert('Error deleting drink. Please try again.');
                }
            });
        });
    });
</script>

<?php include('footer.php') ?>