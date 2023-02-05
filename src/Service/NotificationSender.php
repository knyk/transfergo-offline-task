<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Channel;

class NotificationSender
{
    public function send(string $receiver, Channel $channel): void
    {
    }
}
