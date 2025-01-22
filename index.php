<?php
require_once 'pages/home/header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

?>

<main class="container main-content">
    <div class="text-center">
        <h1>Welcome to Event Management</h1>
        <p class="lead">Plan, organize, and manage your events seamlessly with our system.</p>
    </div>

    <div class="features text-center">
        <h2>Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4">
                <i class="fa-solid fa-calendar-check feature-icon"></i>
                <h4>Easy Event Management</h4>
                <p>Create and manage events effortlessly with our intuitive tools.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-users feature-icon"></i>
                <h4>Join the Community</h4>
                <p>Connect with other attendees and make your events memorable.</p>
            </div>
            <div class="col-md-4">
                <i class="fa-solid fa-download feature-icon"></i>
                <h4>Download Reports</h4>
                <p>Admins can download detailed event and attendee reports with ease.</p>
            </div>
        </div>
    </div>

    <div class="testimonials">
        <h2>What Our Users Say</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>"This platform has made event planning a breeze. I love how easy it is to create events and
                            track registrations."</p>
                        <p class="text-muted">- John Doe</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>"The community feature is fantastic! I've connected with so many interesting people through
                            this platform."</p>
                        <p class="text-muted">- Jane Smith</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p>"The reporting feature is incredibly helpful for analyzing event performance. It gives me
                            valuable insights."</p>
                        <p class="text-muted">- David Lee</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php

require_once 'pages/home/footer.php';
?>