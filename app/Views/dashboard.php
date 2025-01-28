<div class="container-fluid">
    <div class="row">
        <!-- Event Card -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card shadow border-left-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Events</h5>
                        <p class="card-text">Total Events</p>
                        <h3 class="display-4"><?= isset($eventCount) ? $eventCount : 'N/A'; ?></h3>
                    </div>
                    <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                </div>
            </div>
        </div>

        <!-- Attendees Card -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card shadow border-left-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Attendees</h5>
                        <p class="card-text">Total Attendees</p>
                        <h3 class="display-4"><?= isset($attendeeCount) ? $attendeeCount : 'N/A'; ?></h3>
                    </div>
                    <i class="fas fa-users fa-3x text-success"></i>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card shadow border-left-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Total Users</p>
                        <h3 class="display-4"><?= isset($userCount) ? $userCount : 'N/A'; ?></h3>
                    </div>
                    <i class="fas fa-users-cog fa-3x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>