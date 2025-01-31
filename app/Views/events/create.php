<style>
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-row {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        min-height: 80px;
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

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Create New Event</h2>
        </div>
        <div class="card-body">
            <form id="create-event-form" novalidate>
                <div class="form-row">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="event-name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="event-name" name="name"
                                placeholder="Enter event name" required minlength="5" maxlength="250">
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="event-max-capacity" class="form-label">Max Capacity</label>
                            <input type="number" class="form-control" id="event-max-capacity" name="max_capacity"
                                placeholder="Enter max capacity" min="1" required>
                            <div class="invalid-feedback" id="max-capacity-error"></div>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="event-start-date" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" id="event-start-date"
                                name="start_datetime" required>
                            <div class="invalid-feedback" id="start-date-error"></div>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="event-end-date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="event-end-date" name="end_datetime">
                            <div class="invalid-feedback" id="end-date-error"></div>
                        </div>
                    </div>
                </div>

                <div class="form-column">
                    <div class="form-group">
                        <label for="event-description" class="form-label">Description</label>
                        <textarea class="form-control" id="event-description" name="description" rows="3"
                            placeholder="Enter event description" required></textarea>
                        <div class="invalid-feedback" id="description-error"></div>
                    </div>
                </div>

                <div class="submit-btn">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Set minimum date for start and end date fields
        const today = new Date().toISOString().slice(0, 16);
        $('#event-start-date, #event-end-date').attr('min', today);

        // Sync start and end date time
        $('#event-start-date').on('input', function () {
            $('#event-end-date').attr('min', $(this).val());
        });

        // Form submission
        $('#create-event-form').on('submit', function (e) {
            e.preventDefault();
            $('.invalid-feedback').text(''); // Clear previous errors
            $('.form-control').removeClass('is-invalid'); // Remove invalid class

            const formData = new FormData(this); // Use FormData for cleaner data handling

            // Validate form inputs
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }

            // AJAX request to submit form data
            $.ajax({
                url: '<?= BASE_URL ?>events/create',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Event Created!',
                            text: 'The event has been successfully created.',
                            confirmButtonText: 'OK'
                        }).then(() => window.location.href = '<?= BASE_URL ?>events');
                    } else {
                        if (response.errors) {
                            displayErrors(response.errors);
                        } else {
                            showError('Failed to create the event.');
                        }
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        displayErrors(response.errors);
                    } else {
                        showError('An unexpected error occurred while creating the event.');
                    }
                }
            });
        });

        // Display server-side errors
        function displayErrors(errors) {
            for (const [field, message] of Object.entries(errors)) {
                $(`#${field}-error`).text(message);
                $(`[name=${field}]`).addClass('is-invalid'); // Add Bootstrap's invalid class
            }
        }

        // Error popup
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
</body>

</html>