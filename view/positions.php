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
            // Check if the position name already exists
            $statement = $pdo->prepare("SELECT COUNT(*) FROM position WHERE name = :name AND status=1");
            $statement->bindValue(':name', $name);
            $statement->execute();
            $count = $statement->fetchColumn();

            if ($count > 0) {
                $errors[] = "Position name already exists";
            } else {
                // Insert position
                $statement = $pdo->prepare("INSERT INTO position (name) VALUES (:name)");
                $statement->bindValue(':name', $name);
                if ($statement->execute()) {
                    $success = "Position added successfully";
                    // Clear the form data
                    $name = "";
                    header("Location: " . $_SERVER["PHP_SELF"]);
                    exit();
                } else {
                    $errors[] = "Failed to add position";
                }
            }
        } elseif ($operation == "update") {
            $id = $_POST["id"];
            // Update position
            $statement = $pdo->prepare("UPDATE position SET name = :name WHERE id = :id");
            $statement->bindValue(':name', $name);
            $statement->bindValue(':id', $id);
            if ($statement->execute()) {
                $success = "Position updated successfully";
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit();
            } else {
                $errors[] = "Failed to update position";
            }
        }
    }
}

// Fetch all positions from the database
require ("../controller/connection.php");
$statement = $pdo->prepare("SELECT * from position where position.status=1");
//Execute Query
$statement->execute();
//Get Data
$positionList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include ('head.php') ?>

<div class="container-fluid">
    <?php include ('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include ('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h2>Add New Position</h2>
            <!-- Show errors -->
            <?php require ('alert.php') ?>

            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form id="addPositionForm" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="mt-2">Position Name</label>
                    <input type="text" class="form-control mt-3" placeholder="Enter position name" name="name" id="name"
                           autocomplete="off" style="width:50%;" value="<?php echo $name; ?>">
                </div>
                <input type="hidden" name="operation" value="insert">
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

            <h2 class="mt-5">All Positions</h2>
            <?php if (!empty($positionList)) { ?>
                <table id="positionTable" class="table table-striped table-bordered mt-3">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($positionList as $key => $pro) { ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1 ?></th>
                            <td><?php echo htmlspecialchars($pro['name']); ?></td>
                            <td>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-primary editPositionBtn" data-id="<?php echo $pro["id"] ?>"
                                            data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i
                                            class="fas fa-user-edit"></i></button>
                                    <button class="btn btn-danger deletePositionBtn" data-id="<?php echo $pro["id"] ?>"
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

<!-- Edit Position Modal -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
            </div>
            <div class="modal-body">
                <form id="editPositionForm" method="post">
                    <div class="form-group">
                        <label for="edit_position_name">Position Name</label>
                        <input type="text" class="form-control" id="edit_position_name" name="name" required>
                    </div>
                    <input type="hidden" id="edit_position_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Position Modal -->
<div class="modal fade" id="deletePositionModal" tabindex="-1" role="dialog" aria-labelledby="deletePositionModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePositionModalLabel">Confirm Delete Position</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete position <span id="delete_position_name"></span>?</p>
                <form id="deletePositionForm" method="post">
                    <input type="hidden" id="delete_position_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mt-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Edit Position Modal
        $(".editPositionBtn").click(function () {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#edit_position_id").val(id);
            $("#edit_position_name").val(name);
            $("#editPositionForm").find('input[name="operation"]').val("update");
            $("#editPositionModal").modal("show"); // Ensure the modal is triggered
        });

        // Delete Position Modal
        $(".deletePositionBtn").click(function () {
            var id = $(this).data("id");
            var name = $(this).data("name");
            $("#delete_position_id").val(id);
            $("#delete_position_name").text(name);
            $("#deletePositionModal").modal("show"); // Ensure the modal is triggered
        });

        // Submit Edit Position Form
        $("#editPositionForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = 'edit_position.php'; // Updated URL
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json', // Specify JSON dataType
                success: function (response) {
                    if (response.success) {
                        // Update the position name in the table
                        var id = $("#edit_position_id").val();
                        var newName = response.name;
                        $("#positionTable").find("[data-id='" + id + "']").closest("tr").find(".positionName").text(newName);
                        $("#editPositionModal").modal("hide");
                        alert("Position updated successfully");
                    } else {
                        console.log('Error:', response.message);
                        alert("Failed to update position");
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                    alert("Failed to update position");
                }
            });
        });

        // Submit Delete Position Form
        $("#deletePositionForm").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = 'delete_position.php'; // Updated URL
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function (data) {
                    // Reload the page or update the specific part of the page
                    window.location.reload();
                    alert("Position deleted successfully"); // Show success alert
                },
                error: function (data) {
                    console.log('Error:', data);
                    alert("Failed to delete position"); // Show error alert
                }
            });
        });
    });
</script>
