<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Appointment;

class AppointmentReminder extends Notification
{
    use Queueable;

    protected Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $appt = $this->appointment;
        $date = $appt->appointment_date ? date('d M Y, h:i A', strtotime($appt->appointment_date)) : 'N/A';

        return (new MailMessage)
            ->subject('Appointment Reminder')
            ->greeting('Hello ' . $appt->patient_name)
            ->line("This is a reminder for your appointment on {$date} with " . ($appt->doctor->name ?? 'our clinic') . ".")
            ->action('Check Appointment', url(route('appointments.status')))
            ->line('If you need to reschedule or cancel, please contact us.');
    }
}
