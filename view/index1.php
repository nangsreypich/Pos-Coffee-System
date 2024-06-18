<?php include('head.php') ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<style>
    .card-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 180px;
        /* Adjusted for smaller card */
    }

    .card-text {
        font-size: 12px;
        /* Adjusted for smaller card */
    }

    .card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-wrapper {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 10px;
        font-family: 'Inter', sans-serif;
    }

    .card-text strong {
        font-size: 14px;
        /* Adjusted for smaller card */
        color: #333;
    }

    .card-text {
        color: #555;
        font-size: 12px;
        /* Adjusted for smaller card */
        margin: 0;
    }

    .card-text.teacher-info {
        color: #0000ff;
        font-size: 12px;
        /* Adjusted for smaller card */
        margin-top: 5px;
        display: flex;
        flex-direction: column;
    }

    .card .btn-group .btn {
        background-color: #ff0000;
        border-color: #ff0000;
        transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out;
    }

    .card .btn-group .btn:hover {
        background-color: #cc0000;
        border-color: #cc0000;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(20);
        width: 3rem;
        height: 2.5rem;
    }

    .card-wrapper {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-wrapper .teacher-info {
        font-size: 12px;
        /* Adjusted for smaller card */
        color: #666666;
    }

    .h-100 {
        height: 85% !important;
        /* Adjusted for smaller card */
    }

    .card-img-top {
        width: 100%;
        height: auto;
        max-height: 200px;
        /* Adjusted for smaller card */
        object-fit: contain;
        border-bottom: 1px solid #eee;
    }

    .btn-group .btn {
        font-size: 13px;
        /* Adjusted for smaller category buttons */
    }

    @media (max-width: 768px) {
        .carousel-item .col-6 {
            display: none;
        }

        .carousel-item .col-6:nth-child(-n+4) {
            display: block;
        }
    }
</style>

<form id="signOutForm" action="logout.php" method="post">
    <?php include('header_web.php') ?>

    <div class="container mt-3">
        <div id="carouselExampleIndicators" class="carousel slide">
            <div class="carousel-indicators">
                <!--<button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>-->
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../image/COVER_WEP.jpg" class="d-block w-100" alt="carousel-1">
                </div>
                <!--<div class="carousel-item">-->
                <!--    <img src="../image/COVER WEP09000.jpg" class="d-block w-100" alt="carousel-2">-->
                <!--</div>-->
            </div>
            <!--<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">-->
            <!--    <span class="carousel-control-prev-icon" aria-hidden="true"></span>-->
            <!--    <span class="visually-hidden">Previous</span>-->
            <!--</button>-->
            <!--<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">-->
            <!--    <span class="carousel-control-next-icon" aria-hidden="true"></span>-->
            <!--    <span class="visually-hidden">Next</span>-->
            <!--</button>-->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myCarousel = document.querySelector('#carouselExampleIndicators');
            var carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000,
                wrap: true,
                pause: 'hover',
                keyboard: false,
                ride: 'carousel'
            });
        });
    </script>

    <section class="mt-3 py-2 text-center container">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-light title" style="font-size: 25px;font-weight: bold;">Welcome to our coffee shop </h1>
                <p class="lead description" style="font-size: 14pt;">Third Coffee Shop Pos System</p>
                <p>
                    <a href="contact.php" class="btn btn-secondary my-2">Contact us</a>
                </p>
            </div>
        </div>
    </section>

    <div class="container mt-5">
        <?php
        include_once('../controller/connection.php');

        // Query to fetch drinks with categories
        $query = "SELECT drink.name, drink.image, drink.price, category.name AS category_name 
                  FROM drink 
                  JOIN category ON drink.cat_id = category.id";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        function generateCarousel($drinks, $category = 'All')
        {
            echo '<div class="btn-group mb-5" role="group" aria-label="Drink categories">';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'All\')">All</button>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'MilkTea\')">MilkTea</button>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'Hot Coffee\')">Hot Coffee</button>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'Iced Coffee\')">Iced Coffee</button>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'Fast Food\')">Fast Food</button>';
            echo '<button type="button" class="btn btn-outline-secondary" onclick="filterDrinks(\'Frapped\')">Frapped</button>';
            echo '</div>';

            echo '<div id="drink-carousel" class="carousel slide">';
            echo '<div class="carousel-inner">';

            // Display All drinks initially
            $filteredDrinks = $drinks;

            $filteredDrinks = array_values($filteredDrinks);
            $num_drinks = count($filteredDrinks);
            $num_rows = ceil($num_drinks / 4); // Calculate number of rows needed

            for ($row_index = 0; $row_index < $num_rows; $row_index++) {
                echo '<div class="carousel-item' . ($row_index === 0 ? ' active' : '') . '">';
                echo '<div class="row row-cols-1 row-cols-md-4 g-3">';

                for ($i = $row_index * 4; $i < min(($row_index + 1) * 4, $num_drinks); $i++) {
                    $drink = $filteredDrinks[$i];
        ?>
                    <div class="col-6 col-md-3 d-md-block d-lg-block drink-item" data-category="<?php echo $drink['category_name']; ?>">
                        <a href="#" style="text-decoration: none;">
                            <div class="card shadow-sm h-100 mt-2" style="border-radius: 10px; overflow: hidden;">
                                <div style="overflow: hidden; border-radius: 10px;">
                                    <img src="<?php echo $drink['image']; ?>" class="card-img-top" style="border-radius: 10px;" alt="<?php echo $drink['name']; ?>">
                                </div>
                                <div class="card-body">
                                    <div class="card-wrapper">
                                        <p class="card-text"><strong><?php echo $drink['name']; ?></strong></p>
                                        <p class="card-text teacher-info"><?php echo $drink['category_name']; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm text-white" style="background-color:#b48712;border:none;">$<?php echo $drink['price']; ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
        <?php
                }

                echo '</div>';
                echo '</div>';
            }

            echo '</div>';
            echo '<button class="carousel-control-prev" type="button" data-bs-target="#drink-carousel" data-bs-slide="prev">';
            echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
            echo '<span class="visually-hidden">Previous</span>';
            echo '</button>';
            echo '<button class="carousel-control-next" type="button" data-bs-target="#drink-carousel" data-bs-slide="next">';
            echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
            echo '<span class="visually-hidden">Next</span>';
            echo '</button>';
            echo '</div>';
        }

        generateCarousel($drinks);
        ?>
    </div>

    <script>
        var allDrinks = <?php echo json_encode($drinks); ?>; // Preserve All drinks data

function filterDrinks(category) {
    var carouselInner = document.querySelector('#drink-carousel .carousel-inner');
    carouselInner.innerHTML = '';

    var filteredDrinks;
    if (category === 'All') {
        filteredDrinks = allDrinks; // Show All drinks from preserved data
    } else {
        filteredDrinks = allDrinks.filter(function(drink) {
            return drink.category_name === category;
        });
    }

    // Generate carousel items based on filtered drinks
    var numDrinks = filteredDrinks.length;
    var numRows = Math.ceil(numDrinks / 4);

    for (var rowIndex = 0; rowIndex < numRows; rowIndex++) {
        var row = document.createElement('div');
        row.className = 'carousel-item' + (rowIndex === 0 ? ' active' : '');
        var rowContent = '<div class="row row-cols-1 row-cols-md-4 g-3">';

        for (var i = rowIndex * 4; i < Math.min((rowIndex + 1) * 4, numDrinks); i++) {
            var drink = filteredDrinks[i];
            rowContent += '<div class="col-6 col-md-3 d-md-block d-lg-block drink-item" data-category="' + drink.category_name + '">' +
                '<a href="#" style="text-decoration: none;">' +
                '<div class="card shadow-sm h-100 mt-2" style="border-radius: 10px; overflow: hidden;">' +
                '<div style="overflow: hidden; border-radius: 10px;">' +
                '<img src="' + drink.image + '" class="card-img-top" style="border-radius: 10px;" alt="' + drink.name + '">' +
                '</div>' +
                '<div class="card-body">' +
                '<div class="card-wrapper">' +
                '<p class="card-text"><strong>' + drink.name + '</strong></p>' +
                '<p class="card-text teacher-info">' + drink.category_name + '</p>' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<div class="btn-group">' +
                '<button type="button" class="btn btn-sm text-white" style="background-color:#b48712;border:none;">$' + drink.price + '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</a>' +
                '</div>';
        }

        rowContent += '</div>';
        row.innerHTML = rowContent;
        carouselInner.appendChild(row);
    }

    updateCarouselControls();
}

// Initially display All drinks
filterDrinks('All');

// Event listener for category buttons
document.querySelectorAll('.btn-group .btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var category = this.textContent.trim(); // Get the category name from button text
        filterDrinks(category);
    });
});

function updateCarouselControls() {
    var carousel = document.querySelector('#drink-carousel');
    var activeIndex = Array.from(carousel.querySelectorAll('.carousel-item')).findIndex(item => item.classList.contains('active'));
    var visibleItems = Array.from(carousel.querySelectorAll('.carousel-item')).filter(item => item.style.display !== 'none');
    var prevControl = carousel.querySelector('.carousel-control-prev');
    var nextControl = carousel.querySelector('.carousel-control-next');

    prevControl.style.display = (activeIndex <= 0) ? 'none' : 'block';
    nextControl.style.display = (activeIndex >= visibleItems.length - 1) ? 'none' : 'block';
}

// Add event listener for carousel slide event
var drinkCarousel = document.querySelector('#drink-carousel');
drinkCarousel.addEventListener('slid.bs.carousel', updateCarouselControls);

// Update carousel display on window resize
window.addEventListener('resize', function() {
    filterDrinks(document.querySelector('.btn-group .btn.active').textContent.trim());
});

// Initial update of carousel controls
updateCarouselControls();

    </script>

    <?php include('footer.php') ?>
</form>

</html>