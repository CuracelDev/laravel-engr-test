<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch Ready Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2563eb; color: white; padding: 20px; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9fafb; padding: 20px; border-radius: 0 0 5px 5px; }
        .batch-info { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #2563eb; }
        .claim-item { padding: 10px; margin: 5px 0; background-color: white; border-radius: 3px; }
        .total { font-size: 18px; font-weight: bold; color: #2563eb; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Claims Batch Ready</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $insurer->name }},</p>
            
            <p>A new batch of claims is ready for processing:</p>
            
            <div class="batch-info">
                <strong>Batch Code:</strong> {{ $batch->batch_code }}<br>
                <strong>Batch Date:</strong> {{ $batch->batch_date->format('Y-m-d') }}<br>
                <strong>Number of Claims:</strong> {{ $batch->claim_count }}<br>
                <strong>Total Value:</strong> ₦{{ number_format($batch->total_value, 2) }}<br>
                <strong>Processing Cost:</strong> ₦{{ number_format($batch->processing_cost, 2) }}
            </div>

            <h3>Claims Summary:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Reference Code</th>
                        <th>Provider</th>
                        <th>Specialty</th>
                        <th>Priority</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($claims as $claim)
                    <tr>
                        <td>{{ $claim->claim_reference_code }}</td>
                        <td>{{ $claim->provider_name }}</td>
                        <td>{{ $claim->specialty }}</td>
                        <td>{{ ucfirst($claim->priority_level) }}</td>
                        <td>₦{{ number_format($claim->claim_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Total Batch Value: ₦{{ number_format($batch->total_value, 2) }}
            </div>

            <p>Please process this batch at your earliest convenience.</p>

            <p>Best regards,<br>Claims Batching System</p>
        </div>
    </div>
</body>
</html>
