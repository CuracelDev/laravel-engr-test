<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Submitted</title>
</head>
<body>
    <h1>New Claim Submitted</h1>
    <p><strong>Provider Name:</strong> {{ $claim->provider_name }}</p>
    <p><strong>Encounter Date:</strong> {{ $claim->encounter_date }}</p>
    <p><strong>Total Amount:</strong> ${{ $claim->total_amount }}</p>
    <p><strong>Specialty:</strong> {{ $claim->specialty }}</p>
    <p><strong>Priority Level:</strong> {{ $claim->priority_level }}</p>
</body>
</html>
