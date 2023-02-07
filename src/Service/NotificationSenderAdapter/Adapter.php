<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter;

use App\ValueObject\Channel;
use App\ValueObject\Notification;

interface Adapter
{
    /**
     * @throws SendingFailed
     */
    public function send(Notification $notification): void;

    public function supports(Channel $channel): bool;
}
