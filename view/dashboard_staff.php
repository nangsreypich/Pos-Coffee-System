<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: user/login.php");
    exit();
}

//Connection
include('../controller/connection.php');

// Prepare Query
$statement = $pdo->prepare("SELECT staff.*, position.name AS position_name FROM staff INNER JOIN position ON staff.pos_id = position.id WHERE staff.status=1");

// Check for query execution errors
if (!$statement) {
    die('Error in preparing the query: ' . $pdo->errorInfo()[2]);
}

// Execute Query
if (!$statement->execute()) {
    die('Error in executing the query: ' . $statement->errorInfo()[2]);
}

// Fetch Data
$staffList = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('head.php') ?>

<div class="container-fluid">
    <?php include('header.php'); ?>
    <div class="row">
        <!-- Sidebar -->
        <?php include('sidebar_staff.php') ?>
        <!-- Main Content Area -->
        <div id="mainContent" class="col-md-10 mt-4">
            <h1>Dashboard</h1>
            <div class="table-responsive" style="overflow-y: auto;">
                <table class="table table-striped table-bordered"> <!-- Added table-bordered class -->
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
               <tbody>
                  <?php foreach ($staffList as $key => $pro) { ?>
                     <tr>
                        <th scope="row"><?php echo $key + 1 ?></th>
                        <td><?php echo $pro['name']; ?></td>
                        <td><?php echo $pro['gender']; ?></td>
                        <td><?php echo $pro['dob']; ?></td>
                        <td><?php echo $pro['pob']; ?></td>
                        <td><?php echo $pro['phone']; ?></td>
                        <td><?php echo $pro['email']; ?></td>
                        <td><?php echo $pro['address']; ?></td>
                        <td><?php echo $pro['position_name']; ?></td>
                        <td>
                           <div class="d-grid gap-2 d-md-block">
                              <a class="btn btn-danger" type="button" href="edit_staff.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-user-edit"></i></a>
                              <a class="btn btn-primary" type="button" href="delete_staff.php?id=<?php echo $pro["id"] ?>"><i class="fas fa-trash-alt"></i></a>
                           </div>
                        </td>
                     </tr>
                  <?php } ?>
               </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php include('footer.php') ?>
</div>