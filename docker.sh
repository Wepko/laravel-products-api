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
        print_info "Установка проекта Laravel..."

        # Создаем .env файл если его нет
        if [ ! -f ".env" ]; then
            cp .env.example .env
            print_info "Создан .env файл"
        fi

        # Запускаем контейнеры
        docker compose up -d --build

        # Устанавливаем Laravel если проект пустой
        if [ ! -f "composer.json" ]; then
            print_info "Установка Laravel..."
            docker compose exec app composer create-project laravel/laravel . --prefer-dist
        fi

        # Устанавливаем зависимости
        print_info "Установка PHP зависимостей..."
        docker compose exec app composer install

        print_info "Установка Node.js зависимостей..."
        docker compose exec app npm install

        # Генерируем ключ приложения
        print_info "Генерация ключа приложения..."
        docker compose exec app php artisan key:generate

        # Запускаем миграции
        print_info "Запуск миграций..."
        docker compose exec app php artisan migrate

        # Устанавливаем права
        print_info "Установка прав..."
        docker compose exec app chmod -R 775 storage bootstrap/cache

        print_success "Установка завершена!"
        echo ""
        print_info "Доступные адреса:"
        echo "  Приложение:      http://localhost:8080"
        echo "  PHPMyAdmin:      http://localhost:8081 (root/secret)"
        echo "  Mailpit:         http://localhost:8025"
        echo "  Meilisearch:     http://localhost:7700"
        echo "  Elasticsearch:   http://localhost:9200"
        echo ""
        print_info "Доступные команды:"
        echo "  ./docker.sh up       - Запуск контейнеров"
        echo "  ./docker.sh down     - Остановка контейнеров"
        echo "  ./docker.sh artisan  - Запуск artisan команд"
        echo "  ./docker.sh shell    - Войти в контейнер"
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