<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\NotificationSent;
use App\Service\RecordNotificationLog;

readonly class NotificationSentEventListener
{
    public function __construct(private RecordNotificationLog $recordNotificationLog)
    {
    }

    public function __invoke(NotificationSent $event): void
    {
        $this->recordNotificationLog->record($event->notification, $event->sentAt);
    }
}
