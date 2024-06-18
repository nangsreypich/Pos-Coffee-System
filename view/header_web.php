<header class="d-flex justify-content-between py-3 border-bottom">
    <div class="d-flex align-items-center">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="../image/Logo ASSIGMENT.png" alt="" class="bi me-2" width="70" height="70">
            <span class="fs-5">Third Coffee Shop</span>
        </a>
    </div>

    <!-- Login button for small screens -->
    <div class="d-md-none mt-2">
        <?php 
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            echo '<span style="padding-right: 20px; color: blue;">Welcome, '.$username.'</span>';
            echo '<a href="../user/logout.php" class="btn btn-danger"><i class="fa fa-right-from-bracket"></i> Logout</a>';
        } else {
            echo '<a href="index1.php" class="btn mx-2" style="background-color:#b48712;margin-top:10px;">Home</a>';
            echo '<a href="contact.php" class="btn mx-2" style="background-color:#b48712;margin-top:10px;">Contact</a>';
            echo '<a href="../user/login.php" class="btn mx-2 p-2" style="background-color:#b48712;margin-top:10px;"><i class="fas fa-right-to-bracket" ></i></a>';
        }
        ?>
    </div>

    <!-- Display welcome and logout button for medium screens and larger -->
    <nav class="d-none d-md-block mt-3">
        <a href="index1.php" class="btn mx-2" style="background-color:#b48712;margin-top:10px;">Home</a>
        <a href="contact.php" class="btn mx-2" style="background-color:#b48712;margin-top:10px;">Contact</a>
        <?php 
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            echo '<span style="padding-right: 20px; color: blue;">Welcome, '.$username.'</span>';
            echo '<a href="../user/logout.php" class="btn btn-danger"><i class="fa fa-right-from-bracket"></i> Logout</a>';
        } else {
            echo '<a href="../user/login.php" class="btn mx-3 p-2" style="background-color:#b48712;margin-top:10px;"><i class="fas fa-right-to-bracket" ></i></a>';
        }
        ?>
    </nav>
</header>
