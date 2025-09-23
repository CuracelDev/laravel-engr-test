<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Curacel - Claims Batch Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .batch-details {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #475569;
        }
        .value {
            color: #1e293b;
        }
        .footer {
            background-color: #1e293b;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .processing-reason {
            background-color: #dbeafe;
            border-left: 4px solid #2563eb;
            padding: 12px;
            margin: 15px 0;
            border-radius: 0 6px 6px 0;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #059669;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Claims Batch Ready for Processing</h1>
        <p>Batch ID: {{ $batch->batch_identifier }}</p>
    </div>

    <div class="content">
        <div class="processing-reason">
            <strong>Processing Trigger:</strong> {{ $reasonText }}
        </div>

        <div class="batch-details">
            <h3>Batch Summary</h3>
            
            <div class="detail-row">
                <span class="label">Provider:</span>
                <span class="value">{{ $batch->provider_name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Insurer:</span>
                <span class="value">{{ $batch->insurer_code }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Batch Date:</span>
                <span class="value">{{ $batch->batch_date->format('F j, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Number of Claims:</span>
                <span class="value">{{ $batch->claims_count }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Total Amount:</span>
                <span class="value amount">${{ number_format($batch->total_amount, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Processing Cost:</span>
                <span class="value">${{ number_format($batch->processing_cost, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Processing Fee Rate:</span>
                <span class="value">{{ number_format(($batch->processing_cost / $batch->total_amount) * 100, 2) }}%</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value">{{ ucfirst($batch->status) }}</span>
            </div>
        </div>

        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Review the batch details above</li>
            <li>Process the claims according to your standard procedures</li>
            <li>Update claim statuses upon completion</li>
        </ul>

        <p>If you have any questions about this batch, please contact our claims processing team.</p>
    </div>

    <div class="footer">
        <p>Curacel Healthcare Claims Processing Platform</p>
    </div>
</body>
</html>