<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter;

use App\ValueObject\Channel;

interface Adapter
{
    /**
     * @throws SendingFailed
     */
    public function send(string $receiver, Channel $channel): void;
}
