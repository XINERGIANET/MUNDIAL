<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingMatchResultsNotification extends Notification
{
    use Queueable;

    public function __construct(public int $count)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Partidos pendientes de resultado',
            'body' => "Hay {$this->count} partidos pendientes de registrar resultado.",
            'url' => url('/admin'),
        ];
    }
}
