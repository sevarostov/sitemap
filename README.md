# sitemap
The website map generation module based on PHP+MYSQL with docker env

## Technical Requirements & Installation

- Git
- Docker
- Docker Compose

## Settings

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
