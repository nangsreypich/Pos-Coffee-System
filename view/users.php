<?php
$errors = [];
$success = "";
$username = "";

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
    $username = $_POST["username"];
    $password = $_POST["password"];
    $type = $_POST["type"];

    // Validation
    if (!$username) {
        $errors[] = "Name is required";
    }
    if (!$password) {
        $errors[] = "Password is required";
    }
    if (!$type) {
        $errors[] = "Type is required";
    }

    if (empty($errors)) {
        // Fetch the position based on the selected type
        $statement = $pdo->prepare("SELECT name FROM position WHERE id = :type");
        $statement->bindValue(':type', $type);
        $statement->execute();
        $position = $statement->fetch(PDO::FETCH_ASSOC);

        if ($position) {
            $positionName = $position['name'];

            // Prepare and execute statement
            $statement = $pdo->prepare("INSERT INTO users (username, password, type) VALUES (:username, :password, :type)");
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $password);
            $statement->bindValue(':type', $positionName);
            if ($statement->execute()) {
                $success = "User added successfully";
                $username = "";
            } else {
                $errors[] = "Failed to add user";
            }
        } else {
            $errors[] = "Invalid position selected";
        }
    }
}

// Fetch all positions from the database
require("../controller/connection.php");
$statement = $pdo->prepare("SELECT * FROM position WHERE position.status=1");
// Execute Query
$statement->execute();
// Get Data
$positions = $statement->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users from the database
$statement = $pdo->prepare("SELECT * FROM users WHERE users.status=1");
// Execute Query
$statement->execute();
// Get Data
$userList = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_manager.php') ?>
        <!-- Content -->
        <div class="col-md-10 mt-4">
            <h2>Add New User</h2>
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
                    <label for="username" class="mt-2">User Name</label>
                    <input type="text" class="form-control mt-3" placeholder="Enter username" name="username" id="username" autocomplete="off" style="width:50%;" value="">
                </div>
                <div class="form-group">
                    <label for="password" class="mt-2">Password</label>
                    <input type="text" class="form-control mt-3" placeholder="Enter password" name="password" id="password" autocomplete="off" style="width:50%;" value="">
                </div>
                <div class="form-group">
                    <label for="type" class="mt-2">Type</label>
                    <select id="type" class="form-control mt-3" name="type" style="width:50%;">
                        <option value="">==Select user type==</option>
                        <?php foreach ($positions as $position) : ?>
                            <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>

            <h2 class="mt-5">All Users</h2>
            <?php if (!empty($userList)) { ?>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userList as $key => $pro) { ?>
                            <tr>
                                <th scope="row"><?php echo $key + 1 ?></th>
                                <td><?php echo htmlspecialchars($pro['username']); ?></td>
                                <td><?php echo htmlspecialchars($pro['password']); ?></td>
                                <td><?php echo htmlspecialchars($pro['type']); ?></td>
                                <td>
                                    <div class="d-grid gap-2 d-md-block">
                                        <a class="btn btn-danger" type="button" href="edit_user.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-user-edit"></i></a>
                                        <a class="btn btn-primary" type="button" href="delete_user.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-trash-alt"></i></a>
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
