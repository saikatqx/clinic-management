<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Doctor Prescription</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      color: #2e2e2e;
      margin: 0;
      padding: 0;
      background: #f8f9fa;
    }

    .container {
      width: 90%;
      margin: 30px auto;
      background: #fff;
      padding: 40px 50px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .header {
      text-align: center;
      border-bottom: 3px solid #c50000;
      padding-bottom: 10px;
      margin-bottom: 25px;
      position: relative;
    }

    .header img.logo {
      width: 80px;
      height: auto;
      position: absolute;
      top: 10px;
      left: 50px;
    }

    .header h1 {
      color: #c50000;
      margin: 0;
      font-size: 28px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .header p {
      margin: 2px 0;
      font-size: 13px;
      color: #444;
    }

    .header small {
      color: #777;
      font-size: 12px;
    }

    .section-title {
      font-size: 18px;
      color: #c50000;
      margin-top: 25px;
      margin-bottom: 10px;
      border-left: 4px solid #c50000;
      padding-left: 8px;
      text-transform: uppercase;
    }

    .details p, .prescription p {
      font-size: 14px;
      margin: 4px 0;
    }

    .label {
      font-weight: bold;
      width: 160px;
      display: inline-block;
    }

    .divider {
      margin: 20px 0;
      border-bottom: 1px dashed #ccc;
    }

    .rx-symbol {
      font-size: 34px;
      color: #c50000;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .prescription-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .prescription-table th,
    .prescription-table td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
      font-size: 14px;
    }

    .prescription-table th {
      background: #f2f2f2;
      color: #222;
    }

    .footer {
      margin-top: 40px;
      font-size: 13px;
      color: #555;
      border-top: 1px solid #ddd;
      padding-top: 10px;
    }

    .footer p {
      margin: 3px 0;
    }

    .footer .right {
      text-align: right;
    }

    .clinic-info {
      text-align: center;
      font-size: 12px;
      color: #777;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header">
      {{-- Clinic Logo --}}
      @if(!empty($settings->clinic_logo))
        <img src="{{ public_path('images/settings/'.$settings->clinic_logo) }}" alt="Clinic Logo" class="logo">
      @endif

      <h1>{{ $settings->clinic_name ?? 'Doctor Clinic' }}</h1>

      @if(!empty($settings->tagline))
        <p><em>{{ $settings->tagline }}</em></p>
      @endif

      @if(!empty($settings->address))
        <p>{{ $settings->address }}</p>
      @endif

      @if(!empty($settings->mobile) || !empty($settings->email))
        <p>
          @if(!empty($settings->mobile)) 📞 {{ $settings->mobile }} @endif
          @if(!empty($settings->email)) | ✉️ {{ $settings->email }} @endif
        </p>
      @endif

      <small>Generated on {{ $generated_at }}</small>
    </div>

    <!-- Patient & Doctor Info -->
    <div class="details">
      <h3 class="section-title">Patient Details</h3>
      <p><span class="label">Patient Name:</span> {{ $appointment->patient_name }}</p>
      <p><span class="label">Doctor:</span> {{ $appointment->doctor->name ?? 'N/A' }}</p>
      <p><span class="label">Appointment Date:</span> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y, h:i A') }}</p>
    </div>

    <div class="divider"></div>

    <!-- Prescription Info -->
    <div class="prescription">
      <div class="rx-symbol">℞</div>
      <h3 class="section-title">Prescription Details</h3>

      <table class="prescription-table">
        <tr>
          <th>Medicine Name</th>
          <th>Checkup</th>
          <th>Eating Time</th>
          <th>Notes</th>
        </tr>
        <tr>
          <td>{{ $medicine_name }}</td>
          <td>{{ $checkup_name ?: 'N/A' }}</td>
          <td>{{ $eating_time }}</td>
          <td>{{ $short_note ?: 'N/A' }}</td>
        </tr>
      </table>
    </div>

    <div class="divider"></div>

    <!-- Footer -->
    <div class="footer">
      <p><strong>Doctor Signature:</strong> ___________________________</p>
      <p class="right"><em>Prescription generated at {{ $generated_at }}</em></p>
    </div>

    <div class="clinic-info">
      <p>© {{ date('Y') }} {{ $settings->clinic_name ?? 'Doctor Clinic' }} — All Rights Reserved</p>
    </div>
  </div>
</body>
</html>
