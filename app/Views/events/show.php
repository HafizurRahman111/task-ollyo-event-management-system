<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0"><?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p><strong>Description:</strong></p>
                <p><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>Max Capacity:</strong>
                    <?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Start Date:</strong>
                    <?php
                    $startDateTime = new DateTime($event['start_datetime']);
                    echo $startDateTime->format('d-m-Y, h:i A');
                    ?>
                </p>
                <p><strong>End Date:</strong>
                    <?php
                    $endDateTime = new DateTime($event['end_datetime']);
                    echo $endDateTime->format('d-m-Y, h:i A');
                    ?>
                </p>

                <?php if ($userRole == 'admin'): ?>
                    <p><strong>Created By:</strong>
                        <?= htmlspecialchars($event['created_by_name'], ENT_QUOTES, 'UTF-8') ?>[<?= htmlspecialchars($event['created_by'], ENT_QUOTES, 'UTF-8') ?>]
                    </p>
                <?php endif; ?>

                <p><strong>Created At:</strong>
                    <?php
                    $createdAt = new DateTime($event['created_at']);
                    echo $createdAt->format('d-m-Y, h:i A');
                    ?>
                </p>
                <p><strong>Updated At:</strong>
                    <?php
                    if ($event['updated_at']) {
                        $updatedAt = new DateTime($event['updated_at']);
                        echo $updatedAt->format('d-m-Y, h:i A');
                    } else {
                        echo "N/A";
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <a href="<?= BASE_URL ?>events" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>
</div>