# Healthcare Claims Batching Algorithm

## Overview

This system implements an intelligent batching algorithm for healthcare claims processing that minimizes total processing costs while respecting insurer constraints.

## Algorithm Approach

### 1. Cost Calculation Strategy

The processing cost for each claim is calculated using a multi-factor formula:

```
Processing Cost = Base Cost × Specialty Multiplier × Priority Multiplier × Value Multiplier
```

Where:
- **Base Cost**: Increases linearly from 20% on the 1st to 50% on the 30th of the month
- **Specialty Multiplier**: Insurer-specific efficiency rates for different medical specialties
- **Priority Multiplier**: Higher priority claims incur higher processing costs
- **Value Multiplier**: Cost increases with claim monetary value

### 2. Batching Logic

Claims are batched using the following criteria:

1. **Batch Identification**: `Provider Name + Date` (based on insurer's date preference)
2. **Date Preference**: Insurers can choose between encounter date or submission date
3. **Size Constraints**: Each batch must meet minimum and maximum size requirements
4. **Processing Triggers**:
   - Batch reaches maximum size
   - Batch is from previous day (daily processing cycle)
   - Manual processing command

### 3. Optimization Features

#### Dynamic Processing
- Claims are processed immediately when batch size limits are reached
- Daily batch processing ensures timely claim handling
- Automatic email notifications to insurers

#### Cost Minimization
- Time-based processing delays for cost optimization
- Specialty-aware routing to efficient insurers
- Priority-based cost calculations

#### Scalability
- Indexed database queries for performance
- Batch processing to handle large volumes
- Configurable insurer constraints

## Implementation Details

### Database Schema

**Insurers Table**:
- Processing constraints (capacity, batch sizes)
- Cost multipliers by specialty and priority
- Date preferences and contact information

**Claims Table**:
- Comprehensive claim data with items
- Batch tracking and processing status
- Cost calculations and audit trail

### Key Components

1. **SubmitClaim Action**: Validates and creates claims
2. **BatchClaim Action**: Assigns claims to batches with cost calculation
3. **ProcessBatch Action**: Evaluates and processes batches based on constraints
4. **NotifyInsurer Action**: Sends email notifications to insurers
5. **ClaimController**: Orchestrates actions for API endpoints

### Laravel Actions Architecture

The system uses Laravel Actions for clean, reusable business logic:
- Each action is a single-responsibility class
- Actions can be called from controllers, commands, jobs, or other actions
- Built-in validation and authorization support
- Testable and maintainable code structure

### Performance Characteristics

- **Time Complexity**: O(n log n) for batch sorting and processing
- **Space Complexity**: O(n) for claim storage and indexing
- **Scalability**: Horizontal scaling through database partitioning

## Usage

### Claim Submission
1. Provider submits claim through web interface
2. System validates and calculates costs
3. Claim is automatically batched based on insurer preferences
4. Processing occurs when batch criteria are met

### Batch Processing
```bash
# Manual batch processing
php artisan claims:process-batches

# Scheduled processing (recommended daily)
# Add to scheduler in app/Console/Kernel.php
```

### Configuration
Insurer settings can be modified through database seeders or admin interface:
- Processing costs and multipliers
- Batch size constraints
- Date preferences
- Capacity limits

## Testing

Comprehensive test suite covers:
- Claim validation and submission
- Batch processing logic
- Cost calculation accuracy
- Email notification delivery
- Edge cases and error handling

Run tests:
```bash
php artisan test
```

## Future Enhancements

1. **Machine Learning**: Predictive cost optimization
2. **Real-time Analytics**: Processing cost dashboards
3. **API Integration**: External insurer system connectivity
4. **Advanced Scheduling**: Multi-day batch optimization