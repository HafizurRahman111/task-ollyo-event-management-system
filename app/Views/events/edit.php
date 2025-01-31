<style>
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .form-column {
        flex: 1;
        min-width: 280px;
    }

    .submit-btn {
        text-align: right;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .form-column {
            flex: 1 0 100%;
        }
    }
</style>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h2 class="mb-0">Edit Event: <?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    </div>
    <div class="card-body">
        <form action="<?= BASE_URL ?>events/update/<?= $event['id'] ?>" method="POST" novalidate>
            <!-- Event Name -->
            <div class="form-group">
                <label for="event-name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="event-name" name="name"
                    value="<?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>" required minlength="5"
                    maxlength="250">
                <div class="invalid-feedback" id="name-error">
                    <?= $errors['name'] ?? '' ?>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="event-description" class="form-label">Description</label>
                <textarea class="form-control" id="event-description" name="description" rows="3" required
                    placeholder="Enter event description"><?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="invalid-feedback" id="description-error">
                    <?= $errors['description'] ?? '' ?>
                </div>
            </div>

            <!-- Max Capacity -->
            <div class="form-group">
                <label for="event-max-capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="event-max-capacity" name="max_capacity"
                    value="<?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8') ?>" required min="1">
                <div class="invalid-feedback" id="max-capacity-error">
                    <?= $errors['max_capacity'] ?? '' ?>
                </div>
            </div>

            <!-- Start Date -->
            <div class="form-group">
                <label for="event-start-date" class="form-label">Start Date</label>
                <input type="datetime-local" class="form-control" id="event-start-date" name="start_datetime"
                    value="<?= htmlspecialchars($event['start_datetime'], ENT_QUOTES, 'UTF-8') ?>" required>
                <div class="invalid-feedback" id="start-date-error">
                    <?= $errors['start_datetime'] ?? '' ?>
                </div>
            </div>

            <!-- End Date -->
            <div class="form-group">
                <label for="event-end-date" class="form-label">End Date</label>
                <input type="datetime-local" class="form-control" id="event-end-date" name="end_datetime"
                    value="<?= htmlspecialchars($event['end_datetime'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="invalid-feedback" id="end-date-error">
                    <?= $errors['end_datetime'] ?? '' ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        const today = new Date().toISOString().slice(0, 16);
        $('#event-start-date, #event-end-date').attr('min', today);

        $('#event-start-date').on('input', function () {
            $('#event-end-date').attr('min', $(this).val());
        });

        $('#edit-event-form').on('submit', function (e) {
            e.preventDefault();
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            const formData = new FormData(this);

            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            $.ajax({
                url: '<?= BASE_URL ?>events/update/<?= $event['id'] ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Event Updated!',
                            text: 'The event has been successfully updated.',
                            confirmButtonText: 'OK'
                        }).then(() => window.location.href = '<?= BASE_URL ?>events/view/<?= $event['id'] ?>');
                    } else {
                        if (response.errors) {
                            displayErrors(response.errors);
                        } else {
                            showError('Failed to update the event.');
                        }
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        displayErrors(response.errors);
                    } else {
                        showError('An unexpected error occurred while updating the event.');
                    }
                }
            });
        });

        function displayErrors(errors) {
            for (const [field, message] of Object.entries(errors)) {
                $(`#${field}-error`).text(message);
                $(`[name=${field}]`).addClass('is-invalid');
            }
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    });
</script>