<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Setting;
use App\Mail\DailyScheduleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SendDailyScheduleBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clinic:send-schedule-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and email the appointments schedule backup for tomorrow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting schedule backup generation...');

        $tomorrow = Carbon::tomorrow()->toDateString();
        $tomorrowFormatted = Carbon::tomorrow()->format('d M Y, l');

        // Fetch tomorrow's appointments
        $appointments = Appointment::with('doctor')
            ->whereDate('appointment_date', $tomorrow)
            ->orderBy('appointment_date', 'asc')
            ->get();

        $settings = Setting::first();
        $adminEmail = $settings->email ?? config('mail.from.address') ?? 'admin@example.com';

        // Prepare PDF data
        $pdfData = [
            'appointments' => $appointments,
            'date' => $tomorrow,
            'settings' => $settings,
            'generated_at' => now()->format('d M Y, h:i A'),
        ];

        try {
            $pdf = Pdf::loadView('admin.appointments.daily_backup_pdf', $pdfData);
            
            // Temporary storage path inside the workspace
            $fileName = 'schedule_backup_' . $tomorrow . '_' . time() . '.pdf';
            
            // Ensure temporary directory exists
            $storageDir = storage_path('app/backups');
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            $filePath = $storageDir . '/' . $fileName;
            $pdf->save($filePath);

            // Send Email
            Mail::to($adminEmail)->send(new DailyScheduleMail($tomorrowFormatted, $appointments->count(), $filePath));

            // Clean up file after mailing
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->info("Backup sent successfully to {$adminEmail}!");
            Log::info("Daily schedule backup sent successfully to {$adminEmail} for date {$tomorrow}");
        } catch (\Exception $e) {
            $this->error('Failed to generate or send backup: ' . $e->getMessage());
            Log::error('Daily schedule backup command error: ' . $e->getMessage());
        }
    }
}
