<x-mail::message>
# Claim Added to Batch

Hello {{ $insurer->name }},

A new claim has been added to your batch. Here are the details:

## Claim Information
- **Claim ID**: {{ $claim->id }}
- **Provider**: {{ $claim->provider_name }}
- **Encounter Date**: {{ $claim->encounter_date->format('M d, Y') }}
- **Total Amount**: N{{ number_format($claim->total_amount, 2) }}
- **Processing Cost**: N{{ number_format($claim->processing_cost, 2) }}

## Batch Information
- **Batch ID**: {{ $batch->batch_identifier }}
- **Total Claims in Batch**: {{ $claimCount }}
- **Batch Date**: {{ $batch->batch_date->format('M d, Y') }}
- **Processing Date**: {{ $batch->processing_date->format('M d, Y') }}
- **Total Batch Amount**: N{{ number_format($totalAmount, 2) }}
- **Total Batch Processing Cost**: N{{ number_format($totalCost, 2) }}

Your claim has been successfully added to the batch and is scheduled for processing.

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
