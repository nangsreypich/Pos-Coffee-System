<?php

// Include the alert function
include('alert.php');

// Include database connection
include('../controller/connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if all required fields are provided
    if (isset($_POST['email']) && isset($_POST['username']) && isset($_POST['address']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $address = $_POST['address'];
        $password = $_POST['password'];

        try {
            // Check if the username already exists for customers
            $stmt = $pdo->prepare("SELECT * FROM customer WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the username already exists for staff
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer || $staff) {
                showAlert("Username already exists. Please choose a different username.");
            } else {
                // Insert new customer into the customer table
                $stmt = $pdo->prepare("INSERT INTO customer (email, username, address, password) VALUES (:email, :username, :address, :password)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                // Insert user into the users table with type 'Customer'
                $stmt = $pdo->prepare("INSERT INTO users (username, password, type) VALUES (:username, :password, 'Customer')");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                // Registration successful
                showAlert("Registration successful. You can now log in.");
                exit();
            }
        } catch (PDOException $e) {
            // Error in database connection or query
            showAlert("Error: " . $e->getMessage());
        }
    } else {
        // Required fields not provided
        showAlert("Please provide email, username, address, and password.");
    }
}
?>
