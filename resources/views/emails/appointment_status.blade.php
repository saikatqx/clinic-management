<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Update</title>
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
            background-color: #007bff;
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
        .appointment-info {
            background-color: #f0f4ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }
        .appointment-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .appointment-info td {
            padding: 6px 0;
            font-size: 15px;
        }
        .appointment-info td.label {
            font-weight: 600;
            color: #444444;
            width: 120px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
        }
        .status-Confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-Cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-Pending {
            background-color: #fff3cd;
            color: #856404;
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
            <h2>Appointment Status Update</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->patient_name }},</p>
            <p>There has been an update regarding your medical appointment. Please find the current details of your booking below:</p>
            
            <div class="appointment-info">
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
                        <td class="label">Date & Time</td>
                        <td>{{ date('d M Y, h:i A', strtotime($appointment->appointment_date)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>
                            <span class="status-badge status-{{ $appointment->status }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            @if($appointment->status === 'Confirmed')
                <p>We look forward to seeing you at your scheduled time. If you need to make any changes, please contact the clinic directly.</p>
            @elseif($appointment->status === 'Cancelled')
                <p>We regret to inform you that your appointment has been cancelled. Please visit our website or contact our office to reschedule.</p>
            @endif

            <p>Best regards,<br><strong>Clinic Management Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Clinic Management. All rights reserved.
        </div>
    </div>
</body>
</html>
