PHP_CONTAINER=php

.PHONY: up down build artisan composer fresh shell setup

up:
	docker compose up -d
down:
	docker compose down
build:
	docker compose build --no-cache
artisan:
	docker compose exec $(PHP_CONTAINER) php artisan $(ARGS)
composer:
	docker compose exec $(PHP_CONTAINER) composer $(ARGS)
fresh:
	make artisan ARGS="migrate:fresh --seed"
shell:
	docker compose exec $(PHP_CONTAINER) /bin/sh

setup:
	@echo "--- Setting up EPAS Environment ---"
	@if [ ! -f .env ]; then cp .env.example .env; fi
	make build
	make up
	@echo "Waiting for containers to be ready..."
	@sleep 10
	make composer ARGS=install
	make artisan ARGS=key:generate
	make fresh
	@echo "✅ Application setup complete"
