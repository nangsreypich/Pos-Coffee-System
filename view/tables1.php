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
    require("../controller/connection.php");

    // Get data from form
    $name = $_POST["name"];
    $operation = $_POST["operation"];

    // Validation
    if (!$name) {
        $errors[] = "Name is required";
    }

    if (empty($errors)) {
        if ($operation == "insert") {
            // Check if the coffee_table name already exists
            $statement = $pdo->prepare("SELECT COUNT(*) FROM coffee_table WHERE name = :name AND status=1");
            $statement->bindValue(':name', $name);
            $statement->execute();
            $count = $statement->fetchColumn();

            if ($count > 0) {
                $errors[] = "Table name already exists";
            } else {
                // Insert coffee_table
                $statement = $pdo->prepare("INSERT INTO coffee_table (name) VALUES (:name)");
                $statement->bindValue(':name', $name);
                if ($statement->execute()) {
                    $success = "Table added successfully";
                    // Clear the form data
                    $name = "";
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit();
                } else {
                    $errors[] = "Failed to add coffee_table";
                }
            }
        } elseif ($operation == "update") {
            $id = $_POST["id"];
            // Update coffee_table
            $statement = $pdo->prepare("UPDATE coffee_table SET name = :name WHERE id = :id");
            $statement->bindValue(':name', $name);
            $statement->bindValue(':id', $id);
            if ($statement->execute()) {
                $success = "Table updated successfully";
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit();
            } else {
                $errors[] = "Failed to update coffee_table";
            }
        }
    }
}

// Fetch all Tables from the database
require("../controller/connection.php");
$statement = $pdo->prepare("SELECT * from coffee_table where coffee_table.status=1");
//Execute Query
$statement->execute();
//Get Data
$tableList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_cashier.php') ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h2>Add New Table</h2>
            <!-- Show errors -->
            <?php require('alert.php') ?>

            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="mt-2">Table Name</label>
                    <input type="text" class="form-control mt-3" placeholder="Enter table drink name" name="name" id="name" autocomplete="off" style="width:50%;" value="<?php echo $name; ?>">
                </div>
                <div class="form-group mt-4">
                    <input type="hidden" name="operation" value="insert">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

            <h2 class="mt-5">All Tables</h2>
            <?php if (!empty($tableList)) { ?>
                <table class="table table-bordered mt-3" id="example1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Table</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableList as $key => $pro) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td class="tableName"><?php echo htmlspecialchars($pro['name']); ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-primary editTableBtn" data-id="<?php echo $pro["id"] ?>" data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i class="fas fa-user-edit"></i></button>
                                        <button class="btn btn-danger deleteTableBtn" data-id="<?php echo $pro["id"] ?>" data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <?php include('footer.php') ?>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTableModalLabel">Edit Table</h5>
            </div>
            <div class="modal-body">
                <form id="editTableForm" method="post">
                    <div class="form-group">
                        <label for="edit_table_name">Table Name</label>
                        <input type="text" class="form-control" id="edit_table_name" name="name" required>
                    </div>
                    <input type="hidden" id="edit_table_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Table Modal -->
<div class="modal fade" id="deleteTableModal" tabindex="-1" role="dialog" aria-labelledby="deleteTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTableModalLabel">Confirm Delete Table</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete table <span id="delete_table_name"></span>?</p>
                <form id="deleteTableForm" method="post">
                    <input type="hidden" id="delete_table_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mt-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit Table Modal
        $(".editTableBtn").click(function() {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#edit_table_id").val(id);
            $("#edit_table_name").val(name);
            $("#editTableModal").modal("show");
        });

        // Submit Edit Table Form
        $("#editTableForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'edit_table.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var id = $("#edit_table_id").val();
                        var newName = response.name;
                        $("#example1").find("[data-id='" + id + "']").closest("tr").find(".tableName").text(newName);
                        $("#editTableModal").modal("hide");
                        alert("Table updated successfully");
                    } else {
                        console.log('Error:', response.message);
                        alert("Failed to update table: " + response.message);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    alert("Failed to update table");
                }
            });
        });

        // Delete Table Modal
        $(".deleteTableBtn").click(function() {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#delete_table_id").val(id);
            $("#delete_table_name").text(name);
            $("#deleteTableModal").modal("show");
        });

        // Submit Delete Table Form
        $("#deleteTableForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'delete_table.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data) {
                    window.location.reload();
                    alert("Table deleted successfully");
                },
                error: function(data) {
                    console.log('Error:', data);
                    alert("Failed to delete table");
                }
            });
        });
    });
</script>