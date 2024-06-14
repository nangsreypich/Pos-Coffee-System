<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php 
        // Check if $username is set (based on session data)
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            echo '<span class="d-block mb-3" style="color: blue;">Welcome to '.$username.'</span>';
        }
        
        // Check if $isLogin is set (based on session or cookie data)
        if (isset($_COOKIE["isLogin"])) {
            $isLogin = $_COOKIE["isLogin"];
            if ($isLogin == false) {
                echo '<a href="../user/login.php" class="btn btn-success d-block mb-3"><i class="fa-solid fa-left-to-bracket"></i></a>';
            } else {
                echo '<a href="../user/logout.php" class="btn btn-danger d-block mb-3"><i class="fa-solid fa-right-to-bracket"></i></a>';
            }
        } else {
            // If $isLogin is not set, provide a default behavior (e.g., show login button)
            echo '<a href="../user/login.php" class="btn btn-success d-block mb-3"><i class="fa-solid fa-left-to-bracket"></i></a>';
        }
        ?>
        <ul class="list-group">
            <li class="list-group-item"><a href="index.php" class="text-decoration-none">All Librarian</a></li>
            <li class="list-group-item"><a href="add_librarian.php" class="text-decoration-none">Add Librarian</a></li>
            <li class="list-group-item"><a href="all_book.php" class="text-decoration-none">All Book</a></li>
            <li class="list-group-item"><a href="add_book.php" class="text-decoration-none">Add Book</a></li>
            <li class="list-group-item"><a href="all_borrow.php" class="text-decoration-none">All Book Borrow</a></li>
            <li class="list-group-item"><a href="add_borrow.php" class="text-decoration-none">Add Book Borrow</a></li>
        </ul>
    </div>
</div>
