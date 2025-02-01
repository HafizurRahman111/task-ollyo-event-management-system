<div class="search-container mb-3">
    <div class="input-group">
        <input type="text" id="search-input" class="form-control" placeholder="Search users...">
        <button class="btn btn-outline-secondary" id="search-button">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th data-sort="id">No <i class="fas fa-sort"></i></th>
                <th data-sort="fullname">Full Name <i class="fas fa-sort"></i></th>
                <th data-sort="email">Email <i class="fas fa-sort"></i></th>
                <th data-sort="role">Role <i class="fas fa-sort"></i></th>
                <th data-sort="created_at">Created <i class="fas fa-sort"></i></th>
            </tr>
        </thead>
        <tbody id="user-list">
            <!-- users will be populated here via javascript -->
        </tbody>
    </table>
</div>

<p id="results-count" class="text-center"></p>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center" id="pagination">
        <!-- pagination links will be generated dynamically -->
    </ul>
</nav>

<script>
    const UserManager = {
        currentPage: 1,
        sortOrder: 'desc',
        sortBy: 'id',
        searchQuery: '',

        fetchUsers: function () {
            $.ajax({
                url: '<?= BASE_URL ?>users',
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
                        this.renderUsers(response.data.users);
                        this.renderPagination(response.data.totalPages);
                        this.updateResultsCount(response.data.totalResults, response.data.showingResults);
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: () => {
                    Swal.fire('Error!', 'An error occurred while fetching users.', 'error');
                }
            });
        },

        renderUsers: function (users) {
            const userList = $('#user-list');
            userList.empty();

            if (users.length > 0) {
                let no = 1;
                users.forEach(user => {
                    const date = new Date(user.created_at);

                    const formattedDate = `${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;

                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');

                    const formattedDateTime = `${formattedDate} ${hours}:${minutes}:${seconds}`;

                    const row = `
                            <tr>
                                <td>${no++}</td> 
                                <td>${user.fullname}</td>
                                <td>${user.email}</td>
                                <td>${user.role}</td>
                                <td>${formattedDateTime}</td>
                            </tr>
                        `;
                    userList.append(row);
                });
            } else {
                userList.append('<tr><td colspan="5" class="text-center">No users found.</td></tr>');
            }
        },

        renderPagination: function (totalPages) {
            const pagination = $('#pagination');
            pagination.empty();

            if (this.currentPage > 1) {
                pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${this.currentPage - 1}">Previous</a>
                        </li>
                    `);
            }

            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                        <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
            }

            if (this.currentPage < totalPages) {
                pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${this.currentPage + 1}">Next</a>
                        </li>
                    `);
            }
        },

        updateResultsCount: function (totalResults, showingResults) {
            $('#results-count').text(`Showing ${showingResults} of ${totalResults} results`);
        },

        init: function () {
            $(document).on('click', '.page-link', (e) => {
                e.preventDefault();
                this.currentPage = $(e.target).data('page');
                this.fetchUsers();
            });

            $(document).on('click', 'th[data-sort]', (e) => {
                this.sortBy = $(e.target).data('sort');
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                this.fetchUsers();
            });

            $('#search-button').on('click', () => {
                this.searchQuery = $('#search-input').val();
                this.currentPage = 1;
                this.fetchUsers();
            });

            $('#search-input').on('input', () => {
                this.searchQuery = $('#search-input').val();
                this.currentPage = 1;
                this.fetchUsers();
            });

            this.fetchUsers();
        }
    };

    UserManager.init();
</script>