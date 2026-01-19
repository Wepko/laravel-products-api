.PHONY: help build up down restart shell logs composer npm artisan migrate seed fresh tinker test

help:
	@echo "Доступные команды:"
	@echo "  build        - Собрать Docker образы"
	@echo "  up           - Запустить контейнеры"
	@echo "  down         - Остановить контейнеры"
	@echo "  restart      - Перезапустить контейнеры"
	@echo "  shell        - Войти в контейнер приложения"
	@echo "  logs         - Показать логи контейнеров"
	@echo "  composer     - Запустить composer команду"
	@echo "  npm          - Запустить npm команду"
	@echo "  artisan      - Запустить artisan команду"
	@echo "  migrate      - Запустить миграции"
	@echo "  seed         - Запустить сидеры"
	@echo "  fresh        - Сбросить и запустить миграции с сидерами"
	@echo "  tinker       - Запустить tinker"
	@echo "  test         - Запустить тесты"

build:
	docker-compose build --no-cache

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

shell:
	docker-compose exec app bash

logs:
	docker-compose logs -f

composer:
	docker-compose exec app composer $(filter-out $@,$(MAKECMDGOALS))

npm:
	docker-compose exec app npm $(filter-out $@,$(MAKECMDGOALS))

artisan:
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

migrate:
	docker-compose exec app php artisan migrate

seed:
	docker-compose exec app php artisan db:seed

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

tinker:
	docker-compose exec app php artisan tinker

test:
	docker-compose exec app php artisan test

%:
	@: