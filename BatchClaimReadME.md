**Batching Algorithm Explanation**

## Overview

This algorithm is designed to batch healthcare claims in a way that minimizes insurers total processing costs. It groups claims into batches based on insurers' preferences and constraints, such as daily capacity, batch size limits, and processing cost. Each batch is identified by the provider's name and a specific date ensuring efficient processing by insurers.

## Key Features

- **Date Selection**: Uses the insurer’s preferred date (encounter or submission date) to determine the batch date.  
- **Daily Capacity Checks**: Ensures insurers don’t exceed their daily processing limits.  
- **Batch Size Constraints**: Groups claims into batches that meet the insurer’s minimum and maximum size requirements.  
- **Cost Optimization**: Factors in time of month, specialty efficiency, priority, and claim value to calculate processing costs.

## How It Works

### Step 1: Determine the Batch Date

The insurer’s preference dictates whether the `encounter_date` or `submission_date` of the claim is used as the batch date. e.g:

> If an insurer prefers `encounter_date` a claim with an encounter on Jan 5 will be batched as **Provider A Jan 5 2023**.

### Step 2: Create or Find a Batch

The algorithm checks for an existing batch matching:

- Provider name  
- Batch date (from Step 1)  
- Insurer code  

If none exists a new batch is created.

### Step 3: Check Daily Capacity

Each insurer has a daily claim limit (e.g 100 claims per day).

The algorithm:

1. Sums all claims across batches for the insurer on the target date.  
2. If adding the new claim exceeds the limit the batch date moves to the next day (e.g from Jan 5 to Jan 6).

### Step 4: Validate Batch Size

Each batch must stay within the insurer’s `min_batch_size` and `max_batch_size`. e.g:

- If a batch has 4 claims and the insurer requires 5–10 claims per batch:
  - Adding a 5th claim keeps it within limits.
- If the batch already has 10 claims a new batch is created for the same date.

### Step 5: Assign the Claim and Calculate Costs

Once the batch is finalized:

1. The claim is added to the batch.  
2. Processing costs are calculated using:
   - **Base cost**: Fixed starting cost.  
   - **Time of month**: Costs increase linearly from the 1st (20%) to the 30th (50%).  
   - **Specialty efficiency**: Insurers may process certain specialties faster or cheaper.  
   - **Priority level**: Higher priority = higher cost (e.g priority 5 adds 50% cost).  
   - **Claim value**: Higher monetary value = slightly higher cost.

## Handling Constraints

- **Daily Capacity**: Automatically shifts to the next day if the limit is reached.  
- **Batch Sizes**: Creates new batches if adding a claim would violate size limits even if this results in smaller batches.

## Cost Minimization Strategy

By grouping claims into batches that align with:

- Preferred dates to avoid delays  
- Daily capacity to prevent overloading  
- Optimal batch sizes to balance efficiency  
- Cost factors like specialty and time of month  

This ensures insurers process claims at the lowest possible cost while respecting their operational limits.

## Example Scenario

**Claim**: Encounter date = Jan 5, Priority = 3, Specialty = Cardiology, Value = $1,000.  
**Insurer Preferences**:

- `encounter_date` for batching  
- Daily capacity = 50 claims  
- Min/max batch size = 5–10  

**Outcome**:

- The claim is assigned to a Jan 5 batch.  
- If the Jan 5 batch has 10 claims a new Jan 5 batch is created.  
- Processing cost is calculated with a 30% time-of-month factor (mid-month) and cardiology efficiency.
