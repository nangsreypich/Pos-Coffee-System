<?php
session_start();
$isLogin = false;
// Connection
require("connection.php");

// Prepare Query for Borrow
$statement_borrow = $pdo->prepare("SELECT borrow.*, book.book_name AS book_name, librarian.full_name AS librarian_name FROM borrow
                                    INNER JOIN book ON borrow.book_id = book.id
                                    INNER JOIN librarian ON borrow.librarian_id = librarian.id");
// Execute Query for Borrow
$statement_borrow->execute();
// Get Borrow Data
$borrowList = $statement_borrow->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION["uname"])) {
    $uname = $_SESSION["uname"];
}
if (isset($_COOKIE["isLogin"])) {
    $isLogin = $_COOKIE["isLogin"];
}
if (!isset($_COOKIE["isLogin"]) && $isLogin == false) {
    header("Location: user/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('head.php') ?>
<body>
<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <?php include('sidebar.php') ?>
            <!-- Main Content Area -->
            <div class="col-md-10">
                <h1>Dashboard</h1>
                <a href="add_borrow.php"><button style="float:right;" class="btn btn-primary">+Add Borrow</button></a>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Book Name</th>
                        <th scope="col">Borrow Date</th>
                        <th scope="col">Given Date</th>
                        <th scope="col">Librarian</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($borrowList as $key => $borrow) { ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1 ?></th>
                            <td><?php echo $borrow['book_name']; ?></td>
                            <td><?php echo $borrow['borrow_date']; ?></td>
                            <td><?php echo $borrow['given_date']; ?></td>
                            <td><?php echo $borrow['librarian_name']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
