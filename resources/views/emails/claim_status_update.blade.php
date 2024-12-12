<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claims Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h1 {
            color: #4CAF50;
            font-size: 24px;
        }
        .email-body {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #4CAF50;
            color: white;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .email-footer {
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>Claims Status Update</h1>
    </div>
    <div class="email-body">
        <p>Dear {{ $insurerName }},</p>
        <p>Here is the update on your claims processed today:</p>

        <h2>Processed Claims</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Provider Name</th>
                <th>Total Claim Value</th>
                <th>Time of Month Cost Value</th>
                <th>Specialty Type Cost Value</th>
                <th>Priority Level Cost Value</th>
                <th>Monetary Cost Value</th>
                <th>Total Processing Cost Value</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($processedClaims as $claim)
                <tr>
                    <td>{{ $claim->provider_name }}</td>
                    <td>{{ number_format($claim->total_claim_value,2) }}</td>
                    <td>{{ number_format($claim->time_of_month_cost_value, 2) }}</td>
                    <td>{{ number_format($claim->specialty_type_cost_value, 2) }}</td>
                    <td>{{number_format($claim->priority_level_cost_value, 2) }}</td>
                    <td>{{ number_format($claim->monetary_cost_value, 2) }}</td>
                    <td>{{number_format($claim->total_processing_cost_value, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <p><strong>Total Processed:</strong> {{ $totalProcessed }}</p>

        <h2>Claims Moved to Next Day</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Provider Name</th>
                <th>Total Claim Value</th>
                <th>Time of Month Cost Value</th>
                <th>Specialty Type Cost Value</th>
                <th>Priority Level Cost Value</th>
                <th>Monetary Cost Value</th>
                <th>Total Processing Cost Value</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($movedClaims as $claim)
                <tr>
                    <td>{{ $claim->provider_name }}</td>
                    <td>{{ number_format($claim->total_claim_value,2) }}</td>
                    <td>{{ number_format($claim->time_of_month_cost_value, 2) }}</td>
                    <td>{{ number_format($claim->specialty_type_cost_value, 2) }}</td>
                    <td>{{number_format($claim->priority_level_cost_value, 2) }}</td>
                    <td>{{ number_format($claim->monetary_cost_value, 2) }}</td>
                    <td>{{number_format($claim->total_processing_cost_value, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <p><strong>Total Moved:</strong> {{ $totalMoved }}</p>

        <p>If you have any questions, feel free to contact our support team.</p>
    </div>
    <div class="email-footer">
        <p>&copy; {{ date('Y') }} Claims Management System. All rights reserved.</p>
    </div>
</div>
</body>
</html>
