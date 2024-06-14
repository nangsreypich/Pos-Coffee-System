<h1>Stocker<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: user/login.php");
    exit();
}

//Connection
include('../controller/connection.php');
//Prepare Query
$statement = $pdo->prepare("SELECT * FROM librarian");
//Execute Query
$statement->execute();
//Get Data
$librarianList = $statement->fetchAll(PDO::FETCH_ASSOC);
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
            <?php include('sidebar_stocker.php') ?>
            <!-- Main Content Area -->
            <div class="col-md-10">
                <h1>Stocker Dashboard</h1>
                <a href="add_librarian.php"><button style="float:right;" class="btn btn-primary">+Add Librarian</button></a>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Date of Birth</th>
                        <th scope="col">Address</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Join Date</th>
                        <th scope="col">Image</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($librarianList as $key => $pro) { ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1 ?></th>
                            <td><?php echo $pro['full_name']; ?></td>
                            <td><?php echo $pro['dob']; ?></td>
                            <td><?php echo $pro['address']; ?></td>
                            <td><?php echo $pro['phone']; ?></td>
                            <td><?php echo $pro['join_date']; ?></td>
                            <td><img src="<?php echo $pro['image'] ?? "https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg" ?>" alt="" width="30px" height="30px"></td>
                            <td>
                                <div class="d-grid gap-2 d-md-block">
                                    <a class="btn btn-danger" type="button" href="edit_librarian.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-user-edit"></i></a>
                                    <a class="btn btn-primary" type="button" href="delete_librarian.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-trash-alt"></i></a>
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
</body>
</html>
</h1>