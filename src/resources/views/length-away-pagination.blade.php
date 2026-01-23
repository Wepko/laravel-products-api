<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .filter-card {
            transition: all 0.3s ease;
        }
        .filter-card.collapsed .card-body {
            padding: 0;
            max-height: 0;
            overflow: hidden;
        }
        .filter-badge {
            cursor: pointer;
        }
        .filter-badge:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
<div class="container mt-4" x-data="productTable()" x-init="fetchProducts()">
    <!-- Заголовок -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">Products Management</h2>
                    <p class="text-muted mb-0" x-text="`Total: ${pagination.total || 0} products`"></p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" @click="showFilters = !showFilters">
                        <i class="bi bi-funnel"></i> Filters
                        <span x-show="activeFiltersCount > 0" class="badge bg-danger ms-1"
                              x-text="activeFiltersCount"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Активные фильтры -->
    <div x-show="activeFiltersCount > 0" class="mb-3">
        <div class="card border-primary">
            <div class="card-body py-2">
                <small class="text-muted me-2">Active filters:</small>
                <template x-for="(value, key) in filters" :key="key">
                    <template x-if="value !== null && value !== '' && key !== 'sort' && key !== 'page' && key !== 'perPage'">
                        <span class="badge bg-primary me-2 mb-1 filter-badge"
                              @click="clearFilter(key)">
                            <template x-if="key === 'q'">Search: <span x-text="value"></span></template>
                            <template x-if="key === 'inStock'">In Stock: <span x-text="value ? 'Yes' : 'No'"></span></template>
                            <template x-if="key === 'priceFrom'">Price from: $<span x-text="value"></span></template>
                            <template x-if="key === 'priceTo'">Price to: $<span x-text="value"></span></template>
                            <template x-if="key === 'categoryId'">Category ID: <span x-text="value"></span></template>
                            <template x-if="key === 'ratingFrom'">Rating ≥ <span x-text="value"></span></template>
                            <template x-if="key === 'sort'">Sort: <span x-text="getSortLabel(value)"></span></template>
                            <i class="bi bi-x ms-1"></i>
                        </span>
                    </template>
                </template>
                <a href="#" class="text-danger small" @click.prevent="clearAllFilters">
                    <i class="bi bi-x-circle"></i> Clear all
                </a>
            </div>
        </div>
    </div>

    <!-- Фильтры (аккордеон) -->
    <div class="card mb-4 filter-card" :class="{ 'collapsed': !showFilters }">
        <div class="card-header" @click="showFilters = !showFilters" style="cursor: pointer;">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filters
                <i class="bi bi-chevron-down float-end" :class="{ 'bi-chevron-up': showFilters }"></i>
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Поиск -->
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Product name..."
                               x-model="filters.q" @input.debounce.500ms="applyFilters">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Наличие -->
                <div class="col-md-3">
                    <label class="form-label">In Stock</label>
                    <select class="form-select" x-model="filters.inStock" @change="applyFilters">
                        <option value="">All</option>
                        <option value="1">In Stock</option>
                        <option value="0">Out of Stock</option>
                    </select>
                </div>

                <!-- Категория -->
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" x-model="filters.categoryId" @change="applyFilters">
                        <option value="">All Categories</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Цена -->
                <div class="col-md-3">
                    <label class="form-label">Price From</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" placeholder="Min price"
                               min="0" step="0.01"
                               x-model="filters.priceFrom" @input.debounce.500ms="applyFilters">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Price To</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" placeholder="Max price"
                               min="0" step="0.01"
                               x-model="filters.priceTo" @input.debounce.500ms="applyFilters">
                    </div>
                </div>

                <!-- Рейтинг -->
                <div class="col-md-3">
                    <label class="form-label">Rating From</label>
                    <select class="form-select" x-model="filters.ratingFrom" @change="applyFilters">
                        <option value="">Any rating</option>
                        <option value="1">1+ star</option>
                        <option value="2">2+ stars</option>
                        <option value="3">3+ stars</option>
                        <option value="4">4+ stars</option>
                        <option value="5">5 stars</option>
                    </select>
                </div>

                <!-- Сортировка -->
                <div class="col-md-3">
                    <label class="form-label">Sort By</label>
                    <select class="form-select" x-model="filters.sort" @change="applyFilters">
                        <option value="">Default</option>
                        <option value="created_at_desc">Newest First</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="name_asc">Name: A-Z</option>
                        <option value="name_desc">Name: Z-A</option>
                        <option value="rating_desc">Highest Rating</option>
                    </select>
                </div>

                <!-- Кнопки -->
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary" @click="clearAllFilters">
                            <i class="bi bi-x-circle"></i> Clear All
                        </button>
                        <button class="btn btn-primary" @click="applyFilters">
                            <i class="bi bi-check-circle"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица продуктов -->
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
                            <th>ID</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>
                                <a href="#" @click.prevent="sortBy('price')" class="text-decoration-none">
                                    Price
                                    <i class="bi bi-arrow-down" x-show="filters.sort === 'price_asc'"></i>
                                    <i class="bi bi-arrow-up" x-show="filters.sort === 'price_desc'"></i>
                                </a>
                            </th>
                            <th>Stock</th>
                            <th>
                                <a href="#" @click.prevent="sortBy('rating')" class="text-decoration-none">
                                    Rating
                                    <i class="bi bi-arrow-down" x-show="filters.sort === 'rating_desc'"></i>
                                </a>
                            </th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="product in products" :key="product.id">
                            <tr>
                                <td x-text="product.id"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <img :src="product.image || 'https://via.placeholder.com/50'"
                                                 alt="Product image"
                                                 class="rounded"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0" x-text="product.name"></h6>
                                            <small class="text-muted" x-text="product.sku"></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary" x-text="product.category?.name || 'No category'"></span>
                                </td>
                                <td>
                                    <strong x-text="'$' + product.price"></strong>
                                </td>
                                <td>
                                    <span class="badge"
                                          :class="product.in_stock ? 'bg-success' : 'bg-danger'">
                                        <span x-text="product.in_stock ? 'In Stock' : 'Out of Stock'"></span>
{{--                                        <span x-show="product.in_stock"--}}
{{--                                              x-text="' (' + product.stock_quantity + ')'"></span>--}}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning">
                                            <template x-for="i in 5" :key="i">
                                                <i class="bi"
                                                   :class="i <= Math.round(product.rating) ? 'bi-star-fill' : 'bi-star'"></i>
                                            </template>
                                        </div>
                                        <small class="text-muted ms-2" x-text="product.rating.toFixed(1)"></small>
                                    </div>
                                </td>
                                <td x-text="new Date(product.created_at).toLocaleDateString()"></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="products.length === 0">
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-box display-6"></i>
                                    <p class="mt-2">No products found</p>
                                    <button class="btn btn-sm btn-outline-primary" @click="clearAllFilters">
                                        Clear filters
                                    </button>
                                </div>
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
                            <span x-text="pagination.total || 0"></span> products
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

    <!-- Отладка -->
    <div class="mt-3">
        <details>
            <summary class="text-muted small">Debug Info</summary>
            <pre class="small text-muted mt-2" x-text="JSON.stringify({filters, pagination}, null, 2)"></pre>
        </details>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function productTable() {
        return {
            products: [],
            categories: [],
            loading: false,
            showFilters: true,
            filters: {
                q: '',
                inStock: '',
                priceFrom: '',
                priceTo: '',
                categoryId: '',
                ratingFrom: '',
                sort: 'created_at_desc',
                page: 1,
                perPage: 15
            },
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

            get activeFiltersCount() {
                return Object.entries(this.filters)
                    .filter(([key, value]) =>
                        value !== null &&
                        value !== '' &&
                        key !== 'page' &&
                        key !== 'perPage' &&
                        key !== 'sort'
                    ).length;
            },

            async fetchCategories() {
                try {
                    const response = await fetch('/api/categories');
                    const result = await response.json();
                    this.categories = result.data || [];
                } catch (error) {
                    console.error('Error fetching categories:', error);
                }
            },

            async fetchProducts(page = 1) {
                this.loading = true;
                this.filters.page = page;

                // Строим параметры запроса
                const params = new URLSearchParams();

                Object.entries(this.filters).forEach(([key, value]) => {
                    if (value !== null && value !== '' && value !== undefined) {
                        params.append(key, value);
                    }
                });

                try {
                    const response = await fetch(`/api/products?${params}`);
                    const result = await response.json();

                    this.products = result.data;
                    this.updatePagination(result);
                } catch (error) {
                    console.error('Error fetching products:', error);
                } finally {
                    this.loading = false;
                }
            },

            updatePagination(response) {
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

            applyFilters() {
                this.fetchProducts(1);
            },

            goToPage(page) {
                if (page === '...') return;
                this.fetchProducts(page);
            },

            prevPage() {
                if (this.pagination.prev_page_url) {
                    this.fetchProducts(this.pagination.current_page - 1);
                }
            },

            nextPage() {
                if (this.pagination.next_page_url) {
                    this.fetchProducts(this.pagination.current_page + 1);
                }
            },

            changePerPage(perPage) {
                this.filters.perPage = perPage;
                this.fetchProducts(1);
            },

            sortBy(field) {
                if (field === 'price') {
                    this.filters.sort = this.filters.sort === 'price_asc' ? 'price_desc' : 'price_asc';
                } else if (field === 'rating') {
                    this.filters.sort = 'rating_desc';
                } else if (field === 'name') {
                    this.filters.sort = this.filters.sort === 'name_asc' ? 'name_desc' : 'name_asc';
                }
                this.fetchProducts(1);
            },

            clearFilter(filterKey) {
                if (filterKey === 'sort') {
                    this.filters[filterKey] = 'created_at_desc';
                } else {
                    this.filters[filterKey] = '';
                }
                this.fetchProducts(1);
            },

            clearAllFilters() {
                this.filters = {
                    q: '',
                    inStock: '',
                    priceFrom: '',
                    priceTo: '',
                    categoryId: '',
                    ratingFrom: '',
                    sort: 'created_at_desc',
                    page: 1,
                    perPage: 15
                };
                this.fetchProducts(1);
            },

            getSortLabel(sortValue) {
                const labels = {
                    'created_at_desc': 'Newest First',
                    'price_asc': 'Price: Low to High',
                    'price_desc': 'Price: High to Low',
                    'name_asc': 'Name: A-Z',
                    'name_desc': 'Name: Z-A',
                    'rating_desc': 'Highest Rating'
                };
                return labels[sortValue] || sortValue;
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },

            init() {
                this.fetchCategories();
                this.fetchProducts();
            }
        }
    }
</script>
</body>
</html>
