<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Batch of Claims</title>
</head>
<body>
    <h1>Claims Ready for Processing</h1>
    <p>Hello, batch of claims has been created for processing. Below are the details:</p>

    <h2>Batch Details</h2>
    <ul>
        <li><strong>Batch ID:</strong> {{ $batch->id }}</li>
        <li><strong>Provider Name:</strong> {{ $batch->provider_name }}</li>
        <li><strong>Batch Date:</strong> {{ $batch->batch_date }}</li>
        <li><strong>Total Processing Cost:</strong> ${{ number_format($batch->total_processing_cost, 2) }}</li>
    </ul>

    <h2>Claims in This Batch</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Specialty</th>
                <th>Priority Level</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($batch->claims as $claim)
                <tr>
                    <td>{{ $claim->id }}</td>
                    <td>{{ $claim->specialty }}</td>
                    <td>{{ $claim->priority_level }}</td>
                    <td>${{ number_format($claim->total_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Please process this batch at your earliest convenience.</p>

    <footer>
        <p>Best regards,</p>
        <p>Your Curacel Claims Team</p>
    </footer>
</body>
</html>