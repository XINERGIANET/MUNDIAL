<?php

namespace App\Services\Otp;

use App\Models\User;

interface OtpSenderInterface
{
    public function send(User $user, string $plainCode, string $channel): void;
}
