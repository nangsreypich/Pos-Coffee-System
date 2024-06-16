<?php
include('alert.php');
session_start();

// Include database connection
include('../controller/connection.php');

// Function to set a cookie
function setLoginCookies($username, $userType) {
    setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days
    setcookie("user_type", $userType, time() + (86400 * 30), "/"); // 30 days
}

// Function to redirect based on user type
function redirectBasedOnUserType($userType) {
    switch ($userType) {
        case "Manager":
            header("Location: ../view/dashboard_manager.php");
            break;
        case "Staff":
            header("Location: ../view/dashboard_staff.php");
            break;
        case "Cashier":
            header("Location: ../view/dashboard_cashier.php");
            break;
        case "Stocker":
            header("Location: ../view/dashboard_stocker.php");
            break;
        default:
            // Redirect to a generic dashboard if type is not recognized
            header("Location: ../view/dashboard.php");
    }
    exit();
}

// Check if the user is already logged in via cookies
if (isset($_COOKIE['username']) && isset($_COOKIE['user_type'])) {
    $_SESSION["username"] = $_COOKIE['username'];
    redirectBasedOnUserType($_COOKIE['user_type']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if username and password are provided
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            // Prepare SQL statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username=:username AND password=:password");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // Username and password match
                $_SESSION["username"] = $username;
                // Set login cookies
                setLoginCookies($username, $row["type"]);
                // Redirect based on user type
                redirectBasedOnUserType($row["type"]);
            } else {
                // Username and password do not match
                showAlert("Invalid username or password.");
            }
        } catch (PDOException $e) {
            // Error in database connection or query
            showAlert("Error: " . $e->getMessage());
        }
    } else {
        // Username or password not provided
        showAlert("Please provide username and password.");
    }
}
?>

<?php include('../view/head.php') ?>
<style>
    .bg {
        background-image: url('../image/COVER\ WEP09000.jpg');
        height: 100%;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    .container {
        font-family: "Inter", sans-serif;
    }
</style>

<div class="bg vh-100">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-2-strong" style="border-radius: 1rem; background-color: #b48712;">
                    <div class="card-body p-5 text-center">
                        <form method="post" action="">
                            <h3 class="mb-5">Sign in</h3>

                            <div class="form-outline mb-4">
                                <label for="username" class="form-label h5">Username</label>
                                <input class="form-control" type="text" id="username" name="username" placeholder="Enter username" required>
                            </div>

                            <div class="form-outline mb-4">
                                <label for="password" class="form-label h5">Password</label>
                                <input class="form-control" type="password" id="password" name="password" placeholder="Enter password" required>
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
