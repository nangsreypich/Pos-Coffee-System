<?php include('head.php') ?>
<style>
    .contact4 {
        font-family: "Inter", sans-serif;
        font-weight: 300;
    }

    .contact4 h1,
    .contact4 h2,
    .contact4 h3,
    .contact4 h4,
    .contact4 h5,
    .contact4 h6 {
        color: #3e4555;
    }

    .contact4 .font-weight-medium {
        font-weight: 500;
    }

    .contact4 .form-control {
        background: #ffffff;
        border-color: #000000;
    }

    .contact4 .form-control:focus {
        border-color: #000000;
    }

    .contact4 input::-webkit-input-placeholder,
    .contact4 textarea::-webkit-input-placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .contact4 input:-ms-input-placeholder,
    .contact4 textarea:-ms-input-placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .contact4 input::placeholder,
    .contact4 textarea::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .contact4 .right-image {
        position: absolute;
        right: 0;
        bottom: 0;
        top: 0;
    }

    .contact4.bg-info {
        border-radius: 5px;
        /* background-color: #0000ff !important; */
    }

    .contact4 .text-inverse {
        color: #3e4555 !important;
    }

    @media (min-width: 1024px) {
        .contact4 .contact-box {
            padding: 80px 105px 80px 0px;
        }
    }

    @media (max-width: 767px) {
        .contact4 .contact-box {
            padding-left: 15px;
            padding-right: 15px;
        }
    }

    @media (max-width: 1023px) {
        .contact4 .right-image {
            position: relative;
            bottom: -95px;
        }
    }

    .opening-hours {
        display: flex;
        flex-direction: column;
        margin-left: 20px;
    }

    .opening-hours-item {
        margin-bottom: 10px;
    }

    .opening-hours-title {
        font-weight: 500;
        margin-bottom: 5px;
    }

    .opening-hours-time {
        font-weight: 400;
    }
</style>

<form id="signOutForm" action="logout.php" method="post">
    <?php include('header_web.php') ?>

    <div class="container mt-5">
        <div class=" contact4 overflow-hiddedn position-relative">
            <!-- Row  -->
            <div class="row no-gutters">
                <div class="container">
                    <div class="col-lg-6 contact-box mb-4 mb-md-0 mx-3">
                        <div class="">
                            <h1 class="title " style="color: #145cff;">Contact Us</h1>
                            <form class="mt-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group mt-2">
                                            <label for="name" style=" font-weight:500" class="mx-3">Our Facebook Page </label>
                                            <a class="link-body-emphasis mx-2" href="https://web.facebook.com/rtcbattambang" target="_blank">
                                                <i class="fa-brands fa-facebook"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mt-2">
                                            <label for="phone" style=" font-weight:500;" class="mx-3">Contact </label>
                                            <p style=" font-weight:400">+855 17 480 762 </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mt-2">
                                            <label for="phone" style=" font-weight:500" class="mx-3">Location </label>
                                            <p style=" font-weight:400">Kamakor Village, Sangkat Svaypor, Krunng Battambang, Battambang, Cambodia </p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mt-2">
                                            <label for="phone" style="font-weight: 500" class="mx-3">Working Hours</label>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="hoursDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Morning
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="hoursDropdown">
                                                    <a class="dropdown-item" href="#" onclick="selectMorning()">Morning</a>
                                                    <a class="dropdown-item" href="#" onclick="selectAfternoon()">Afternoon</a>
                                                </div>
                                            </div>
                                            <div id="openingHours" class="opening-hours mt-3">
                                                <div id="morningHours" class="opening-hours-item">
                                                    <p class="opening-hours-time"><i class="fas fa-circle" style="color: green;"></i> 8:00 AM - 11:00 AM</p>
                                                </div>
                                                <div id="afternoonHours" class="opening-hours-item" style="display: none;">
                                                    <p class="opening-hours-time"><i class="fas fa-circle" style="color: green;"></i> 1:30 PM - 5:00 PM</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function selectMorning() {
                                            document.getElementById("hoursDropdown").innerText = "Morning";
                                            document.getElementById("morningHours").style.display = "block";
                                            document.getElementById("afternoonHours").style.display = "none";
                                        }

                                        function selectAfternoon() {
                                            document.getElementById("hoursDropdown").innerText = "Afternoon";
                                            document.getElementById("morningHours").style.display = "none";
                                            document.getElementById("afternoonHours").style.display = "block";
                                        }
                                    </script>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 right-image p-10 my-2 mt-5">
                    <div class="row mx-2 my-2">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2570.3692000003603!2d103.19732130824899!3d13.104729987169689!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3105498b07e7f707%3A0xfa913c97d5ff8de8!2sRegional%20Polytechnic%20Institute%20Techo%20Sen%20Battambang!5e1!3m2!1sen!2skh!4v1718634001667!5m2!1sen!2skh" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php include_once('footer.php') ?>
    </div>
</form>
<script>
    function disableRightClick(event) {
            // Prevent the default right-click behavior
            event.preventDefault();
            }

            // Add event listener to disable right-click on the document
            document.addEventListener('contextmenu', disableRightClick);
</script>