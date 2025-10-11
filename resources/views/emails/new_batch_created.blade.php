@component('mail::message')
# New Batch Created

A new batch has been created for provider: **{{ $batch->provider_name }}**

- **Insurer ID:** {{ $batch->insurer_id }}
- **Batch Date:** {{ $batch->batch_date }}
- **Total Value:** ${{ number_format($batch->total_value, 2) }}
- **Status:** {{ ucfirst($batch->status) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
