<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Laundry Has Been Delivered</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .header {
            background-color: #198754;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
            line-height: 1.6;
        }
        .footer {
            background-color: #f1f3f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Iimaan Dry Cleaner</h1>
        </div>
        <div class="content">
            <p>Dear {{ $order->customer->name }},</p>
            <p>Your laundry order <strong>#{{ $order->id }}</strong> has been successfully delivered. We hope everything is fresh and perfect!</p>
            <p>We'd love to hear your feedback. Please log in to your account to leave a review: <a href="{{ url('/reviews') }}">{{ url('/reviews') }}</a></p>
            <p>Thank you for choosing Iimaan Dry Cleaner!</p>
            <p>Best regards,<br><strong>The Iimaan Dry Cleaner Team</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Iimaan Dry Cleaner. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
