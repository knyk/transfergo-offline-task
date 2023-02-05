<?php

declare(strict_types=1);

namespace App\Command;

use App\ValueObject\Channel;

final readonly class SendNotification
{
    public function __construct(public string $receiver, public Channel $channel)
    {
    }
}
