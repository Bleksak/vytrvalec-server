setup:
    composer install
    npm install
    bin/console doctrine:schema:create
    mkdir -p public/uploads
    bin/console asset-map:compile

update:
    composer update
    npm update
    bin/console doctrine:schema:update
    bin/console asset-map:compile

test:
    vendor/bin/behat

lint:
    vendor/bin/mago lint

analyze:
    vendor/bin/mago analyze

fmt:
    vendor/bin/mago fmt
    vendor/bin/twig-cs-fixer fix src/ templates/

db:migrate:diff:
    bin/console migrations:diff

db:migrate:squash:
    bin/console migrations:squash

db:seed:
    bin/console doctrine:fixtures:load
