set shell := ["bash", "-c"]

# Use FrankenPHP's bundled PHP for everything
php := "frankenphp php-cli"
composer := "./bin/composer"

pre-init:
	./setup.sh

init:
	@echo "Running project setup..."
	@if [ ! -f .env.local ]; then cp .env .env.local && echo "Created .env.local"; fi
	{{composer}} install --no-interaction
	@if [ -f package.json ]; then npm ci --no-audit --no-fund 2>/dev/null || npm install --no-audit --no-fund; fi
	mkdir -p var public/uploads
	touch var/data.db;
	{{php}} bin/console doctrine:schema:create
	{{php}} bin/console asset-map:compile -q
	just db-seed

update:
	{{composer}} update --no-interaction
	@if [ -f package.json ]; then npm update --no-audit --no-fund; fi
	{{php}} bin/console doctrine:migrations:migrate --no-interaction -q
	{{php}} bin/console asset-map:compile -q

test:
	{{php}} vendor/bin/behat

lint:
	vendor/bin/mago lint

analyze:
	vendor/bin/mago analyze

fmt:
	vendor/bin/mago fmt
	{{php}} vendor/bin/twig-cs-fixer fix src/ templates/

db-migrate-diff:
	{{php}} bin/console migrations:diff

db-migrate-squash:
	{{php}} bin/console migrations:squash

db-seed:
	{{php}} bin/console doctrine:fixtures:load

db-reset:
	{{php}} bin/console doctrine:database:drop --force --if-exists -q
	{{php}} bin/console doctrine:database:create --if-not-exists -q
	{{php}} bin/console doctrine:migrations:migrate --no-interaction -q

run:
	@trap 'kill 0' EXIT INT TERM; \
	echo "Starting FrankenPHP + WebSocket server + Queue..."; \
	frankenphp run --config Caddyfile & \
	{{php}} bin/console mv:ws-submission-producer & \
	{{php}} bin/console messenger:consume --all -vvv & \
	wait
