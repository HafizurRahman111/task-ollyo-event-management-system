<!-- Hero Section -->
<section class="hero" style="background-image: url('https://via.placeholder.com/1920x1080');">
    <div class="container text-center hero-content">
        <h1>Welcome to EMS <br /> Event Management System</h1>
        <p>Plan, organize, and manage your events seamlessly.</p>
        <a href="<?php echo BASE_URL . 'auth/register'; ?>" class="btn btn-primary btn-lg" role="button">Get Started</a>
    </div>
</section>

<!-- Features Section -->
<section class="features text-center mb-5">
    <h2 class="section-title text-center mb-4">Why Choose Us?</h2>
    <div class="row mt-4">
        <div class="col-md-4">
            <i class="fa-solid fa-calendar-check feature-icon mb-3"></i>
            <h4>Easy Event Management</h4>
            <p>Create and manage events effortlessly with our intuitive tools.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-users feature-icon mb-3"></i>
            <h4>Join the Community</h4>
            <p>Connect with other attendees and make your events memorable.</p>
        </div>
        <div class="col-md-4">
            <i class="fa-solid fa-download feature-icon mb-3"></i>
            <h4>Download Reports</h4>
            <p>Admins can download detailed event and attendee reports with ease.</p>
        </div>
    </div>
</section>

<!-- Featured Events Section -->
<section class="featured-events py-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Featured Events</h2>
        <div class="row">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="event-card shadow-sm rounded">
                            <div class="event-info p-3">
                                <h3 class="event-name"><?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="event-description">
                                    <?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <p><strong>Max Capacity:</strong>
                                    <?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p><strong>Start Time:</strong> <?= date('F j, Y, g:i a', strtotime($event['start_time'])); ?>
                                </p>
                                <p><strong>End Time:</strong> <?= date('F j, Y, g:i a', strtotime($event['end_time'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center w-100">No events available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>