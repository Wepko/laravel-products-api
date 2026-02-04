#!/bin/bash

set -e

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функции для вывода
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Проверка наличия Docker
check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker не установлен. Установите Docker: https://docs.docker.com/get-docker/"
        exit 1
    fi

    if ! docker info &> /dev/null; then
        print_error "Демон Docker не запущен. Запустите Docker Desktop или службу Docker."
        exit 1
    fi
}

# Проверка наличия Docker Compose
check_docker_compose() {
    if ! docker compose version &> /dev/null; then
        print_error "Docker Compose не установлен. Установите Docker Compose Plugin."
        exit 1
    fi
}

# Основные команды
case "$1" in
    "up"|"start")
        check_docker
        check_docker_compose
        print_info "Запуск Docker контейнеров..."
        docker compose up -d
        print_success "Контейнеры запущены"
        ;;

    "down"|"stop")
        print_info "Остановка Docker контейнеров..."
        docker compose down
        print_success "Контейнеры остановлены"
        ;;

    "restart")
        print_info "Перезапуск Docker контейнеров..."
        docker compose restart
        print_success "Контейнеры перезапущены"
        ;;

    "build")
        check_docker
        check_docker_compose
        print_info "Сборка Docker образов..."
        docker compose build --no-cache
        print_success "Образы собраны"
        ;;

    "logs")
        docker compose logs -f
        ;;

    "shell"|"bash")
        docker compose exec app bash
        ;;

    "artisan")
        docker compose exec app php artisan "${@:2}"
        ;;

    "composer")
        docker compose exec app composer "${@:2}"
        ;;

    "npm")
        docker compose exec app npm "${@:2}"
        ;;

    "test")
        docker compose exec app php artisan test
        ;;

    "migrate")
        docker compose exec app php artisan migrate
        ;;

    "fresh")
        docker compose exec app php artisan migrate:fresh --seed
        ;;

    "tinker")
        docker compose exec app php artisan tinker
        ;;

   "install")
        print_info "🚀 Установка проекта Laravel Products API..."

        # Проверяем Docker
        check_docker
        check_docker_compose

        # 1. Копируем .env файл если его нет в папке src
        if [ ! -f "src/.env" ]; then
            if [ -f ".env.example" ]; then  # Без src/
                cp .env.example src/.env     # Из корня в src/
                print_success "Создан .env файл в папке src/"
            else
                print_warning "Файл .env.example не найден в папке src/"
            fi
        else
            print_info "Файл .env уже существует в папке src/"
        fi

        # 2. Собираем и запускаем контейнеры
        print_info "🐳 Сборка и запуск Docker контейнеров..."
        docker compose build --no-cache
        docker compose up -d

        # 3. Ждем запуска сервисов
        print_info "⏳ Ждем запуска сервисов (30 секунд)..."
        sleep 30

        # 4. Устанавливаем PHP зависимости
        print_info "📦 Установка PHP зависимостей..."
        docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader

        # 5. Генерируем ключ приложения внутри контейнера
        print_info "🔑 Генерация ключа приложения..."
        docker compose exec -T app php artisan key:generate --force

        # 6. Копируем сгенерированный ключ обратно на хост
        print_info "📋 Копируем обновленный .env файл..."
        docker compose cp app:/var/www/.env ./src/.env.container 2>/dev/null || true
        if [ -f "./src/.env.container" ]; then
            # Берем только APP_KEY из контейнера
            APP_KEY_CONTAINER=$(grep "^APP_KEY=" ./src/.env.container)
            if [ ! -z "$APP_KEY_CONTAINER" ]; then
                # Обновляем APP_KEY в локальном .env
                grep -v "^APP_KEY=" ./src/.env > ./src/.env.tmp
                echo "$APP_KEY_CONTAINER" >> ./src/.env.tmp
                mv ./src/.env.tmp ./src/.env
                print_success "Ключ приложения обновлен"
            fi
            rm -f ./src/.env.container
        fi

        # 7. Устанавливаем NPM зависимости
        print_info "📦 Установка Node.js зависимостей..."
        docker compose exec -T app npm install --quiet

        # 8. Запускаем миграции
        print_info "🔄 Запуск миграций..."
        docker compose exec -T app php artisan migrate --force

        # 9. Запускаем сидеры
        print_info "🌱 Запуск сидеров..."
        docker compose exec -T app php artisan db:seed --force

        # 10. Устанавливаем права
        print_info "🔐 Установка прав на папки..."
        docker compose exec -T app chmod -R 775 storage bootstrap/cache

        # 11. Создаем симлинк для storage
        print_info "🔗 Создание симлинка storage..."
        docker compose exec -T app php artisan storage:link

        # 12. Генерируем документацию Swagger
        print_info "📚 Генерация документации API..."
        docker compose exec -T app php artisan l5-swagger:generate

        # 13. Очищаем кэш
        print_info "🧹 Очистка кэша..."
        docker compose exec -T app php artisan config:clear
        docker compose exec -T app php artisan cache:clear
        docker compose exec -T app php artisan view:clear
        docker compose exec -T app php artisan route:clear

        print_success "✅ Установка завершена!"
        echo ""
        print_info "🌐 Доступные адреса:"
        echo "   Приложение:      http://localhost"
        echo "   API документация: http://localhost/api/documentation"
        echo "   phpMyAdmin:      http://localhost:8080 (root/secret)"
        echo "   Elasticsearch:   http://localhost:9200"
        echo "   MySQL:           localhost:3306 (laravel/secret)"
        echo "   Redis:           localhost:6379"
        echo ""
        print_info "🔧 Команды управления:"
        echo "   ./docker.sh up       - Запуск контейнеров"
        echo "   ./docker.sh down     - Остановка контейнеров"
        echo "   ./docker.sh logs     - Показать логи"
        echo "   ./docker.sh artisan  - Запуск artisan команд"
        echo "   ./docker.sh shell    - Войти в контейнер"
        echo "   ./docker.sh fresh    - Пересоздать БД с тестовыми данными"
        echo ""
        print_info "📝 Проверка работоспособности:"
        echo "   curl http://localhost/api/products"
        echo "   или откройте http://localhost в браузере"
        ;;

    "status")
        docker compose ps
        ;;

    "clean")
        print_warning "Очистка Docker ресурсов..."
        docker compose down -v
        docker system prune -f
        print_success "Очистка завершена"
        ;;

    "help"|*)
        echo "Использование: ./docker.sh [команда]"
        echo ""
        echo "Команды:"
        echo "  up, start      - Запустить контейнеры"
        echo "  down, stop     - Остановить контейнеры"
        echo "  restart        - Перезапустить контейнеры"
        echo "  build          - Собрать образы"
        echo "  logs           - Показать логи"
        echo "  shell, bash    - Войти в контейнер"
        echo "  artisan        - Запустить artisan команду"
        echo "  composer       - Запустить composer команду"
        echo "  npm            - Запустить npm команду"
        echo "  test           - Запустить тесты"
        echo "  migrate        - Запустить миграции"
        echo "  fresh          - Сбросить БД и запустить сидеры"
        echo "  tinker         - Запустить tinker"
        echo "  install        - Полная установка проекта"
        echo "  status         - Показать статус контейнеров"
        echo "  clean          - Очистить Docker ресурсы"
        echo "  help           - Показать эту справку"
        ;;
esac