## Setup. Build docker container, run migrations and create database, seed projects data
```bash
docker-compose up -d
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed
```

## URLs
- List page http://127.0.0.1:8018/tasks
