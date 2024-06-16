<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: ../user/login.php");
    exit();
}

require("../controller/connection.php");

$username = $_SESSION["username"];

// Fetch the user details including the password (assuming password is stored in plain text)
$statement = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
$statement->execute([':username' => $username]);
$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_username'])) {
        // Update username
        $newUsername = $_POST['new_username'];
        $updateStmt = $pdo->prepare("UPDATE users SET username = :new_username WHERE id = :id");
        $updateStmt->execute([':new_username' => $newUsername, ':id' => $user['id']]);
        
        // Update session and cookies
        $_SESSION['username'] = $newUsername;
        setcookie("username", $newUsername, time() + (86400 * 30), "/");
        
        $username = $newUsername;
        $successMessage = "Username updated successfully.";
    }

    if (isset($_POST['new_password'])) {
        // Update password (plain text storage example)
        $newPassword = $_POST['new_password'];
        $updateStmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :id");
        $updateStmt->execute([':new_password' => $newPassword, ':id' => $user['id']]);

        $successMessage = isset($successMessage) ? $successMessage . " Password updated successfully." : "Password updated successfully.";
    }
}

include('head.php');
?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <?php include('sidebar_manager.php'); ?>
        <div class="col-md-10 mt-4">
            <h1>My Profile</h1>
            <?php if (isset($successMessage)) : ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <?php if (isset($errorMessage)) : ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>

            <h2>Profile Details</h2>
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <td><?php echo htmlspecialchars($username); ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><?php echo htmlspecialchars($user['password']); ?></td>
                </tr>
                <!-- You can add more profile details here as needed -->
            </table>

            <h2>Update Profile</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="new_username" class="form-label">New Username</label>
                    <input type="text" class="form-control" id="new_username" name="new_username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
