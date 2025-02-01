<div class="container">

    <h2 class="mb-0">Edit Event</h2>

    <div id="general-error" class="alert alert-danger d-none"></div>

    <form id="event-form" action="<?= BASE_URL ?>events/update/<?= $event['id'] ?>" method="POST" novalidate>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>" required minlength="5"
                        maxlength="250">
                    <div class="invalid-feedback" id="name-error"></div>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required minlength="10"
                        maxlength="1000"><?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="invalid-feedback" id="description-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="capacity" class="form-label">Max Capacity</label>
                    <input type="number" class="form-control" id="max-capacity" name="max_capacity"
                        value="<?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8') ?>" required min="0">
                    <div class="invalid-feedback" id="max-capacity-error"></div>
                </div>
                <div class="form-group">
                    <label for="start-date" class="form-label">Start Date</label>
                    <input type="datetime-local" class="form-control" id="start-date" name="start_datetime"
                        value="<?= htmlspecialchars($event['start_datetime'], ENT_QUOTES, 'UTF-8') ?>" required>
                    <div class="invalid-feedback" id="start-date-error"></div>
                </div>
                <div class="form-group">
                    <label for="end-date" class="form-label">End Date</label>
                    <input type="datetime-local" class="form-control" id="end-date" name="end_datetime"
                        value="<?= htmlspecialchars($event['end_datetime'], ENT_QUOTES, 'UTF-8') ?>" required>
                    <div class="invalid-feedback" id="end-date-error"></div>
                </div>
            </div>
        </div>
        <div class="form-group d-flex justify-content-end">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventForm = document.getElementById('edit-event-form');
        const errorBox = document.getElementById('general-error');

        eventForm.addEventListener('submit', function (event) {
            event.preventDefault();

            clearErrors();

            const errors = validateForm();
            if (errors.length > 0) {
                displayErrors(errors);
                return;
            }

            eventForm.submit();
        });

        function validateForm() {
            const errors = [];

            const name = document.getElementById('name').value.trim();
            if (name.length < 5 || name.length > 250) {
                errors.push({ field: 'name', message: 'Event name must be between 5 and 250 characters.' });
            }

            const description = document.getElementById('description').value.trim();
            if (description.length < 10 || description.length > 1000) {
                errors.push({ field: 'description', message: 'Event description must be between 10 and 1000 characters.' });
            }

            const maxCapacity = document.getElementById('max-capacity').value.trim();
            if (!maxCapacity || maxCapacity <= 0) {
                errors.push({ field: 'max-capacity', message: 'Maximum capacity must be a positive integer.' });
            }

            const startDatetime = document.getElementById('start-date').value.trim();
            if (!startDatetime) {
                errors.push({ field: 'start-date', message: 'Please enter a valid start date and time.' });
            }

            const endDatetime = document.getElementById('end-date').value.trim();
            if (!endDatetime) {
                errors.push({ field: 'end-date', message: 'Please enter a valid end date and time.' });
            } else if (new Date(endDatetime) <= new Date(startDatetime)) {
                errors.push({ field: 'end-date', message: 'End date and time must be after the start date and time.' });
            }

            return errors;
        }

        function displayErrors(errors) {
            errors.forEach(error => {
                if (error.field === 'general') {
                    errorBox.innerHTML = `<li>${error.message}</li>`;
                    errorBox.classList.remove('d-none');
                } else {
                    const input = document.getElementById(error.field);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = document.getElementById(`${error.field}-error`);
                        if (feedback) {
                            feedback.textContent = error.message;
                        }
                    }
                }
            });
        }

        function clearErrors() {
            errorBox.classList.add('d-none');
            errorBox.innerHTML = '';
            eventForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            eventForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        document.getElementById('event-start-date').addEventListener('change', function () {
            document.getElementById('event-end-date').setAttribute('min', this.value);
        });
    });
</script>