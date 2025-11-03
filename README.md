


## Setup

Same way you would install a typical laravel application.

    composer install

    npm install

    npm run dev

    php artisan serve

The UI is displayed on the root page

## Extra Notes

## Batching Algorithm — Approach & Design

### Overview
This batching algorithm optimizes healthcare claim processing by grouping claims into batches based on cost efficiency, insurer preferences, and operational constraints.

It uses a **greedy, marginal-cost optimization algorithm** that makes decisions incrementally each time a new claim is submitted, ensuring both scalability and simplicity.

---
### Inputs Considered

#### Each Claim includes:
- encounter_date
- submission_date
- specialty
- priority_level
- total_amount

#### Each Insurer defines:
- daily_capacity (maximum claim value processed per day)
- min_batch_size / max_batch_size
- batch_by rule (encounter or submission)
- specialty_efficiency multipliers (e.g. cardiology = 0.9)
- email for notification

### Core Logic

#### 1. Determine the Batch Date
Each insurer specifies whether batches are grouped by the claim’s **`encounter_date`** or **`submission_date`**.  
```php
$batchDate = ($insurer->batch_by === 'encounter')
    ? $claim->encounter_date
    : $claim->submission_date;
```

#### 2. Compute Claim Processing Cost
Each claim’s cost is estimated based on multiple weighted factors:
--- Range: 20% to 50% of claim value
```php
basePercent = 20% + ((dayOfMonth - 1) / 29) * (50% - 20%);
claimCost = basePercent * specialtyEfficiency * priorityFactor * log(totalAmount + 1);
```

#### 3. Evaluate Marginal Cost for Each Batch (Batch Selection Process)
For each candidate batch, estimate how adding this claim affects total cost.
Choose existing batch if: lowest marginal_cost ≤ new_batch_cost
Otherwise create new batch with 5% overhead penalty
```php
foreach ($existingBatch as $batch) {
    // Skip if batch would exceed capacity
    if ($batch->count + 1 > $insurer->max_batch_size) continue;
    
    // Calculate marginal cost
    $marginal = $claimCost * (1 + ($batch->total_amount / $insurer->daily_capacity));
}
```

#### 4. Assign Claim, Update Records & Notify Insurer
Once assigned:
The claim’s batch_id, and batch’s total_cost & claim_count are updated.
If the batch reaches the insurer’s min_batch_size or max_batch_size,
an email notification is queued to alert the insurer.

| Property            | Description                                                    |
| ------------------- | -------------------------------------------------------------- |
| **Algorithm Type**  | Greedy incremental, marginal-cost based                                    |
| **Time Complexity** | O(k) per claim (k = number of active batches per insurer/date) |
| **Scalability**     | Works efficiently for thousands of claims/day                  |

#### 5. Benefits
Keeps per-claim processing fast (real-time O(1–n))
Reduces processing cost dynamically using real data
Automatically balances load across days and providers
Cleanly extendable for scheduled re-batching jobs
