<?php
// Include database connection
include('../controller/connection.php');

// Ensure session is started
session_start();

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
        case "Stocker":
            header("Location: ../view/dashboard_stocker.php");
            break;
        case "Cashier":
            header("Location: ../view/dashboard_cashier.php");
            break;
        case "Customer":
            header("Location: ../view/dashboard_customer.php");
            break;
        default:
            // Redirect to a generic dashboard if type is not recognized
            header("Location: ../view/dashboard.php");
    }
    exit(); // Ensure to exit after header redirect
}

// Initialize an empty error message
$error_message = '';

// Check if the user is already logged in via cookies
if (isset($_COOKIE['username']) && isset($_COOKIE['user_type'])) {
    $_SESSION["username"] = $_COOKIE['username'];
    redirectBasedOnUserType($_COOKIE['user_type']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if username, password, and login type are provided
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['login_type'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $loginType = $_POST['login_type'];

        try {
            // Prepare the query based on the login type
            if (in_array($loginType, ['Manager', 'Stocker', 'Cashier', 'Staff', 'Customer'])) {
                // Prepare SQL query for users table
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username=:username AND password=:password AND type=:loginType");
                $stmt->bindParam(':loginType', $loginType);
            } else {
                // Invalid login type (should not happen if frontend validation is in place)
                $error_message = "Invalid login type.";
            }

            if (empty($error_message)) {
                // Bind parameters and execute the statement
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                // Fetch the result
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Username, password, and user type match
                    $_SESSION["username"] = $username;
                    $_SESSION["user_type"] = $loginType; // Set the user type in session

                    // Optionally set login cookies
                    setLoginCookies($username, $loginType);

                    // Redirect based on user type
                    redirectBasedOnUserType($loginType);
                } else {
                    // Username, password, or user type do not match
                    $error_message = "No user found with this username and login type.";
                }
            }
        } catch (PDOException $e) {
            // Error in database connection or query
            $error_message = "Error: " . $e->getMessage();
        }
    } else {
        // Username, password, or login type not provided
        $error_message = "Please provide username, password, and login type.";
    }
}
?>

<?php include('../view/head.php') ?>
<style>
    .bg {
        background-image: url('../image/COVER WEP09000.jpg');
        min-height: 100vh;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .container {
        font-family: "Inter", sans-serif;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .card {
        border-radius: 1rem;
        background-color: #b48712;
        width: 100%;
        max-width: 500px;
    }
</style>

<div class="bg">
    <div class="container">
        <div class="card shadow-2-strong">
            <div class="card-body p-5 text-center">
                <h3 class="mb-5">Select Action</h3>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <div class="form-outline mb-4">
                    <label for="action_type" class="form-label h5">I want to:</label>
                    <select class="form-control" id="action_type" name="action_type" required>
                        <option value="login">Login</option>
                        <option value="register">Register</option>
                    </select>
                </div>
                
                <div id="login-form" class="d-none">
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

                        <div class="form-outline mb-4">
                            <label for="login_type" class="form-label h5">Login as</label>
                            <select class="form-control" id="login_type" name="login_type" required>
                                <option value="Manager">Manager</option>
                                <option value="Stocker">Stocker</option>
                                <option value="Cashier">Cashier</option>
                                <option value="Customer">Customer</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</button>
                    </form>
                </div>

                <div id="register-form" class="d-none">
                    <form method="post" action="register.php">
                        <h3 class="mb-5">Register</h3>

                        <div class="form-outline mb-4">
                            <label for="reg_email" class="form-label h5">Email</label>
                            <input class="form-control" type="email" id="reg_email" name="email" placeholder="Enter email" required>
                        </div>                    
                        <div class="form-outline mb-4">
                            <label for="reg_username" class="form-label h5">Username</label>
                            <input class="form-control" type="text" id="reg_username" name="username" placeholder="Enter username" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label for="reg_address" class="form-label h5">Address</label>
                            <input class="form-control" type="text" id="reg_address" name="address" placeholder="Enter address" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label for="reg_password" class="form-label h5">Password</label>
                            <input class="form-control" type="password" id="reg_password" name="password" placeholder="Enter password" required>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure the login form is shown by default
    document.addEventListener('DOMContentLoaded', function() {
        var loginForm = document.getElementById('login-form');
        var registerForm = document.getElementById('register-form');
        
        // Show login form by default
        loginForm.classList.remove('d-none');
        registerForm.classList.add('d-none');

        // Add event listener to toggle forms based on action_type select
        document.getElementById('action_type').addEventListener('change', function() {
            if (this.value === 'login') {
                loginForm.classList.remove('d-none');
                registerForm.classList.add('d-none');
            } else if (this.value === 'register') {
                registerForm.classList.remove('d-none');
                loginForm.classList.add('d-none');
            }
        });
    });
</script>
