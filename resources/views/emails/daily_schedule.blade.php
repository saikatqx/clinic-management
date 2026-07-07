<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Schedule Backup</title>
    <style>
        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            background-color: #f8f9fb;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            border: 1px solid #eaeaea;
        }
        .header {
            background-color: #343a40;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 16px;
            color: #555555;
        }
        .details-box {
            background-color: #f8f9fa;
            border-left: 4px solid #343a40;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        .footer {
            background-color: #f8f9fb;
            padding: 20px 30px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #eaeaea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Offline Schedule Backup</h2>
        </div>
        <div class="content">
            <p>Hello Admin,</p>
            <p>Please find attached the automated daily schedule backup PDF for tomorrow, <strong>{{ $dateString }}</strong>.</p>
            
            <div class="details-box">
                <p style="margin:0; font-size:15px;"><strong>Backup Overview:</strong></p>
                <p style="margin:5px 0 0 0; font-size:14px; color:#666;">
                    Total appointments scheduled: <strong>{{ $count }}</strong>
                </p>
            </div>

            <p>This backup serves as an offline checklist for clinic staff in the event of an internet outage or server downtime. Please keep a copy accessible at the front reception desk.</p>
            <p>Best regards,<br><strong>Clinic Management System</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Clinic Management. All rights reserved.
        </div>
    </div>
</body>
</html>
