<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Prescription</title>
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
            background-color: #28a745;
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
        .prescription-box {
            background-color: #f4faf6;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        .prescription-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .prescription-box td {
            padding: 6px 0;
            font-size: 15px;
        }
        .prescription-box td.label {
            font-weight: 600;
            color: #444444;
            width: 150px;
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
            <h2>Your Prescription is Ready</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->patient_name }},</p>
            <p>Dr. {{ $appointment->doctor->name ?? 'N/A' }} has generated a prescription for your appointment. We have attached the official PDF copy of the prescription to this email.</p>
            
            <div class="prescription-box">
                <p style="margin-bottom:10px; font-weight:700; color:#28a745;">Prescription Summary:</p>
                <table>
                    <tr>
                        <td class="label">Appointment ID</td>
                        <td>#{{ $appointment->id }}</td>
                    </tr>
                    <tr>
                        <td class="label">Doctor</td>
                        <td>Dr. {{ $appointment->doctor->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Consultation Date</td>
                        <td>{{ date('d M Y, h:i A', strtotime($appointment->appointment_date)) }}</td>
                    </tr>
                </table>
            </div>

            <p>Please refer to the attached PDF file for full dosage instructions, checkups recommended, and doctor notes.</p>
            <p>Best regards,<br><strong>Clinic Management Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Clinic Management. All rights reserved.
        </div>
    </div>
</body>
</html>
