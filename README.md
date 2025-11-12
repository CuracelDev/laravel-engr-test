


# Healthcare Claims Processing Platform

A Laravel + Vue.js application for optimizing healthcare claim batching and processing between Providers and Insurers.

## Features

- **Intelligent Batching**: Automatically groups claims by provider and date
- **Cost Optimization**: Multi-factor cost calculation (time, specialty, priority, value)
- **Constraint Management**: Respects insurer batch sizes, capacity limits, and date preferences
- **Laravel Actions**: Clean, reusable business logic using Laravel Actions package
- **Automated Processing**: Console command for batch processing
- **Email Notifications**: Automatic insurer notifications
- **Comprehensive Testing**: Full test coverage for all features

## Setup

### Installation

```bash
composer install
npm install
```

### Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and mail settings in `.env`

### Database Setup

```bash
php artisan migrate
php artisan db:seed --class=InsurerSeeder
```

### Run Application

```bash
npm run dev
php artisan serve
```

The UI is displayed on the root page at `http://localhost:8000`

## Architecture

### Laravel Actions

The application uses [Laravel Actions](https://laravelactions.com/) for business logic:

- **SubmitClaim**: Validates and creates claims
- **BatchClaim**: Assigns claims to batches with cost calculation
- **ProcessBatch**: Evaluates and processes batches
- **NotifyInsurer**: Sends email notifications

### Batching Algorithm

See [BATCHING_ALGORITHM.md](BATCHING_ALGORITHM.md) for detailed explanation.

**Key Features:**
- Time-based cost optimization (20%-50% throughout month)
- Specialty-specific processing costs
- Priority-based cost multipliers
- Value-proportional costs
- Automatic batch processing triggers

## API Endpoints

- `GET /` - Claim submission form
- `POST /api/claims` - Submit new claim

## Testing

```bash
php artisan test
```

## Batch Processing

```bash
# Manual processing
php artisan claims:process-batches

# Schedule daily (add to app/Console/Kernel.php)
$schedule->command('claims:process-batches')->daily();
```

## Extra Notes

- Built with Laravel 11 and Vue 3
- Uses Inertia.js for seamless SPA experience
- Tailwind CSS for styling
- Comprehensive validation and error handling



