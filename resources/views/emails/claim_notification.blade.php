<h2>New Claim Submitted</h2>

<p><strong>Provider:</strong> {{ $claim->provider_name }}</p>
<p><strong>Specialty:</strong> {{ $claim->specialty }}</p>
<p><strong>Total Amount:</strong> ₦{{ number_format($claim->total_amount, 2) }}</p>
<p><strong>Priority:</strong> {{ $claim->priority_level }}</p>
<p><strong>Batch:</strong> {{ optional($claim->batch)->name ?? 'Not yet batched' }}</p>

<p>Check your dashboard to process this claim.</p>
