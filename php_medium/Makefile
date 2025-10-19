.PHONY: help build up down restart logs shell clean production

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker image
	cd compose && docker-compose build

production: ## Start production (port 8010)
	cd compose && docker-compose up -d --build

up: production ## Alias for production

down: ## Stop containers
	cd compose && docker-compose down

restart: ## Restart containers
	cd compose && docker-compose restart

logs: ## Show logs
	cd compose && docker-compose logs -f

shell: ## Enter container shell
	docker exec -it environment_htb bash

clean: ## Stop and remove containers, volumes
	cd compose && docker-compose down -v

rebuild: ## Full rebuild (clean + build + up)
	cd compose && docker-compose down -v
	cd compose && docker-compose up -d --build --force-recreate

test: ## Test application endpoints
	@echo "Testing application (port 8010)..."
	@curl -s http://localhost:8010/ | grep -q "Environment" && echo "✅ Homepage works" || echo "❌ Homepage failed"
	@curl -s -o /dev/null -w "%{http_code}" http://localhost:8010/management/info | grep -q "302" && echo "✅ /info redirects (correct)" || echo "❌ /info should redirect"

status: ## Show container status
	docker ps | grep environment_htb

migrate: ## Run migrations
	docker exec -it environment_htb php artisan migrate

seed: ## Run seeders
	docker exec -it environment_htb php artisan db:seed

fresh: ## Fresh database (migrate + seed)
	docker exec -it environment_htb php artisan migrate:fresh --seed

env: ## Show environment
	docker exec -it environment_htb php artisan env

