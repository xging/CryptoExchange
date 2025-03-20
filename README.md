# CryptoExchange
Practice task

# Project Documentation

## Notes
### API
- Coingecko API is used for fetching crypto data.
### Live Update
- Currency pairs which are stored in the currency_pairs table, their exchange rate and hist data, will be updated every 1 minute. Scheduled by cron.
### Fixtures
- Three custom exchange rate pairs are loaded into the currency_pairs table by default.
### Check tables:
- php bin/console doctrine:query:sql "SELECT * FROM exchange_rate;"
- php bin/console doctrine:query:sql "SELECT * FROM exchange_rate_hist;"
- php bin/console doctrine:query:sql "SELECT * FROM currency_pairs;"

---

## Installation
Follow these steps to install the project:
- Necessary to Install Docker and Composer
  
1. Clone the repository:
   ```bash
   git clone https://github.com/xging/CryptoExchange.git
   
2. Build and start containers with Docker Compose
   ```bash
   docker-compose up --build
   
## Usage
### * In order to run the async process, you need to run the consumer (no need to run the consumer for sync messages)
### * The watch-pair command will be executed every minute as scheduled by Crontab.
### * Execute commands in Docker PHP container
    docker exec -it symfony-php-container bash
    
0. Run RabbitMQ сonsumer
   ```bash
   php bin/console messenger:consume -vv
1. It is possible to add a currency pair to the queue for processing (Async)
   ```bash
   php bin/console app:add-pair "bitcoin eur"
2. It is possible to кemove сurrency pairs from the list (Async)
   ```bash
   php bin/console app:remove-pair "bitcoin eur"
3. Update exchange rate and save to hist manually (Sync)
   ```bash
   php bin/console app:watch-pair

## HTTP Request
### * Redis is used to cache request output.
0. Check Redis Keys:
   ```bash
   docker exec -it symfony-redis-container redis-cli
     KEYS *
   
1. Find current exchange rate by selected currencies:
   ```bash
   http://localhost:8080/api/currency/exchange-rate?from=bitcoin&to=eur

2. Find history records by selected currencies:
   ```bash
   http://localhost:8080/api/currency/exchange-rate-hist?from=ethereum&to=eur

3. Find history records by selected currencies and date (YYYY-MM-DD):
   ```bash
   http://localhost:8080/api/currency/exchange-rate-hist?from=bitcoin&to=eur&date=2025-03-19

4. Find history records by selected currencies, date (YYYY-MM-DD), and time (HH:MM:SS):
   ```bash
   http://localhost:8080/api/currency/exchange-rate-hist?from=bitcoin&to=eur&date=2025-03-19&time=22:22:05


