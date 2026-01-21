<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-4" x-data="userTable()" x-init="fetchUsers()">
    <!-- Заголовок и поиск -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="mb-0">Users Management</h2>
                    <p class="text-muted mb-0" x-text="`Total: ${pagination.total || 0} users`"></p>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search users..."
                               x-model="search" @input.debounce.500ms="searchUsers()">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица -->
    <div class="card">
        <div class="card-body">
            <!-- Загрузка -->
            <div x-show="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Таблица данных -->
            <div x-show="!loading">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>
                                <a href="#" @click.prevent="sortBy('id')">
                                    ID
                                    <i class="bi bi-arrow-down" x-show="sortField === 'id' && sortDirection === 'asc'"></i>
                                    <i class="bi bi-arrow-up" x-show="sortField === 'id' && sortDirection === 'desc'"></i>
                                </a>
                            </th>
                            <th>
                                <a href="#" @click.prevent="sortBy('name')">
                                    Name
                                    <i class="bi bi-arrow-down" x-show="sortField === 'name' && sortDirection === 'asc'"></i>
                                    <i class="bi bi-arrow-up" x-show="sortField === 'name' && sortDirection === 'desc'"></i>
                                </a>
                            </th>
                            <th>Email</th>
                            <th>
                                <a href="#" @click.prevent="sortBy('created_at')">
                                    Created At
                                    <i class="bi bi-arrow-down" x-show="sortField === 'created_at' && sortDirection === 'asc'"></i>
                                    <i class="bi bi-arrow-up" x-show="sortField === 'created_at' && sortDirection === 'desc'"></i>
                                </a>
                            </th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="user in users" :key="user.id">
                            <tr>
                                <td x-text="user.id"></td>
                                <td x-text="user.name"></td>
                                <td x-text="user.email"></td>
                                <td x-text="new Date(user.created_at).toLocaleDateString()"></td>
                                <td>
                                    <span class="badge bg-success" x-show="user.active">Active</span>
                                    <span class="badge bg-danger" x-show="!user.active">Inactive</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="users.length === 0">
                            <td colspan="6" class="text-center py-4">
                                No users found
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                            <span class="text-muted">
                                Showing <span x-text="pagination.from || 0"></span> to
                                <span x-text="pagination.to || 0"></span> of
                                <span x-text="pagination.total || 0"></span> entries
                            </span>
                    </div>

                    <nav>
                        <ul class="pagination mb-0">
                            <!-- First Page -->
                            <li class="page-item" :class="{ disabled: !pagination.first_page_url }">
                                <a class="page-link" href="#" @click.prevent="goToPage(1)">
                                    <i class="bi bi-chevron-double-left"></i>
                                </a>
                            </li>

                            <!-- Previous Page -->
                            <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                <a class="page-link" href="#" @click.prevent="prevPage()">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <template x-for="link in pagination.links" :key="link.label">
                                <li class="page-item" :class="{
                                        active: link.active,
                                        disabled: !link.url
                                    }">
                                    <a class="page-link" href="#"
                                       x-html="link.label"
                                       @click.prevent="link.url ? goToPage(link.label) : null">
                                    </a>
                                </li>
                            </template>

                            <!-- Next Page -->
                            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                <a class="page-link" href="#" @click.prevent="nextPage()">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>

                            <!-- Last Page -->
                            <li class="page-item" :class="{ disabled: !pagination.last_page_url }">
                                <a class="page-link" href="#" @click.prevent="goToPage(pagination.last_page)">
                                    <i class="bi bi-chevron-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                            <span x-text="pagination.per_page"></span> per page
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" @click.prevent="changePerPage(10)">10</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="changePerPage(25)">25</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="changePerPage(50)">50</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="changePerPage(100)">100</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Отладка (можно убрать) -->
    <div class="mt-3 small text-muted">
        <pre x-text="JSON.stringify(pagination, null, 2)"></pre>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function userTable() {
        return {
            users: [],
            loading: false,
            search: '',
            sortField: 'id',
            sortDirection: 'desc',
            pagination: {
                current_page: 1,
                per_page: 15,
                total: 0,
                last_page: 1,
                from: 0,
                to: 0,
                links: [],
                first_page_url: null,
                last_page_url: null,
                prev_page_url: null,
                next_page_url: null
            },

            async fetchUsers(page = 1) {
                this.loading = true;

                const params = new URLSearchParams({
                    page: page,
                    per_page: this.pagination.per_page,
                    search: this.search,
                    sort_by: this.sortField,
                    sort_direction: this.sortDirection
                });

                try {
                    const response = await fetch(`/api/users?${params}`);
                    const result = await response.json();

                    this.users = result.data;
                    this.updatePagination(result);
                } catch (error) {
                    console.error('Error fetching users:', error);
                } finally {
                    this.loading = false;
                }
            },

            updatePagination(response) {
                // Обновляем данные пагинации
                this.pagination = {
                    current_page: response.current_page || 1,
                    per_page: response.per_page || 15,
                    total: response.total || 0,
                    last_page: response.last_page || 1,
                    from: response.from || 0,
                    to: response.to || 0,
                    links: response.links || [],
                    first_page_url: response.first_page_url,
                    last_page_url: response.last_page_url,
                    prev_page_url: response.prev_page_url,
                    next_page_url: response.next_page_url
                };
            },

            goToPage(page) {
                if (page === '...') return;
                this.fetchUsers(page);
            },

            prevPage() {
                if (this.pagination.prev_page_url) {
                    this.fetchUsers(this.pagination.current_page - 1);
                }
            },

            nextPage() {
                if (this.pagination.next_page_url) {
                    this.fetchUsers(this.pagination.current_page + 1);
                }
            },

            changePerPage(perPage) {
                this.pagination.per_page = perPage;
                this.fetchUsers(1);
            },

            sortBy(field) {
                if (this.sortField === field) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortField = field;
                    this.sortDirection = 'desc';
                }
                this.fetchUsers(1);
            },

            searchUsers() {
                this.fetchUsers(1);
            }
        }
    }
</script>
</body>
</html>
