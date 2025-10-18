<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Ready for Processing</title>
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
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .batch-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #4F46E5;
        }
        .batch-info p {
            margin: 5px 0;
        }
        .claims-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        .claims-table th,
        .claims-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .claims-table th {
            background-color: #4F46E5;
            color: white;
        }
        .claims-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .highlight {
            color: #4F46E5;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Batch Ready for Processing</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $insurer->name }},</p>
        
        <p>A new batch of claims is ready for processing.</p>
        
        <div class="batch-info">
            <h3>Batch Information</h3>
            <p><strong>Batch ID:</strong> {{ $batch->identifier }}</p>
            <p><strong>Batch Date:</strong> {{ $batch->batch_date->format('F d, Y') }}</p>
            <p><strong>Total Claims:</strong> <span class="highlight">{{ $batch->total_claims }}</span></p>
            <p><strong>Total Amount:</strong> <span class="highlight">${{ number_format($batch->total_amount, 2) }}</span></p>
            <p><strong>Status:</strong> {{ ucfirst($batch->status) }}</p>
            <p><strong>Optimized At:</strong> {{ $batch->optimized_at?->format('F d, Y H:i:s') ?? 'N/A' }}</p>
        </div>
        
        <h3>Claims Details</h3>
        <table class="claims-table">
            <thead>
                <tr>
                    <th>Claim ID</th>
                    <th>Provider</th>
                    <th>Specialty</th>
                    <th>Priority</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($claims as $claim)
                <tr>
                    <td>{{ $claim->id }}</td>
                    <td>{{ $claim->provider_name }}</td>
                    <td>{{ ucfirst($claim->specialty) }}</td>
                    <td>{{ $claim->priority_level }}</td>
                    <td>${{ number_format($claim->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <p style="margin-top: 20px;">
            This batch has been optimized to minimize processing costs and is ready for your review and processing.
        </p>
        
        <p>
            If you have any questions or concerns, please contact our support team.
        </p>
        
        <p>
            Best regards,<br>
            <strong>Claims Processing System</strong>
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} Claims Processing Platform. All rights reserved.</p>
        <p>This is an automated notification. Please do not reply to this email.</p>
    </div>
</body>
</html>

