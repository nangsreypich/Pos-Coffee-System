<header class="d-flex justify-content-between py-3 border-bottom">
    <div class="d-flex align-items-center">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <img src="../image/Logo ASSIGMENT.png" alt="" class="bi me-2" width="70" height="70">
            <span class="fs-5">Third Coffee Shop</span>
        </a>
    </div>
    <!-- Menu toggle button -->
    <div>
        <button class="btn btn-primary d-md-none mt-3" id="menuToggle"><i class="fas fa-bars"></i></button>
    </div>
    <div class="d-none d-md-block mt-3"> <!-- Display only on medium and larger screens -->
        <?php 
        // Check if $username is set (based on session data)
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            echo '<span style="padding-right: 20px; color: blue;">Welcome, '.$username.'</span>';
            echo '<a href="../user/logout.php" class="btn btn-danger"><i class="fa fa-right-from-bracket"></i> Logout</a>';
        } else {
            echo '<a href="../user/login.php" class="btn " style="background-color:#b48712;">Login</a>';
        }
        ?>
    </div>
</header>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var menuToggle = document.getElementById('menuToggle');
        var sidebar = document.getElementById('sidebar');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('d-none'); // Toggle display of the sidebar
                // Add or remove a class to the main content area to adjust its width
                document.getElementById('mainContent').classList.toggle('col-md-10');
                document.getElementById('mainContent').classList.toggle('col-md-12');
            });
        }
    });
</script>
