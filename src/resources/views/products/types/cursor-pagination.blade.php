<!DOCTYPE html>
<html lang="ru" x-data="products">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Товары с Cursor-пагинацией</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .rating-stars {
            color: #ffc107;
        }
    </style>
</head>
<body class="bg-light">
<!-- Loading Overlay -->
<div x-show="loading" class="loading-overlay" x-cloak>
    <div class="text-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-3 text-muted">Загружаем товары...</p>
    </div>
</div>

<div class="container py-4">
    <!-- Заголовок -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-primary">
                <i class="bi bi-shop"></i> Каталог товаров
            </h1>
            <p class="lead text-muted">Используется cursor-пагинация для быстрой навигации</p>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Фильтры и сортировка</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Поиск -->
                <div class="col-md-6 col-lg-4">
                    <label for="search" class="form-label">Поиск по названию</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text"
                               class="form-control"
                               id="search"
                               placeholder="Введите название товара"
                               x-model="filters.q"
                               @keyup.debounce.500ms="loadProducts()">
                    </div>
                </div>

                <!-- Цена от/до -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Цена</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number"
                                   class="form-control"
                                   placeholder="От"
                                   x-model="filters.priceFrom"
                                   @change.debounce="loadProducts()">
                        </div>
                        <div class="col-6">
                            <input type="number"
                                   class="form-control"
                                   placeholder="До"
                                   x-model="filters.priceTo"
                                   @change.debounce="loadProducts()">
                        </div>
                    </div>
                </div>

                <!-- Наличие -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Наличие</label>
                    <select class="form-select" x-model="filters.inStock" @change="loadProducts()">
                        <option value="">Все товары</option>
                        <option value="1">В наличии</option>
                        <option value="0">Нет в наличии</option>
                    </select>
                </div>

                <!-- Сортировка -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Сортировка</label>
                    <select class="form-select" x-model="filters.sort" @change="loadProducts()">
                        <option value="">По умолчанию</option>
                        <option value="price_asc">Цена ↑</option>
                        <option value="price_desc">Цена ↓</option>
                        <option value="rating_desc">Рейтинг ↓</option>
                        <option value="newest">Сначала новые</option>
                    </select>
                </div>

                <!-- Количество на странице -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label">Товаров на странице</label>
                    <select class="form-select" x-model="filters.perPage" @change="loadProducts()">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <!-- Кнопки управления -->
                <div class="col-md-6 col-lg-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100 me-2" @click="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i> Сбросить
                    </button>
                    <button class="btn btn-primary w-100" @click="loadProducts()">
                        <i class="bi bi-arrow-clockwise"></i> Применить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="alert alert-info mb-4" x-show="products.length > 0" x-cloak>
        <div class="row">
            <div class="col-md-4">
                <i class="bi bi-grid-3x3-gap"></i>
                Загружено товаров: <span x-text="products.length" class="fw-bold"></span>
            </div>
            <div class="col-md-4">
                <i class="bi bi-arrow-left-right"></i>
                Пагинация: <span class="fw-bold">Cursor-based</span>
            </div>
            <div class="col-md-4">
                <i class="bi bi-info-circle"></i>
                Используйте кнопки ниже для навигации
            </div>
        </div>
    </div>

    <!-- Товары -->
    <div class="row g-4 mb-4" id="products-container">
        <template x-for="product in products" :key="product.id">
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm card-hover">
                    <!-- Бейдж наличия -->
                    <span class="stock-badge">
                            <span x-show="product.in_stock" class="badge bg-success">
                                <i class="bi bi-check-circle"></i> В наличии
                            </span>
                            <span x-show="!product.in_stock" class="badge bg-danger">
                                <i class="bi bi-x-circle"></i> Нет в наличии
                            </span>
                        </span>

                    <!-- Заглушка для изображения -->
                    <div class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 180px;">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>

                    <div class="card-body">
                        <!-- Название и категория -->
                        <h5 class="card-title" x-text="product.name"></h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-tag"></i>
                            <span x-text="product.category?.name || 'Без категории'"></span>
                        </p>

                        <!-- Рейтинг -->
                        <div class="mb-2">
                                <span class="rating-stars">
                                    <template x-for="i in 5" :key="i">
                                        <i class="bi"
                                           :class="i <= Math.round(product.rating) ? 'bi-star-fill' : 'bi-star'">
                                        </i>
                                    </template>
                                </span>
                            <small class="text-muted ms-2" x-text="product.rating.toFixed(1)"></small>
                        </div>

                        <!-- Цена -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h4 text-primary fw-bold" x-text="formatPrice(product.price)"></span>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-cart-plus"></i> В корзину
                            </button>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-top-0">
                        <small class="text-muted">
                            <i class="bi bi-upc"></i> ID: <span x-text="product.id.substring(0, 8)"></span>...
                        </small>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Сообщение если нет товаров -->
    <div x-show="products.length === 0 && !loading" class="text-center py-5" x-cloak>
        <i class="bi bi-box-seam display-1 text-muted"></i>
        <h3 class="mt-3">Товары не найдены</h3>
        <p class="text-muted">Попробуйте изменить параметры фильтрации</p>
    </div>

    <!-- Пагинация -->
    <div x-show="products.length > 0" class="row mb-4" x-cloak>
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <!-- Информация -->
                        <div class="mb-3 mb-md-0">
                            <small class="text-muted">
                                <span x-text="products.length"></span> товаров загружено
                                <span x-show="pagination.has_more"> | Есть еще товары</span>
                            </small>
                        </div>

                        <!-- Кнопки навигации -->
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary"
                                    @click="loadPrevPage()"
                                    :disabled="!pagination.prev_cursor || loading">
                                <i class="bi bi-arrow-left"></i> Назад
                            </button>

                            <button class="btn btn-outline-secondary"
                                    @click="loadFirstPage()"
                                    :disabled="loading || !pagination.prev_cursor">
                                <i class="bi bi-arrow-counterclockwise"></i> В начало
                            </button>

                            <button class="btn btn-outline-primary"
                                    @click="loadNextPage()"
                                    :disabled="!pagination.next_cursor || loading">
                                Вперед <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Cursor информация -->
                        <div class="mt-3 mt-md-0">
                            <small class="text-muted d-block">
                                <i class="bi bi-key"></i> Текущий cursor:
                                <code class="ms-1" x-text="pagination.next_cursor?.substring(0, 20) || 'начало'"></code>...
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Отладка (можно скрыть) -->
    <div class="card mt-4" x-show="debug">
        <div class="card-header bg-dark text-white">
            <button class="btn btn-sm btn-light" @click="debug = !debug">
                <i class="bi bi-code-slash"></i> Отладка
            </button>
        </div>
        <div class="card-body">
            <pre x-text="JSON.stringify({filters: filters, pagination: pagination}, null, 2)"></pre>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Регистрируем компонент в Alpine.js
    document.addEventListener('alpine:init', () => {
        Alpine.data('products', () => ({
            // Состояние
            loading: false,
            debug: false,
            products: [],
            pagination: {
                next_cursor: null,
                prev_cursor: null,
                has_more: false,
                per_page: 15,
                path: ''
            },
            filters: {
                q: '',
                priceFrom: null,
                priceTo: null,
                categoryId: null,
                inStock: null,
                ratingFrom: null,
                sort: '',
                page: 1,
                perPage: 15,
                cursor: null
            },

            // Инициализация
            init() {
                console.log('Products app initialized');
                // Загружаем первые товары при загрузке
                this.loadProducts();
            },

            // Загрузка товаров
            async loadProducts(cursor = null) {
                try {
                    this.loading = true;

                    // Обновляем cursor если передан
                    if (cursor) {
                        this.filters.cursor = cursor;
                    } else {
                        // При новой фильтрации сбрасываем cursor
                        this.filters.cursor = null;
                    }

                    // Строим URL с параметрами
                    const url = new URL('{{ url("/api/products/cursor") }}');

                    // Добавляем фильтры
                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value !== null && value !== '' && value !== undefined) {
                            // Преобразуем ключи из camelCase в snake_case для API
                            const apiKey = this.convertToSnakeCase(key);
                            url.searchParams.append(apiKey, value);
                        }
                    });

                    console.log('Loading from:', url.toString());

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    // Обновляем данные
                    this.products = data.data || [];
                    this.updatePagination(data);

                } catch (error) {
                    console.error('Error loading products:', error);
                    this.showError('Ошибка при загрузке товаров');
                } finally {
                    this.loading = false;
                }
            },

            // Загрузка следующей страницы
            loadNextPage() {
                if (this.pagination.next_cursor) {
                    this.loadProducts(this.pagination.next_cursor);
                }
            },

            // Загрузка предыдущей страницы
            loadPrevPage() {
                if (this.pagination.prev_cursor) {
                    this.loadProducts(this.pagination.prev_cursor);
                }
            },

            // Загрузка первой страницы
            loadFirstPage() {
                this.filters.cursor = null;
                this.loadProducts();
            },

            // Обновление данных пагинации
            updatePagination(data) {
                this.pagination = {
                    next_cursor: data.next_cursor || null,
                    prev_cursor: data.prev_cursor || null,
                    has_more: data.next_cursor ? true : false,
                    per_page: data.per_page || this.filters.perPage,
                    path: data.path || ''
                };

                // Обновляем per_page в фильтрах
                this.filters.perPage = data.per_page || this.filters.perPage;
            },

            // Сброс фильтров
            resetFilters() {
                this.filters = {
                    q: '',
                    priceFrom: null,
                    priceTo: null,
                    categoryId: null,
                    inStock: null,
                    ratingFrom: null,
                    sort: '',
                    page: 1,
                    perPage: 15,
                    cursor: null
                };
                this.loadProducts();
            },

            // Форматирование цены
            formatPrice(price) {
                return new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }).format(price);
            },

            // Показать ошибку
            showError(message) {
                // Можно использовать Bootstrap toast или alert
                alert(message);
            },

            // Конвертация camelCase в snake_case для API
            convertToSnakeCase(str) {
                return str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
            }
        }));
    });
</script>
</body>
</html>
