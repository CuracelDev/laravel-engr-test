<h2>New Batch Summary</h2>
<p><strong>Provider:</strong> {{ $batch->provider_name }}</p>
<p><strong>Date:</strong> {{ $batch->batch_date }}</p>
<p><strong>Total Claims:</strong> {{ $batch->claims->count() }}</p>
<p><strong>Total Amount:</strong> ${{ $batch->claims->sum('total_amount') }}</p>
<p><strong>Processing Cost:</strong> ${{ $batch->claims->sum('processing_cost') }}</p>
<ul>
  @foreach($batch->claims as $c)
    <li>Claim #{{ $c->id }} – ${{ $c->total_amount }} (Processing Cost: ${{ $c->processing_cost }})</li>
  @endforeach
</ul>