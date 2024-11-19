


## Setup

Same way you would install a typical laravel application.

    composer install

    npm install

    npm run dev

    php artisan serve

The UI is displayed on the root page

## Extra Notes
## The batching algorithm in this code processes claims and groups them into batches for efficient handling based on insurer-defined constraints. Here's how the approach is structured:

#### Initial Setup and Claim Processing Costs:

## The action begins by calculating the total processing cost for each claim using the CalculateProcessingCostAction. The cost for each claim is accumulated in $finalcost. Claims are then sorted based on two factors: priority (higher priority first) and monetary value (higher value first). This ensures that claims with higher importance are processed first.


### The checkDailyLimitAndSortByDate method is intended to ensure that the total processing cost of claims for a particular day does not exceed a set limit. Claims are removed from the batch (starting from the lowest priority) until the total processing cost is within the allowed range.

# Batch Size Constraints and Claim Grouping:
###  The algorithm ensures that claims are processed in batches that meet the minimum and maximum batch size constraints set by the insurer.

### Claims are grouped by provider and date (based on insurer preferences) using the groupClaimsByProviderAndDate method. This grouping helps in organizing claims based on the provider and their relevant date preference (either encounter_date or created_at).

### Splitting Claims into Smaller Batches:

### If the number of claims in a batch exceeds the insurer’s maximum batch size, the algorithm splits the claims into smaller batches. Each batch is then inserted into the job_batches table. If a batch is smaller than or equal to the maximum batch size, it is inserted directly as one batch.
## Insertion into Job Batches:

### Each batch is inserted into the job_batches table, and the individual claims in the batch are associated with their respective batch.

## Key Methods:
1. sortByPriorityAndMonitaryValue($claims): Sorts claims first by priority and then by total value in descending order.
2. checkDailyLimitAndSortByDate($claims, $expectedTotal): Ensures that the total processing cost does not exceed the expected limit for the day by removing lower-priority claims.
3. sortWithMinMax($claims): Groups claims by provider and date, then splits batches if they exceed the maximum batch size.
4. groupClaimsByProviderAndDate($claims, $datePreference): Groups claims by provider and the selected date, allowing for batch processing based on both criteria.
5. insertIntoJobBatches($claimsBatch): Inserts each batch of claims into the job_batches table, attaching each claim to the respective batch.
6. Daily Processing Cost Calculation:
The DailyMaxProcessingCostAction calculates the maximum amount that can be processed on the current day based on the monthly limit and a daily incremental percentage. This ensures that the daily processing amount is spread out evenly across the month but can also adjust based on the current day.

## Summary:
##  This batching algorithm efficiently groups and processes claims by sorting them based on priority and monetary value, ensuring that they are processed within defined daily and batch size limits. Claims are grouped by provider and date preference, with the ability to split large batches into smaller ones. The daily processing cost is calculated dynamically to ensure compliance with insurer limits.



```bash
Install the dependencies
Run composer install to install the PHP dependencies for your Laravel application:

bash
Copy code
composer install
c. Copy .env file
If you don’t have an .env file, create one by copying .env.example:

bash
Copy code
cp .env.example .env
2. Set Up Redis for Cache and Queueing
Redis is commonly used in Laravel for caching and queueing. You need to configure Redis in your .env file and the Redis configuration file.

a. Install Redis Server (If Redis is not installed)
If Redis is not installed on your local system or server, you can install it by running the following commands based on your OS.

For Ubuntu/Debian:
bash
Copy code
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
For MacOS (with Homebrew):
bash
Copy code
brew install redis
brew services start redis
Verify Redis is Running:
bash
Copy code
redis-cli ping
You should get the response PONG from the Redis server.

b. Update .env for Redis
In the .env file, set the Redis configuration for the cache and queue services:

env
Copy code
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
This tells Laravel to use Redis for caching, session, and queue management.```

```shell
Update .env for Email Configuration
Configure your email service provider (SMTP, Mailgun, etc.) in the .env file.

For example, using Gmail’s SMTP server:

env
Copy code
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```