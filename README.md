# sitemap
The website map generation module based on PHP 8.3 + MYSQL 9.3.0  with docker env

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
