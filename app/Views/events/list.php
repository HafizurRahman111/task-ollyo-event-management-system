<div class="container mt-1">
    <div class="search-container d-flex justify-content-between align-items-center mb-3">
        <div class="input-group">
            <input type="text" id="search-input" class="form-control" placeholder="Search events..."
                aria-label="Search events">
            <button class="btn btn-outline-secondary" type="button" id="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <a href="<?= BASE_URL ?>events/create" class="btn btn-primary new-event-button">
            <i class="fas fa-plus"></i> New Event
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th data-sort="id">ID <i class="fas fa-sort"></i></th>
                    <th data-sort="name">Name <i class="fas fa-sort"></i></th>
                    <th>Description</th>
                    <th data-sort="max_capacity">Max Capacity <i class="fas fa-sort"></i></th>
                    <th data-sort="start_datetime">Start Date <i class="fas fa-sort"></i></th>
                    <th data-sort="end_datetime">End Date <i class="fas fa-sort"></i></th>
                    <?php if ($userRole === 'admin'): ?>
                        <th>Created By</th>
                    <?php endif; ?>
                    <th data-sort="created_at">Created <i class="fas fa-sort"></i></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="event-list">
                <!-- Events will be populated here via JavaScript -->
            </tbody>
        </table>
    </div>

    <p id="results-count" class="text-center">Showing <?= $showingResults; ?> results out of <?= $totalResults; ?></p>

    <nav aria-label="Page navigation">
        <ul class="pagination" id="pagination">
            <!-- Pagination links will be generated dynamically -->
        </ul>
    </nav>
</div>

<script>
    const currentUserId = <?= $currentUserId ?>;
    const userRole = '<?= $userRole ?>';

    const EventManager = {
        currentPage: 1,
        sortOrder: 'asc',
        sortBy: 'id',
        searchQuery: '',

        fetchEvents: function () {
            $.ajax({
                url: '<?= BASE_URL ?>events',
                type: 'GET',
                data: {
                    page: this.currentPage,
                    sort: this.sortBy,
                    order: this.sortOrder,
                    search: this.searchQuery
                },
                dataType: 'json',
                success: (response) => {
                    if (response.status === 'success') {
                        this.renderEvents(response.data.events);
                        this.renderPagination(response.data.totalPages);
                        this.updateResultsCount(response.data.totalResults, response.data.showingResults);
                    } else {
                        Swal.fire('Error!', 'Failed to fetch events: ' + response.message, 'error');
                    }
                },
                error: () => {
                    Swal.fire('Error!', 'An error occurred while fetching events.', 'error');
                }
            });
        },

        renderEvents: function (events) {
            const eventList = $('#event-list');
            eventList.empty();

            if (events.length > 0) {
                events.forEach(event => {
                    const row = `
                        <tr>
                            <td>${event.id}</td>
                            <td>${event.name}</td>
                            <td>${event.description}</td>
                            <td>${event.max_capacity}</td>
                            <td>${event.start_datetime}</td>
                            <td>${event.end_datetime}</td>
                            <?php if ($userRole === 'admin'): ?>
                                        <td>${event.created_by_name || 'N/A'}</td>
                            <?php endif; ?>
                            <td>${event.created_at}</td>
                            <td class="action-buttons">${this.renderEventActions(event)}</td>
                        </tr>
                    `;
                    eventList.append(row);
                });
            } else {
                eventList.append('<tr><td colspan="9" class="text-center">No events found.</td></tr>');
            }
        },

        renderEventActions: function (event) {
            let actionButtons = `
                <a href="<?= BASE_URL ?>events/view/${event.id}" class="btn btn-sm btn-primary" title="View">
                    <i class="fas fa-eye"></i>
                </a>
            `;

            // Only show Edit and Delete buttons if the current user created the event
            if (event.created_by === currentUserId || userRole === 'admin') {
                actionButtons += `
                    <a href="<?= BASE_URL ?>events/edit/${event.id}" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger" title="Delete" onclick="EventManager.confirmDelete(${event.id}, '${event.name}', '${event.start_datetime}')">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            }

            // If the user is an admin, show the Report Download option
            if (userRole === 'admin') {
                actionButtons += `
                    <a href="<?= BASE_URL ?>events/report/${event.id}" class="btn btn-sm btn-info" title="Download Report">
                        <i class="fas fa-download"></i> Report
                    </a>
                `;
            }

            return actionButtons;
        },

        renderPagination: function (totalPages) {
            const pagination = $('#pagination');
            pagination.empty();

            // Previous Button
            if (this.currentPage > 1) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${this.currentPage - 1}">Previous</a>
                    </li>
                `);
            }

            // Page Numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                    <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            // Next Button
            if (this.currentPage < totalPages) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${this.currentPage + 1}">Next</a>
                    </li>
                `);
            }
        },

        updateResultsCount: function (totalResults, showingResults) {
            $('#results-count').text(`Showing ${showingResults} results out of ${totalResults}`);
        },

        confirmDelete: function (eventId, eventName, eventStartDate) {
            Swal.fire({
                title: `Are you sure you want to delete the event "${eventName}"?`,
                text: `This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call delete event API
                    $.ajax({
                        url: '<?= BASE_URL ?>events/delete/' + eventId,
                        type: 'POST',
                        success: (response) => {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', 'The event has been deleted.', 'success');
                                this.fetchEvents();
                            } else {
                                Swal.fire('Error!', 'Failed to delete the event: ' + response.message, 'error');
                            }
                        },
                        error: () => {
                            Swal.fire('Error!', 'An error occurred while deleting the event.', 'error');
                        }
                    });
                }
            });
        },

        init: function () {
            // Event Delegation for Pagination Links
            $(document).on('click', '.page-link', (e) => {
                e.preventDefault();
                this.currentPage = $(e.target).data('page');
                this.fetchEvents();
            });

            // Sorting
            $(document).on('click', 'th[data-sort]', (e) => {
                this.sortBy = $(e.target).data('sort');
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                this.fetchEvents();
            });

            // Search
            $('#search-button').on('click', () => {
                this.searchQuery = $('#search-input').val();
                this.currentPage = 1;
                this.fetchEvents();
            });

            $('#search-input').on('input', () => {
                this.searchQuery = $('#search-input').val();
                this.currentPage = 1;
                this.fetchEvents();
            });

            this.fetchEvents();
        }
    };

    EventManager.init();
</script>