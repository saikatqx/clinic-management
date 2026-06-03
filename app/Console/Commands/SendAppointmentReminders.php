<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentReminder;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send email reminders for upcoming appointments within 24 hours.';

    public function handle(): int
    {
        $now = now();
        $nextDay = now()->addDay();

        $appointments = Appointment::whereBetween('appointment_date', [$now, $nextDay])
            ->where('status', '!=', 'Cancelled')
            ->get();

        foreach ($appointments as $appt) {
            if ($appt->patient_email) {
                Notification::route('mail', $appt->patient_email)
                    ->notify(new AppointmentReminder($appt));
                $this->info('Reminder sent to ' . $appt->patient_email . ' for appointment ' . $appt->id);
            } else {
                $this->line('No email for appointment ' . $appt->id . ', skipping.');
            }
        }

        return 0;
    }
}
