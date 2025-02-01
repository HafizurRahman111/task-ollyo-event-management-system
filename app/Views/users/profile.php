<div class="card shadow-lg mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">User Profile</h3>
    </div>
    <div class="card-body">
        <?php if ($user): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>ID:</strong>
                        <p class="mb-0"><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Full Name:</strong>
                        <p class="mb-0"><?= htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Role:</strong>
                        <p class="mb-0"><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Created At:</strong>
                        <p class="mb-0"><?= date('d-m-Y H:i:s', strtotime($user['created_at'])) ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Updated At:</strong>
                        <p class="mb-0"><?= date('d-m-Y H:i:s', strtotime($user['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-danger">User not found.</p>
        <?php endif; ?>
    </div>
</div>