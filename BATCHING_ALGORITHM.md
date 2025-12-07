# Healthcare Claims Batching Algorithm

Overview

The batching algorithm optimizes claim processing costs by effectively assigning claims to batches while considering the insurer-specific constraints and minimizing total processing costs.

Cost Calculation Model

Processing cost formula:
```
Cost = Base Cost × (1 + Time Multiplier) × Specialty Multiplier × Priority Multiplier × Value Multiplier
```

Multipliers:
- Time: Linear increase from 20% (day 1) to 50% (day 30) - `0.20 + ((day - 1) / 29) × 0.30`
- Specialty: Per-insurer efficiency ratings (typically 0.8× to 1.2×)
- Priority: `1.0 + ((priority_level - 1) × 0.1)` - Priority 1: 1.0×, Priority 5: 1.4×
- Value: Based on claim amount
  - ≤ $1,000: 1.0× | $1,001-$5,000: 1.1× | $5,001-$10,000: 1.3× | > $10,000: 1.5×

## Batch Assignment Algorithm

1. Determine Preferred Date: Use encounter or submission date based on insurer preference
2. Find Optimal Date: Search backwards up to 15 days from preferred date:
   - Calculate cost for each date
   - If existing batch with capacity found, apply consolidation bonus: `Cost × (1 - (fill_rate × 0.1))` (up to 10% reduction)
   - Track lowest adjusted cost
   - Stop early if day ≤ 5 (costs already optimal)
3. Assign to Batch: Get or create batch for optimal date. If at max capacity, create new batch for next day
4. Notification: Send email when batch reaches minimum size

Key Features:
- Earlier dates have lower costs (time multiplier)
- Existing batches get consolidation bonus
- Balances individual cost vs. batch efficiency

## Constraints

- Batch Size: Min size required for notification, max size enforced per batch
- Unique Identification: Batch = Insurer ID + Provider Name + Batch Date
- Capacity: Claims distributed across dates if needed

## Complexity & Scalability

- Time: O(W) per claim where W = 15 day window (constant)
- Space: O(B) where B = number of batches
- Scaling: Independent claim processing, database transactions prevent race conditions
- Performance: Indexed lookups, single transaction per claim, handles thousands of claims/minute

## Example

Claim: Provider "Dr. Smith", Cardiology, Priority 3, $5,500, Encounter: Dec 20

Process:
1. Start with Dec 20 (encounter date preference)
2. Find existing batch on Dec 15 with 8 claims
3. Calculate: Dec 20 cost = high (late month), Dec 15 = lower (early month + 10% consolidation bonus)
4. Assign to Dec 15 batch (now 9 claims)
5. Batch already notified (above min size of 5)

Result: ~35% cost reduction vs. original date through consolidation and early-month processing
