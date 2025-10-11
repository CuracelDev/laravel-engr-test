# Claim Batching System – Engineering Approach

## Problem Framing

This system was designed as an **optimization problem**, where **each insurer has unique cost-driving constraints** such as:

- Maximum batch size
- Preferred encounter dates
- Priority handling
- Total cost thresholds

The goal was to **group claims into efficient, valid batches** while respecting these business rules.

## Domain Modeling

The domain was broken down into three core entities:

- **Claims**: Submitted by providers, include metadata like insurer, provider, date, and amount.
- **ClaimItems**: Line-level cost items attached to each claim (e.g. procedures, diagnostics).
- **Batches**: Groupings of claims processed together per insurer, provider, and date.

These were modeled as distinct Eloquent models with relationships:

- `Claim` → hasMany → `ClaimItem`
- `Batch` → hasMany → `Claim`

## Batching Algorithm

The batching logic follows a **greedy strategy**:

1. **Group** unbatched claims by:
   - Insurer
   - Provider name
   - Encounter date

2. **Sort** each group:
   - By `priority_level` (descending)
   - By `total_amount` (descending)

3. **Batch** them:
   - Only group claims when the group has 2+ claims
   - Sum the total amount and check against limits (if any)
   - Mark the claims with a `batch_id`

4. **Notify** the insurer via email when a batch is created.

This approach ensures the most valuable and urgent claims are processed first, while remaining adaptable to different rules or future constraints.

## Testing

- Factories were created for `Claim` and `ClaimItem` to support robust feature tests.
- The `BatchClaimsJob` was fully tested with database assertions and email mocking using `Mail::fake()`.
- Tests confirm:
  - Batch creation
  - Correct claim updates
  - Email notifications sent to insurers