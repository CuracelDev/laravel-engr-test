<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch Ready for Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a5568;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f7fafc;
        }
        .batch-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4299e1;
        }
        .info-row {
            margin: 10px 0;
        }
        .label {
            font-weight: bold;
            color: #2d3748;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Claims Batch Ready for Processing</h1>
        </div>

        <div class="content">
            <p>Dear {{ $batch->insurer->name }},</p>

            <p>A new batch of claims is ready for processing with the following details:</p>

            <div class="batch-info">
                <div class="info-row">
                    <span class="label">Provider:</span> {{ $batch->provider_name }}
                </div>
                <div class="info-row">
                    <span class="label">Batch Date:</span> {{ $batch->batch_date->format('F d, Y') }}
                </div>
                <div class="info-row">
                    <span class="label">Number of Claims:</span> {{ $batch->claim_count }}
                </div>
                <div class="info-row">
                    <span class="label">Total Amount:</span> ${{ number_format($batch->total_amount, 2) }}
                </div>
                <div class="info-row">
                    <span class="label">Estimated Processing Cost:</span> ${{ number_format($batch->processing_cost, 2) }}
                </div>
            </div>

            <p>This batch has been optimized to minimize processing costs while meeting your capacity and batch size requirements.</p>

            <p>Please process this batch at your earliest convenience.</p>
        </div>

        <div class="footer">
            <p>This is an automated message from the Healthcare Claims Processing System</p>
        </div>
    </div>
</body>
</html>
