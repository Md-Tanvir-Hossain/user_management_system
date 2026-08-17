<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class SendVerificationEmail
{
    public function __construct(
        public readonly int $userId,
    ) {
    }
}