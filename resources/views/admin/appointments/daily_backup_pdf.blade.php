<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Daily Appointments Backup Schedule</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      color: #2e2e2e;
      margin: 0;
      padding: 0;
      background: #f8f9fa;
    }

    .container {
      width: 95%;
      margin: 20px auto;
      background: #fff;
      padding: 25px 35px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .header {
      text-align: center;
      border-bottom: 3px solid #007bff;
      padding-bottom: 10px;
      margin-bottom: 20px;
      position: relative;
    }

    .header h1 {
      color: #007bff;
      margin: 0;
      font-size: 24px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .header p {
      margin: 3px 0;
      font-size: 13px;
      color: #444;
    }

    .section-title {
      font-size: 16px;
      color: #007bff;
      margin-top: 15px;
      margin-bottom: 10px;
      border-left: 4px solid #007bff;
      padding-left: 8px;
      text-transform: uppercase;
      font-weight: bold;
    }

    .schedule-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .schedule-table th,
    .schedule-table td {
      border: 1px solid #ddd;
      padding: 8px 10px;
      text-align: left;
      font-size: 12px;
    }

    .schedule-table th {
      background: #f2f2f2;
      color: #333;
      font-weight: bold;
    }

    .footer {
      margin-top: 30px;
      font-size: 11px;
      color: #666;
      border-top: 1px solid #ddd;
      padding-top: 10px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      <h1>{{ $settings->clinic_name ?? 'Doctor Clinic' }}</h1>
      <p><strong>Daily Appointments Backup Schedule</strong></p>
      <p>Schedule Date: {{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}</p>
      <small>Generated on: {{ $generated_at }}</small>
    </div>

    <!-- Appointments Table -->
    <div class="appointments-section">
      <h3 class="section-title">Schedule List</h3>
      
      @if($appointments->count() > 0)
        <table class="schedule-table">
          <thead>
            <tr>
              <th style="width: 8%;">ID</th>
              <th style="width: 25%;">Patient Name</th>
              <th style="width: 20%;">Patient Phone</th>
              <th style="width: 22%;">Doctor</th>
              <th style="width: 15%;">Time</th>
              <th style="width: 10%;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($appointments as $appt)
              <tr>
                <td>#{{ $appt->id }}</td>
                <td><strong>{{ $appt->patient_name }}</strong></td>
                <td>{{ $appt->patient_phone ?? '-' }}</td>
                <td>Dr. {{ $appt->doctor->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($appt->appointment_date)->format('h:i A') }}</td>
                <td>{{ $appt->status }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="text-align: center; color: #777; font-size: 14px; margin-top: 20px;">No appointments scheduled for this date.</p>
      @endif
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>© {{ date('Y') }} {{ $settings->clinic_name ?? 'Doctor Clinic' }} — Offline Backup Document — Confidential</p>
    </div>
  </div>
</body>
</html>
