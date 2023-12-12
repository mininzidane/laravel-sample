## Init a project

Build docker image
```shell
docker-compose up -d --build
```

Next install deps
```shell
docker-compose exec php composer i
```

Run migrations and seed data
```shell
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed
```

Can be viewed by http://127.0.0.1:8077/

If pass `user_id` and/or `partner_id` to the query string (http://127.0.0.1:8077/?user_id=2&partner_id=2), u can change user/partner

Site commission is being rounded to up (to 1 Satoshi as min), partner commission and cashback are being rounded to down

## Run tests

```shell
docker-compose exec php vendor/bin/phpunit
```
