<?php

declare(strict_types=1);

namespace App\Handler;

use App\Command\SendNotification;
use App\Service\NotificationSender;

final readonly class SendNotificationHandler
{
    public function __construct(private NotificationSender $notificationSender)
    {
    }

    public function __invoke(SendNotification $command): void
    {
        $this->notificationSender->send($command->receiver, $command->channel);
    }
}
