<!doctype html>
<html>
  <body>
    <h2>New Claim Batched</h2>
    <p><strong>Batch:</strong> {{ $batch->batch_code }} ({{ $batch->batch_date->format('Y-m-d') }})</p>
    <p><strong>Provider:</strong> {{ $batch->provider_name }}</p>
    <p><strong>Claims in Batch:</strong> {{ $batch->claims_count }}</p>
    <p><strong>Total Amount:</strong> {{ number_format($batch->total_amount, 2) }}</p>

    <hr>
    <h3>Claim Details</h3>
    <p><strong>Specialty:</strong> {{ $claim->specialty }}</p>
    <p><strong>Priority:</strong> {{ $claim->priority_level }}</p>
    <p><strong>Total Value:</strong> {{ number_format($claim->total_value, 2) }}</p>
    <p><strong>Encounter Date:</strong> {{ $claim->encounter_date->format('Y-m-d') }}</p>
    <p><strong>Submission Date:</strong> {{ $claim->submission_date->format('Y-m-d') }}</p>
  </body>
</html>