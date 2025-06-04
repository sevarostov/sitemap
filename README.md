# sitemap
The website map generation module based on PHP 8.3 + MYSQL 9.3.0  with docker environment

## Technical Requirements 
- Git
- Docker
- Docker Compose

## Settings & Installation

### 1. Cloning repo

   ```sh
   git clone git@github.com:sevarostov/sitemap.git
   ```

### 2. Copying env file
  
  ```sh
  scp .env.example .env
  ```

### 3. Building project with docker
  
   ```sh
  docker-compose build 
  docker compose exec php composer install
  docker compose up -d
  ```
### 4. Running migrations

 ```sh
  docker compose exec php php bin/console doctrine:migrations:migrate
  ```

### 5. Seeding fixures (DEV env only)

 ```sh
  docker compose exec php php bin/console doctrine:fixtures:load --append
  ```

### 6.1 Generating sitemap (Сгенерировать по команде карты сайта.)

 ```sh
  docker compose exec php php bin/console s:g
  ```

### 6.2 Changing sitemap (Добавить / удалить / изменить отдельные записи для конкретной карты сайта по критерию)

 ```sh

  docker compose exec php php bin/console s:g main

  ```
### 6.2 Update sitemap (Обновить карты сайта. Удаление несуществующих карт, если изменили название.)

 ```sh

  docker compose exec php php bin/console s:g maps update

  ```

### 7. Web

  http://localhost
  press "start" if you want to  load fixures manually.

  http://localhost/sitemaps/sitemap.xml
  to see created sitemap file
