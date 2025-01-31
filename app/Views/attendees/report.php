<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color:rgb(105, 105, 105);
            font-size: 1.5rem;
            font-weight: bold;
        }

        .table th {
            background-color:rgb(85, 156, 248);
            color: white;
            font-weight: bold;
        }

        .table td {
            padding: 12px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination-container button {
            margin: 0 5px;
        }

        .no-attendees {
            text-align: center;
            color: #dc3545;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .csv-btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .csv-btn-container button {
            width: auto;
        }
    </style>
</head>

<body>

    <!-- Event Details Section -->
    <div class="event-container">
        <h2 class="section-title">Event Details</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Max Capacity</th>
                    <th>Seats Filled</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= count($reportData['attendees']) ?></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>Available Seats</th>
                    <th>Created By</th>
                    <th>Start Date & Time</th>
                    <th>End Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($event['max_capacity'] - count($reportData['attendees']), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars($event['created_by_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($event['start_datetime'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($event['end_datetime'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($event['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($event['updated_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Attendees Section -->
    <div class="attendees-container">
        <div class="csv-btn-container">
            <h2 class="section-title">Attendees</h2>
            <button class="btn btn-primary" onclick="downloadCSV()">Download CSV</button>
        </div>

        <!-- Search Field -->
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search attendees...">

        <?php if (!empty($reportData['attendees'])): ?>
            <table class="table table-striped" id="attendeesTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th data-order="asc">Name <span class="sort-indicator">▲</span><span class="sort-indicator-down"
                                style="display:none;">▼</span></th>
                        <th data-order="asc">Email <span class="sort-indicator">▲</span><span class="sort-indicator-down"
                                style="display:none;">▼</span></th>
                        <th data-order="asc">Registration Type <span class="sort-indicator">▲</span><span
                                class="sort-indicator-down" style="display:none;">▼</span></th>
                        <th data-order="asc">Registered By <span class="sort-indicator">▲</span><span
                                class="sort-indicator-down" style="display:none;">▼</span></th>
                        <th data-order="asc">Registration Time <span class="sort-indicator">▲</span><span
                                class="sort-indicator-down" style="display:none;">▼</span></th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="attendeesBody">
                    <?php $count = 1; ?>
                    <?php foreach ($reportData['attendees'] as $attendee): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($attendee['attendee_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($attendee['attendee_email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($attendee['registration_type'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($attendee['registered_by_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($attendee['registered_at'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    onclick="confirmDelete(<?= $attendee['attendee_id'] ?>, '<?= addslashes($attendee['attendee_name']) ?>', '<?= addslashes($attendee['attendee_email']) ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div class="pagination-container">
                <button id="prevPage" class="btn btn-secondary" disabled>Previous</button>
                <span id="pageNumber">1</span>
                <button id="nextPage" class="btn btn-secondary">Next</button>
            </div>
        <?php else: ?>
            <p class="no-attendees">No attendees registered for this event.</p>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentPage = 1;
            const rowsPerPage = 5;
            const rows = Array.from(document.querySelectorAll("#attendeesBody tr"));
            const searchInput = document.getElementById("searchInput");

            // Function to paginate rows
            function paginateRows() {
                rows.forEach((row, index) => {
                    row.style.display = (index >= (currentPage - 1) * rowsPerPage && index < currentPage * rowsPerPage) ? "" : "none";
                });
                document.getElementById("pageNumber").innerText = currentPage;
                document.getElementById("prevPage").disabled = currentPage === 1;
                document.getElementById("nextPage").disabled = currentPage * rowsPerPage >= rows.length;
            }

            // Function to filter rows based on search input
            function filterRows() {
                const query = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(query) ? "" : "none";
                });
            }

            // Function to sort rows by column
            function sortTable(columnIndex, order = 'asc') {
                rows.sort((a, b) => {
                    const aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
                    const bValue = b.cells[columnIndex].textContent.trim().toLowerCase();

                    if (order === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                });

                // Re-append sorted rows to the table
                const tbody = document.querySelector("#attendeesBody");
                tbody.innerHTML = "";
                rows.forEach(row => tbody.appendChild(row));

                // Reapply pagination
                paginateRows();
            }

            document.querySelectorAll("#attendeesTable th").forEach((header, index) => {
                if (index !== 6) { // Skip the "Actions" column
                    header.addEventListener("click", () => {
                        // Get the current sort order (default: 'asc')
                        const currentOrder = header.getAttribute("data-order") || 'asc';

                        // Determine the new sort order (toggle between 'asc' and 'desc')
                        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                        // Set the new order attribute
                        header.setAttribute("data-order", newOrder);

                        // Toggle the visibility of arrows
                        header.querySelector('.sort-indicator').style.display = newOrder === 'asc' ? 'inline' : 'none';
                        header.querySelector('.sort-indicator-down').style.display = newOrder === 'desc' ? 'inline' : 'none';

                        sortTable(index, newOrder);
                    });
                }
            });

            document.getElementById("prevPage").addEventListener("click", function () {
                if (currentPage > 1) currentPage--;
                paginateRows();
            });

            document.getElementById("nextPage").addEventListener("click", function () {
                if (currentPage * rowsPerPage < rows.length) currentPage++;
                paginateRows();
            });

            // Live search
            searchInput.addEventListener("input", filterRows);

            // Initial pagination
            paginateRows();

            // CSV download functionality
            function downloadCSV() {
                const eventName = "<?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>".replace(/\s+/g, '_').toLowerCase();
                const currentDateTime = new Date();
                const formattedDateTime = currentDateTime.toISOString().replace(/[-:T]/g, '_').replace(/\.\d{3}Z$/, '');

                // Event details
                const eventDetails = [
                    ["Event Name", "<?= htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Description", "<?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Max Capacity", "<?= htmlspecialchars($event['max_capacity'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Seats Filled", "<?= count($reportData['attendees']) ?>"],
                    ["Available Seats", "<?= htmlspecialchars($event['max_capacity'] - count($reportData['attendees']), ENT_QUOTES, 'UTF-8') ?>"],
                    ["Created By", "<?= htmlspecialchars($event['created_by_name'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Start Date & Time", "<?= htmlspecialchars($event['start_datetime'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["End Date & Time", "<?= htmlspecialchars($event['end_datetime'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Created At", "<?= htmlspecialchars($event['created_at'], ENT_QUOTES, 'UTF-8') ?>"],
                    ["Updated At", "<?= htmlspecialchars($event['updated_at'], ENT_QUOTES, 'UTF-8') ?>"]
                ];

                // Attendees data
                const attendeesData = [
                    ["No.", "Name", "Email", "Registration Type", "Registered By", "Registration Time"]
                ];
                rows.forEach(row => {
                    const cells = row.cells;
                    attendeesData.push([
                        cells[0].textContent,
                        cells[1].textContent,
                        cells[2].textContent,
                        cells[3].textContent,
                        cells[4].textContent,
                        cells[5].textContent
                    ]);
                });

                // Combine event details and attendees data
                const csvContent = "data:text/csv;charset=utf-8,"
                    + eventDetails.map(row => row.join(",")).join("\n") + "\n\n"
                    + attendeesData.map(row => row.join(",")).join("\n");

                const filename = `attendee-${eventName}-${formattedDateTime}.csv`;
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", filename);
                document.body.appendChild(link);
                link.click();
            }

            window.downloadCSV = downloadCSV;
        });

        function confirmDelete(attendeeId, attendeeName, attendeeEmail) {
            if (confirm(`Are you sure you want to delete the attendee:\n\nName: ${attendeeName}\nEmail: ${attendeeEmail}`)) {
                window.location.href = `${BASE_URL}/attendees/delete/${attendeeId}`;
            }
        }
    </script>
</body>

</html>