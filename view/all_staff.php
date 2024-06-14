<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}
require("../controller/connection.php");

// Fetch all staff with their positions
$statement = $pdo->prepare("SELECT staff.*, position.name AS position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.status=1");
$statement->execute();
$staffList = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all positions for the dropdown
$positionStatement = $pdo->prepare("SELECT id, name FROM position");
$positionStatement->execute();
$positions = $positionStatement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <?php include('sidebar_manager.php') ?>
        <div class="col-md-10 mt-4">
            <h1>All Staff</h1>
            <div id="success-message" class="alert alert-success" role="alert" style="display: none;"></div>

            <div class="table-responsive">
                <a href="add_staff.php"><button style="float:right;" class="btn btn-primary mb-2">+Add Staff</button></a>
                <table id="example1" class="table table-striped table-bordered mt-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Staff Name</th>
                            <th scope="col">Gender</th>
                            <th scope="col">DOB</th>
                            <th scope="col">POB</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Email</th>
                            <th scope="col">Address</th>
                            <th scope="col">Position</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="staff-table-body">
                        <?php foreach ($staffList as $key => $pro) { ?>
                            <tr id="staff-row-<?php echo $pro['id']; ?>">
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td class="staff-name"><?php echo $pro['name']; ?></td>
                                <td class="staff-gender"><?php echo $pro['gender']; ?></td>
                                <td class="staff-dob"><?php echo $pro['dob']; ?></td>
                                <td class="staff-pob"><?php echo $pro['pob']; ?></td>
                                <td class="staff-phone"><?php echo $pro['phone']; ?></td>
                                <td class="staff-email"><?php echo $pro['email']; ?></td>
                                <td class="staff-address"><?php echo $pro['address']; ?></td>
                                <td class="staff-position" data-position-id="<?php echo $pro['pos_id']; ?>"><?php echo $pro['position_name']; ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <button class="btn btn-primary editStaffBtn" data-id="<?php echo $pro["id"] ?>" data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i class="fas fa-user-edit"></i></button>
                                        <button class="btn btn-danger deleteStaffBtn" data-id="<?php echo $pro["id"] ?>" data-name="<?php echo htmlspecialchars($pro['name']); ?>"><i class="fas fa-trash-alt"></i></button>
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

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" role="dialog" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
            </div>
            <div class="modal-body">
                <form id="editStaffForm" method="post">
                    <div class="form-group">
                        <label for="edit_position_name">Staff Name</label>
                        <input type="text" class="form-control" id="edit_position_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" class="form-select" name="gender" required>
                            <?php
                            $genders = ['Male', 'Female'];
                            foreach ($genders as $gen) : ?>
                                <option value="<?php echo $gen; ?>"><?php echo $gen; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" class="form-control" name="dob" required />
                    </div>
                    <div class="form-group">
                        <label for="pob">Place of Birth</label>
                        <input type="text" id="pob" class="form-control" name="pob" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" class="form-control" name="phone" required />
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="form-control" name="email" required />
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" class="form-control" name="address" required />
                    </div>
                    <div class="form-group">
                        <label for="pos_id">Position</label>
                        <select id="pos_id" class="form-control" name="pos_id" required>
                            <?php foreach ($positions as $position) : ?>
                                <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" id="edit_position_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Staff Modal -->
<div class="modal fade" id="deleteStaffModal" tabindex="-1" role="dialog" aria-labelledby="deleteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStaffModalLabel">Confirm Delete Staff</h5>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete staff <span id="delete_staff_name"></span>?</p>
                <form id="deleteStaffForm" method="post">
                    <input type="hidden" id="delete_staff_id" name="id">
                    <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger mt-2">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Edit Staff Modal
        $(".editStaffBtn").click(function() {
            var id = $(this).data("id");
            var name = $(this).data("name");
            var gender = $(this).closest("tr").find(".staff-gender").text();
            var dob = $(this).closest("tr").find(".staff-dob").text();
            var pob = $(this).closest("tr").find(".staff-pob").text();
            var phone = $(this).closest("tr").find(".staff-phone").text();
            var email = $(this).closest("tr").find(".staff-email").text();
            var address = $(this).closest("tr").find(".staff-address").text();
            var positionId = $(this).closest("tr").find(".staff-position").data("position-id");

            $("#edit_position_id").val(id);
            $("#edit_position_name").val(name);
            $("#gender").val(gender);
            $("#dob").val(dob);
            $("#pob").val(pob);
            $("#phone").val(phone);
            $("#email").val(email);
            $("#address").val(address);

            // Set the position in the select dropdown
            $("#pos_id").val(positionId);

            $("#editStaffModal").modal("show");
        });

        // Delete Staff Modal
        $(".deleteStaffBtn").click(function() {
            var id = $(this).data("id");
            var name = $(this).data("name");

            $("#delete_staff_id").val(id);
            $("#delete_staff_name").text(name);

            $("#deleteStaffModal").modal("show");
        });

        // Submit Edit Staff Form
        $("#editStaffForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'edit_staff.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var id = $("#edit_position_id").val();
                        var newName = response.name;
                        var newGender = response.gender;
                        var newDob = response.dob;
                        var newPob = response.pob;
                        var newPhone = response.phone;
                        var newEmail = response.email;
                        var newAddress = response.address;
                        var newPosition = response.position_name;

                        $("#staff-row-" + id).find(".staff-name").text(newName);
                        $("#staff-row-" + id).find(".staff-gender").text(newGender);
                        $("#staff-row-" + id).find(".staff-dob").text(newDob);
                        $("#staff-row-" + id).find(".staff-pob").text(newPob);
                        $("#staff-row-" + id).find(".staff-phone").text(newPhone);
                        $("#staff-row-" + id).find(".staff-email").text(newEmail);
                        $("#staff-row-" + id).find(".staff-address").text(newAddress);
                        $("#staff-row-" + id).find(".staff-position").text(newPosition);

                        $("#editStaffModal").modal("hide");
                        $("#success-message").text("Staff updated successfully").show().delay(3000).fadeOut();
                    } else {
                        alert("Failed to update staff: " + response.message);
                    }
                },
                error: function(data) {
                    alert("Update Sucess");
                }
            });
        });

         // Submit Delete Staff Form
        $("#deleteStaffForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var url = 'delete_staff.php';
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data) {
                    // Reload the page or update the specific part of the page
                    $("#staff-row-" + form.find('#delete_staff_id').val()).remove();
                    $("#deleteStaffModal").modal("hide");
                    alert("Staff deleted successfully"); // Show success alert
                },
                error: function(data) {
                    console.log('Error:', data);
                    alert("Failed to delete staff"); // Show error alert
                }
            });
        });
    });
</script>