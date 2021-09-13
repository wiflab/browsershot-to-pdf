# Browsershot to PDF

A self-hosted site that receives url pages, make screenshots, then render them as PDF. Supports rendering of multiple pages merged as a single PDF.

### Development
1. Clone this repository.
2. Install dependencies via docker.
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```
3. Build/run development container.
```shell
./vendor/bin/sail up -d
```
4. Copy `.env.example` and name it `.env`
```shell
cp .env.example .env
```
5. Generate app key.
```shell
sail artisan key:generate
```
6. By default, you can now access the app at `http://localhost:8080`
