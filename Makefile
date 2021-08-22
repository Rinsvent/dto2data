auth:
	docker exec -it -u1000:1000 dto2data_php bash

auth-root:
	docker exec -it dto2data_php bash

test:
	bin/codecept run $p

coverage:
	vendor/bin/codecept run --coverage --coverage-html=/app/var/temp.html

# make out container
coverage-open:
	google-chrome var/temp.html/index.html

#docker
start:
	docker-compose up -d
stop:
	docker-compose down
pull:
	docker-compose pull
restart: stop start
restart-php:
	docker-compose restart backend-php-fpm
down-clear:
	docker-compose down -v --remove-orphans
init: down-clear pull start

#prepare
prepare-environment:
	bash bin/docker/prepare.sh
