# Healthcare Claims System

A Laravel with  laravel ui of Vue.js, it optimizes healthcare claims processing between providers and insurers using intelligent batching algorithms to minimize processing costs.


### Core Batching Algorithm

The ClaimBatchingService implements multi-constraint optimization:

```
Processing Cost = Base Cost × Time Multiplier × Specialty Efficiency × Priority Multiplier × Value Multiplier

Where:
- Base Cost = Claim Amount × 2%
- Time Multiplier = 0.20 + ((Day of Month - 1) / 29) × 0.30
- Specialty Efficiency = Insurer-specific rating (0.8-1.3x)
- Priority Multiplier = 1 + ((Priority Level - 1) × 0.25)
- Value Multiplier = 1 + (Claim Amount × Insurer Value Multiplier)
```

### Key Features

- **Dynamic Claim Submission**: Vue.js interface with multi-item support and real-time calculations
- **Intelligent Batching**: Groups claims by Provider + Date + Insurer for optimal cost efficiency
- **Automated Processing**: Triggers batch processing based on size, efficiency, or end-of-day rules
- **Email Notifications**: Automated alerts to insurers when batches are ready
- **Capacity Management**: Respects daily processing limits and batch size constraints

## Installation

```bash
# Install dependencies
composer install
npm install

## Note: I encountered intallation error when used npm - I finally start the project with stress using pnpm.

# Setup database
php artisan migrate:fresh
php artisan db:seed

# Start development servers
php artisan serve
npm run dev
```

## API Endpoints

```
POST /api/claims              # Submit new claim
GET  /api/claims              # List claims with filters
GET  /api/batches            # List batches
GET  /api/insurers    # Available insurers
GET  /api/specialties # Medical specialties
```

## Database Schema

- **claims**: Individual medical claims with items
- **batches**: Optimized claim groupings for processing
- **insurers**: Insurance companies with configurations
- **insurer_configurations**: Processing constraints per insurer

## Testing

The system includes comprehensive tests covering:

- Claim submission and validation
- Batch consolidation logic
- Cost calculation accuracy
- Processing triggers
- Email notifications

```bash
php artisan test
```

## Business Impact

The batching algorithm demonstrates:

- **Cost Optimization**: Reduces processing fees through intelligent grouping
- **Constraint Handling**: Respects all business rules automatically  
- **Scalability**: Handles enterprise-level transaction volumes
- **Automation**: Eliminates manual batching coordination

## Configuration

Insurers can be configured with different:
- Processing capacities
- Batch size limits 
- Specialty efficiency multipliers
- Date preferences for batching

