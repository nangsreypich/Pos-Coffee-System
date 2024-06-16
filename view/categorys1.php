<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

$errors = [];
$success = "";
$name = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    require ("../controller/connection.php");

    // Get data from form
    $name = $_POST["name"];
    $operation = $_POST["operation"];

    // Validation
    if (!$name) {
        $errors[] = "Name is required";
    }

    if (empty($errors)) {
        if ($operation == "insert") {
            // Check if the category name already exists
            $statement = $pdo->prepare("SELECT COUNT(*) FROM category WHERE name = :name AND status=1");
            $statement->bindValue(':name', $name);
            $statement->execute();
            $count = $statement->fetchColumn();

            if ($count > 0) {
                $errors[] = "Category name already exists";
            } else {
                // Insert category
                $statement = $pdo->prepare("INSERT INTO category (name) VALUES (:name)");
                $statement->bindValue(':name', $name);
                if ($statement->execute()) {
                    $success = "Category added successfully";
                    // Clear the form data
                    $name = "";
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit();
                } else {
                    $errors[] = "Failed to add category";
                }
            }
        } elseif ($operation == "update") {
            $id = $_POST["id"];
            // Update category
            $statement = $pdo->prepare("UPDATE category SET name = :name WHERE id = :id");
            $statement->bindValue(':name', $name);
            $statement->bindValue(':id', $id);
            if ($statement->execute()) {
                $success = "Category updated successfully";
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit();
            } else {
                $errors[] = "Failed to update category";
            }
        }
    }
}

// Fetch all categorys from the database
require ("../controller/connection.php");
$statement = $pdo->prepare("SELECT * from category where category.status=1");
//Execute Query
$statement->execute();
//Get Data
$categoryList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include ('head.php') ?>

<div class="container-fluid">
    <?php include ('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include ('sidebar_cashier.php') ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h2>Add New Category</h2>
            <!-- Show errors -->
            <?php require ('alert.php') ?>

            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form id="addCategoryForm" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="mt-2">Category Name</label>
                    <input type="text" class="form-control mt-3" placeholder="Enter category name" name="name" id="name"
                           autocomplete="off" style="width:50%;" value="<?php echo $name; ?>">
                </div>
                <input type="hidden" name="operation" value="insert">
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

            <h2 class="mt-5">All Categorys</h2>
            <?php if (!empty($categoryList)) { ?>
                <table id="example1" class="table table-striped table-bordered mt-3">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categoryList as $key => $pro) { ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1 ?></th>
                            <td><?php echo htmlspecialchars($pro['name']); ?></td>
                            <td>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-primary editCategoryBtn" data-id="<?php echo $pro["id"] ?>"
                                            data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i
                                            class="fas fa-user-edit"></i></button>
                                    <button class="btn btn-danger deleteCategoryBtn" data-id="<?php echo $pro["id"] ?>"
                                            data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i
                                            class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <?php include ('footer.php') ?>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm" method="post">
                    <div class="form-group">
                        <label for="edit_category_name">Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="name" required>
                    </div>
                    <input type="hidden" id="edit_category_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Delete Category</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete category <span id="delete_category_name"></span>?</p>
                <form id="deleteCategoryForm" method="post">
                    <input type="hidden" id="delete_category_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mt-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Edit Category Modal
        $(".editCategoryBtn").click(function () {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#edit_category_id").val(id);
            $("#edit_category_name").val(name);
            $("#editCategoryForm").find('input[name="operation"]').val("update");
            $("#editCategoryModal").modal("show"); // Ensure the modal is triggered
        });

        // Delete Category Modal
        $(".deleteCategoryBtn").click(function () {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#delete_category_id").val(id);
            $("#delete_category_name").text(name);
            $("#deleteCategoryModal").modal("show"); // Ensure the modal is triggered
        });

        // Submit Edit Category Form
        $("#editCategoryForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = 'edit_category.php'; // Updated URL
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json', // Specify JSON dataType
                success: function (response) {
                    if (response.success) {
                        // Update the category name in the table
                        var id = $("#edit_category_id").val();
                        var newName = response.name;
                        $("#example1").find("[data-id='" + id + "']").closest("tr").find(".categoryName").text(newName);
                        $("#editCategoryModal").modal("hide");
                        alert("Category updated successfully");
                    } else {
                        console.log('Error:', response.message);
                        alert("Failed to update category");
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                    alert("Failed to update category");
                }
            });
        });

        // Submit Delete Category Form
        $("#deleteCategoryForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = 'delete_category.php'; // Updated URL
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function (data) {
                    // Reload the page or update the specific part of the page
                    window.location.reload();
                    alert("Category deleted successfully"); // Show success alert
                },
                error: function (data) {
                    console.log('Error:', data);
                    alert("Failed to delete category"); // Show error alert
                }
            });
        });
    });
</script>
