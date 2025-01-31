<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />

    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .card-body {
            padding: 2rem;
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

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }

            .form-column {
                flex: 1 0 100%;
            }
        }

        .submit-btn {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Register for Event</h2>
        </div>
        <div class="card-body">

            <div id="successMessage" class="alert alert-success" style="display: none;"></div>

            <div id="generalErrorMessage" class="alert alert-danger" style="display: none;"></div>

            <form id="registrationForm">
                <div class="form-group">
                    <label for="eventSelect">Select an Event</label>
                    <select class="form-control" id="eventSelect" name="event_id" required>
                        <option value="">Select an Event</option>
                        <?php foreach ($availableEvents as $event): ?>
                            <option value="<?= $event['id'] ?>"
                                data-name="<?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>"
                                data-description="<?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>"
                                data-start="<?= htmlspecialchars($event['start_datetime'], ENT_QUOTES, 'UTF-8') ?>"
                                data-end="<?= htmlspecialchars($event['end_datetime'], ENT_QUOTES, 'UTF-8') ?>"
                                data-available="<?= $event['available_count'] ?>">
                                <?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="eventIdError" class="error-message"></div>
                </div>

                <div id="eventDetails" style="display: none;">
                    <h4 id="eventName"></h4>
                    <p id="eventDescription"></p>
                    <p><strong>Start:</strong> <span id="eventStart"></span></p>
                    <p><strong>End:</strong> <span id="eventEnd"></span></p>
                    <p><strong>Available Seats:</strong> <span id="eventAvailable"></span></p>

                    <input type="hidden" name="event_id" id="eventId">

                    <div class="form-group">
                        <label for="registrationType">Registration Type</label>
                        <select class="form-control" id="registrationType" name="registration_type" required>
                            <option value="self">Register Myself</option>
                            <option value="other">Register Someone Else</option>
                        </select>
                    </div>

                    <div class="form-group" id="attendeeNameGroup" style="display: none;">
                        <label for="attendeeName">Attendee Name</label>
                        <input type="text" class="form-control" id="attendeeName" name="attendee_name">
                        <div id="attendeeNameError" class="error-message"></div>
                    </div>

                    <div class="form-group" id="attendeeEmailGroup" style="display: none;">
                        <label for="attendeeEmail">Attendee Email</label>
                        <input type="email" class="form-control" id="attendeeEmail" name="attendee_email">
                        <div id="attendeeEmailError" class="error-message"></div>
                    </div>

                    <div class="submit-btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Register
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#eventSelect').select2({
                placeholder: "Select an Event",
                allowClear: true
            });

            $('#eventSelect').change(function () {
                var selectedEvent = $(this).find(':selected');
                var eventId = selectedEvent.val();

                if (eventId) {

                    $('#eventDetails').show();
                    $('#eventName').text(selectedEvent.data('name') || 'N/A');
                    $('#eventDescription').text(selectedEvent.data('description') || 'No description available');
                    $('#eventStart').text(selectedEvent.data('start') || 'Not specified');
                    $('#eventEnd').text(selectedEvent.data('end') || 'Not specified');
                    $('#eventAvailable').text(selectedEvent.data('available') || 'Not available');
                    $('#eventId').val(eventId);
                    $('#registrationType').val('self').trigger('change');
                } else {
                    $('#eventDetails').hide();
                }
            });

            $('#registrationType').change(function () {
                if ($(this).val() === 'other') {
                    $('#attendeeNameGroup, #attendeeEmailGroup').show();
                } else {
                    $('#attendeeNameGroup, #attendeeEmailGroup').hide();
                }
            });

            $('#registrationForm').submit(function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: '<?= BASE_URL ?>events/registration',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {

                            $('#successMessage').text(response.message).show();
                            $('#generalErrorMessage').hide();
                            $('#registrationForm')[0].reset();
                            $('#attendeeNameGroup, #attendeeEmailGroup').hide();

                            window.location.href = response.redirect_url;
                        } else {

                            $('#generalErrorMessage').text(response.errors?.general || 'An error occurred.').show();
                            $('#successMessage').hide();
                            $('#eventIdError').text(response.errors?.event_id || '');
                            $('#attendeeNameError').text(response.errors?.attendee_name || '');
                            $('#attendeeEmailError').text(response.errors?.attendee_email || '');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#generalErrorMessage').text('An error occurred: ' + errorThrown).show();
                    }
                });
            });
        });
    </script>

</body>

</html>