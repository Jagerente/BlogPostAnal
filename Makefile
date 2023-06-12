up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose down -v --remove-orphans
	docker-compose rm -vsf
	docker-compose up -d --build


test:
	docker-compose exec php vendor/bin/phpunit ./tests

bash:
	docker-compose exec -u www-data php bash

bash_root:
	docker-compose exec -u 0 php bash

psql:
	docker-compose exec postgres psql -U postgres postgres

create-migration:
	docker-compose exec php bin/console make:migration

migrate:
	docker-compose exec php bin/console doctrine:migrations:migrate

migration:
	docker-compose exec php bin/console make:migration
	docker-compose exec php bin/console doctrine:migrations:migrate

fixture:
	docker-compose exec php bin/console doctrine:fixtures:load

fixture-append:
	docker-compose exec php bin/console doctrine:fixtures:load --append

rebuild:
	docker-compose down
	docker-compose down -v --remove-orphans
	docker-compose rm -vsf
	docker-compose up -d --build
	docker-compose exec php bin/console make:migration
	docker-compose exec php bin/console doctrine:migrations:migrate
	docker-compose exec php bin/console doctrine:fixtures:load
	docker-compose restart go
