<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Otp\OtpSenderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOtpCodeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $plainCode,
        public string $channel,
    )
    {
    }

    public function handle(OtpSenderInterface $sender): void
    {
        $sender->send($this->user, $this->plainCode, $this->channel);
    }
}
