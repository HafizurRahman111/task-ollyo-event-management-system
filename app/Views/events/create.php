<div class="container">
    <h2>Create Event</h2>

    <div id="general-error" class="alert alert-danger d-none"></div>

    <form id="event-form">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Event Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required minlength="5"
                        maxlength="250">
                    <div class="invalid-feedback" id="name-error"></div>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="5" required minlength="10"
                        maxlength="1000"></textarea>
                    <div class="invalid-feedback" id="description-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="max_capacity">Maximum Capacity <span class="text-danger">*</span></label>
                    <input type="number" id="max_capacity" name="max_capacity" class="form-control" required min="1">
                    <div class="invalid-feedback" id="max_capacity-error"></div>
                </div>

                <div class="form-group">
                    <label for="start_datetime">Start Date and Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" class="form-control"
                        required>
                    <div class="invalid-feedback" id="start_datetime-error"></div>
                </div>

                <div class="form-group">
                    <label for="end_datetime">End Date and Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" class="form-control" required>
                    <div class="invalid-feedback" id="end_datetime-error"></div>
                </div>
            </div>
        </div>

        <div class="form-group d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Create Event</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const eventForm = document.getElementById('event-form');
        const errorBox = document.getElementById('general-error');

        document.getElementById('start_datetime').addEventListener('change', function () {
            document.getElementById('end_datetime').setAttribute('min', this.value);
        });

        eventForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            errorBox.classList.add('d-none');
            errorBox.textContent = '';
            eventForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            eventForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

            const errors = validateForm();
            if (errors.length > 0) {
                displayErrors(errors);
                return;
            }

            const submitButton = eventForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            const formData = new FormData(eventForm);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= BASE_URL . "events/create"; ?>', true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);

                    if (data.success) {
                        window.location.href = '<?= BASE_URL . "events"; ?>';
                    } else {
                        displayErrors(data.errors || ['Event creation failed.']);
                    }
                } else {
                    console.error('Error during form submission:', xhr.statusText);
                    displayErrors(['An unexpected error occurred. Please try again.']);
                }
                submitButton.disabled = false;
            };

            xhr.onerror = function () {
                console.error('Error during form submission:', xhr.statusText);
                displayErrors(['An unexpected error occurred. Please try again.']);
                submitButton.disabled = false;
            };

            xhr.send(formData);
        });

        const validateForm = () => {
            const errors = [];

            const name = document.getElementById('name').value.trim();
            if (name.length < 5 || name.length > 250) {
                errors.push({ field: 'name', message: 'Event name must be between 5 and 250 characters.' });
            }

            const description = document.getElementById('description').value.trim();
            if (description.length < 10 || description.length > 1000) {
                errors.push({ field: 'description', message: 'Event description must be between 10 and 1000 characters.' });
            }

            const maxCapacity = document.getElementById('max_capacity').value.trim();
            if (!maxCapacity || maxCapacity <= 0) {
                errors.push({ field: 'max_capacity', message: 'Maximum capacity must be a positive integer.' });
            }

            const startDatetime = document.getElementById('start_datetime').value.trim();
            if (!startDatetime) {
                errors.push({ field: 'start_datetime', message: 'Please enter a valid start date and time.' });
            }

            const endDatetime = document.getElementById('end_datetime').value.trim();
            if (!endDatetime) {
                errors.push({ field: 'end_datetime', message: 'Please enter a valid end date and time.' });
            } else if (new Date(endDatetime) <= new Date(startDatetime)) {
                errors.push({ field: 'end_datetime', message: 'End date and time must be after the start date and time.' });
            }

            return errors;
        };

        const displayErrors = (errors) => {
            errors.forEach(error => {
                const input = document.getElementById(error.field);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.getElementById(`${error.field}-error`);
                    if (feedback) {
                        feedback.textContent = error.message;
                    }
                }
            });

            if (errors.length > 0) {
                errorBox.textContent = errors.map(error => error.message).join(' ');
                errorBox.classList.remove('d-none');
            }
        };
    });
</script>