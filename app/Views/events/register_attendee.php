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

        .alert {
            margin-bottom: 1rem;
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

                    <div id="alreadyRegisteredMessage" class="alert alert-danger fw-bold d-none">
                        You have already registered for this event.
                    </div>

                    <div id="registrationFormFields">
                        <input type="hidden" name="event_id" id="eventId">

                        <div class="form-group">
                            <label for="registrationType">Registration Type</label>
                            <select class="form-control" id="registrationType" name="registration_type" required>
                                <option value="self" selected>Register Myself</option>
                                <option value="other">Register Someone Else</option>
                            </select>
                        </div>
                        <div id="attendeeDetails">
                            <label for="attendee_name">Name</label>
                            <input type="text" name="attendee_name" class="form-control" required>
                            <label for="attendee_email">Email</label>
                            <input type="email" name="attendee_email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Register</button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            const baseUrl = 'https://127.0.0.1/ems/';

            $('#eventSelect').select2({ placeholder: "Select an Event", allowClear: true });

            $('#eventSelect').change(async function () {
                const eventId = $(this).val();
                $('#eventIdError').text('');
                if (eventId) {
                    $('#eventDetails').show();
                    updateEventDetails($(this).find(':selected'));
                    await checkIfAlreadyRegistered(eventId);
                } else {
                    $('#eventDetails').hide();
                }
            });

            function updateEventDetails(selectedEvent) {
                $('#eventName').text(selectedEvent.data('name'));
                $('#eventDescription').text(selectedEvent.data('description'));
                $('#eventStart').text(selectedEvent.data('start'));
                $('#eventEnd').text(selectedEvent.data('end'));
                $('#eventAvailable').text(selectedEvent.data('available'));
                $('#eventId').val(selectedEvent.val());
            }

            async function checkIfAlreadyRegistered(eventId) {
                try {
                    const response = await fetch(`${baseUrl}events/check/${eventId}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        $('#alreadyRegisteredMessage').toggleClass('d-none', !result.alreadyRegistered);
                        $('#registrationFormFields').toggleClass('d-none', result.alreadyRegistered);
                    } else {
                        $('#generalErrorMessage').text(result.error || 'Error checking registration.').show();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    $('#generalErrorMessage').text('A network error occurred.').show();
                }
            }

            $('#registrationForm').submit(function (e) {
                e.preventDefault();
                $('#generalErrorMessage').hide();
                const formData = $(this).serialize();


                $.ajax({
                    url: '<?= BASE_URL ?>events/registration',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {

                            $('#successMessage').text(response.message).show();
                            // $('#generalErrorMessage').hide();
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

            function displayFormErrors(errors) {
                $('#generalErrorMessage').text(errors?.general || 'An error occurred.').show();
                $('#eventIdError').text(errors?.event_id || '');
                $('#attendeeNameError').text(errors?.attendee_name || '');
                $('#attendeeEmailError').text(errors?.attendee_email || '');
            }
        });
    </script>
</body>

</html>