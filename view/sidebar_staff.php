<style>
    #sidebar {
        background-color: #f8f9fa;
        padding: 20px;
        height: 100vh;
    }

    @media (max-width: 767.98px) {
        #sidebar {
            height: auto;
        }
    }

    #sidebar .list-group .list-unstyled {
        margin-bottom: 15px;
    }

    #sidebar .list-group .list-unstyled a {
        color: #b48712;
        font-weight: bold;
        padding: 10px 15px;
        display: block;
        transition: background-color 0.3s ease;
    }

    #sidebar .list-group .list-unstyled a:hover {
        background-color: #e2e6ea;
        border-radius: 5px;
    }

    #sidebar .dropdown-menu {
        background-color: #f8f9fa;
    }

    #sidebar .dropdown-menu .dropdown-item {
        color: #b48712;
    }

    #sidebar .dropdown-menu .dropdown-item:hover {
        background-color: #e2e6ea;
    }

    #sidebar .dropdown-toggle::after {
        color: #b48712;
    }

    .d-md-none span {
        display: block;
        margin-bottom: 10px;
    }

    .d-md-none .btn {
        margin-bottom: 10px;
    }
</style>
<div class="col-md-2 d-none d-md-block" id="sidebar">
    <!-- Username and login button -->
    <div class="d-md-none" style="display: flex; align-items: center;">
        <?php
        // Check if $username is set (based on session data)
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
            echo '<span style="color: blue; margin-right: 10px;">Welcome ' . $username . '</span>';
        }

        // Check if $isLogin is set (based on session or cookie data)
        if (isset($_COOKIE["isLogin"])) {
            $isLogin = $_COOKIE["isLogin"];
            if ($isLogin == false) {
                echo '<a href="../user/login.php" class="btn btn-success" style="margin-left: 10px;">Login</a>';
            } else {
                echo '<a href="../user/login.php" class="btn btn-danger" style="margin-left: 10px;"><i class="fa fa-right-from-bracket"></i></a>';
            }
        } else {
            // If $isLogin is not set, provide a default behavior (e.g., show login button)
            echo '<a href="../user/login.php" class="btn btn-danger" style="margin-left: 10px;"><i class="fa fa-right-from-bracket"></i></a>';
        }
        ?>
    </div>

    <!-- Sidebar menu -->
    <ul class="list-group">
        <li class="list-unstyled"><a href="dashboard_staff.php" class="text-decoration-none"> <i class="fa fa-gauge"></i> Dashboard</a></li>
        <li class="list-unstyled"><a href="tables.php" class="text-decoration-none"><i class="fa fa-table"></i> Tables</a></li>
        <li class="list-unstyled"><a href="categorys.php" class="text-decoration-none"><i class="fa fa-list"></i> Categorys</a></li>
        <li class="list-unstyled dropdown">
            <a href="#" class="text-decoration-none dropdown-toggle" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-cart-shopping"></i> Sales
            </a>
            <ul class="dropdown-menu" aria-labelledby="staffDropdown">
                <li><a class="dropdown-item" href="add_sale.php">Add Sales</a></li>
                <li><a class="dropdown-item" href="all_sale.php">All Sales</a></li>
            </ul>
        </li>
        <li class="list-unstyled dropdown">
            <a href="#" class="text-decoration-none dropdown-toggle" id="staffDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-mug-saucer"></i> Drinks
            </a>
            <ul class="dropdown-menu" aria-labelledby="staffDropdown">
                <li><a class="dropdown-item" href="add_drink.php">Add Drink</a></li>
                <li><a class="dropdown-item" href="all_drink.php">All Drinks</a></li>
            </ul>
        </li>
        <li class="list-unstyled"><a href="stock.php" class="text-decoration-none"><i class="fa fa-cubes-stacked"></i> Stock</a></li>
        <li class="list-unstyled dropdown">
            <a href="#" class="text-decoration-none dropdown-toggle" id="reportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-paste"></i> Report
            </a>
            <ul class="dropdown-menu" aria-labelledby="reportDropdown">
                <li><a class="dropdown-item" href="revenue_report.php">Revenue</a></li>
                <li><a class="dropdown-item" href="expense_report.php">Expense</a></li>
            </ul>
        </li>
        <li class="list-unstyled"><a href="users.php" class="text-decoration-none"><i class="fa fa-gear"></i> Users</a></li>
    </ul>
</div>