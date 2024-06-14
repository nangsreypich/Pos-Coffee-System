<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if not logged in
    header("Location: ../user/login.php");
    exit(); // Make sure to exit after redirection
}
$error = [];
$success = '';

// Create Connection
require_once("../controller/connection.php");

// ==== Update ====
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    // Get data from form
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $type = $_REQUEST["type"];
    $id = $_REQUEST["id"]; // Don't forget to retrieve the ID

    // Validation
    if (!$username || !$password || !$type || !$id) {
        $error[] = "All fields are required";
    }

    // Prepare and execute update query
    if (empty($error)) {
        $upSt = $pdo->prepare("UPDATE users SET username=:username, password=:password, type=:type WHERE id=:id");

        $upSt->bindValue(':username', $username);
        $upSt->bindValue(':password', $password);
        $upSt->bindValue(':type', $type);
        $upSt->bindValue(':id', $id);

        if ($upSt->execute()) {
            $success = "User details updated successfully!";
        } else {
            $error[] = "Error updating user details. Please try again.";
        }
    }
}

// Fetch positions from the database
$positionsQuery = $pdo->query("SELECT * FROM position");
$positions = $positionsQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch user details
$id = $_REQUEST["id"];
$statement = $pdo->prepare("SELECT * FROM users WHERE users.status=1 AND users.id=:id");
$statement->bindValue(':id', $id);
$statement->execute();
$pro = $statement->fetch(PDO::FETCH_ASSOC);

?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h1>Edit User</h1>
            <!-- Show success alert -->
            <?php if ($success) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <!-- Show error alerts -->
            <?php foreach ($error as $err) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $err; ?>
                </div>
            <?php endforeach; ?>

            <form action="" method="post">
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <label for="username" class="form-label mt-2">User Name</label>
                    </div>
                    <div class="" style="width: 50%;">
                        <input type="text" id="username" class="form-control mt-3" name="username" value="<?php echo $pro['username']; ?>" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <label for="password" class="form-label mt-2">Password</label>
                    </div>
                    <div class="" style="width: 50%;">
                        <input type="text" id="password" class="form-control mt-3" name="password" value="<?php echo $pro['password']; ?>" />
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <label for="type" class="form-label mt-2">Type</label>
                    </div>
                    <div class="col-md-9" style="width:50%;">
                        <select id="type" class="form-control" name="type">
                            <?php foreach ($positions as $position) : ?>
                                <option value="<?php echo $position['name']; ?>" <?php if ($position['name'] == $pro['type']) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($position['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $pro['id']; ?>" />
                <button class="btn btn-success mt-3" style="float:left;">Save</button>
            </form>
        </div>
    </div>
</div>