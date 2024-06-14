<?php
$errors = [];
$success = "";
$name = "";
$gender = "";
$dob = "";
$pob = "";
$phone = "";
$address = "";
$email = "";
$position = "";

// Check if the user is logged in
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    require("../controller/connection.php");

    // Get data from form
    $name = $_POST["name"];
    $gender = $_POST["gender"];
    $dob = $_POST["dob"];
    $pob = $_POST["pob"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $position = $_POST["pos_id"];

    // Validation
    $errors = [];
    if (!$name) {
        $errors[] = "Name is required";
    }
    if (!$gender) {
        $errors[] = "Gender is required";
    }
    if (!$dob) {
        $errors[] = "Date of birth is required";
    }
    if (!$phone) {
        $errors[] = "Phone is required";
    }
    if (!$address) {
        $errors[] = "Address is required";
    }
    if (!$position) {
        $errors[] = "Position is required";
    }
    if (!$email) {
        $errors[] = "Email is required";
    }

    if (empty($errors)) {
        // Check if the email already exists
        $stmt_check_email = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE email = :email");
        $stmt_check_email->bindValue(':email', $email);
        $stmt_check_email->execute();
        $email_count = $stmt_check_email->fetchColumn();

        if ($email_count > 0) {
            $errors[] = "Email already exists";
        } else {
            // Prepare and execute statement to insert into staff table
            $statement = $pdo->prepare("INSERT INTO staff(name, gender, dob, pob, phone, address, pos_id, email, status) VALUES(:name, :gender, :dob, :pob, :phone, :address, :pos_id, :email, 1)");
            $statement->bindValue(':name', $name);
            $statement->bindValue(':gender', $gender);
            $statement->bindValue(':dob', $dob);
            $statement->bindValue(':pob', $pob);
            $statement->bindValue(':phone', $phone);
            $statement->bindValue(':address', $address);
            $statement->bindValue(':pos_id', $position);
            $statement->bindValue(':email', $email);

            if ($statement->execute()) {
                $staff_id = $pdo->lastInsertId(); // Get the last inserted ID from staff table
            
                // Fetch position name based on position ID from the staff table
                $stmt_position = $pdo->prepare("SELECT name FROM position WHERE id = :pos_id");
                $stmt_position->bindValue(':pos_id', $position);
                $stmt_position->execute();
                $position_row = $stmt_position->fetch(PDO::FETCH_ASSOC);
                $position_name = $position_row['name'];
            
                // Insert data into specific position table
                if ($position_name !== "Staff") { // Check if the position is not "Staff"
                    $table_name = strtolower($position_name);
                    $stmt = $pdo->prepare("INSERT INTO $table_name(staff_id, staff_name, gender, dob, pob, phone, address, email, status, pos_id) VALUES(:staff_id, :staff_name, :gender, :dob, :pob, :phone, :address, :email, 1, :pos_id)");
            
                    $stmt->bindValue(':staff_id', $staff_id); // Use staff_id from staff table directly
                    $stmt->bindValue(':staff_name', $name);
                    $stmt->bindValue(':gender', $gender);
                    $stmt->bindValue(':dob', $dob);
                    $stmt->bindValue(':pob', $pob);
                    $stmt->bindValue(':phone', $phone);
                    $stmt->bindValue(':address', $address);
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':pos_id', $position);
            
                    if (!$stmt->execute()) {
                        $errors[] = "Failed to add staff to $table_name table";
                    }
                }
            
                $success = "Staff added successfully";
            } else {
                $errors[] = "Failed to add staff";
            }
            
        }
    }
}

require("../controller/connection.php");
// Fetch positions from the database
$positions = []; // Initialize the array
$stmt = $pdo->query("SELECT id, name FROM position");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $positions[] = $row;
}
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-md-6 mt-4">
            <h2>Add New Staff</h2>
            <!-- <button class="btn btn-success" style="float:right;">Back</button> -->
            <!-- Show error -->
            <?php require('alert.php') ?>

            <?php if ($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <?php foreach ($errors as $error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <form action="" method="post" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3 row">
                    <label for="name" class="form-label col-md-3">Staff Name </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="name" class="form-control" name="name" value="" placeholder="Enter staff name" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="gender" class="form-label col-md-3">Gender </label>
                    <div class="col-md-9" style="width:60%;">
                        <select id="gender" class="form-select" name="gender">
                            <option value="hidden">==Select gender==</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="dob" class="form-label col-md-3">Date of Birth </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="date" id="dob" class="form-control" name="dob" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="pob" class="form-label col-md-3">Place of Birth </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="pob" class="form-control" name="pob" placeholder="Enter palce of birth" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="phone" class="form-label col-md-3">Phone </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="phone" class="form-control" name="phone" value="" placeholder="Enter phone number" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="address" class="form-label col-md-3">Address </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="text" id="address" class="form-control" name="address" value="" placeholder="Enter address" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="email" class="form-label col-md-3">Email </label>
                    <div class="col-md-9" style="width:60%;">
                        <input type="email" id="email" class="form-control" name="email" value="" placeholder="Enter email" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <label for="pos_id" class="form-label col-md-3">Position</label>
                    <div class="col-md-9" style="width:60%;">
                        <select id="pos_id" class="form-control" name="pos_id">
                            <?php foreach ($positions as $pos) : ?>
                                <option value="<?php echo $pos['id']; ?>" <?php if ($pos['id'] == $position) echo "selected"; ?>><?php echo htmlspecialchars($pos['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-9" style="width:60%;">
                    <button class="btn btn-success" style="float:left;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>