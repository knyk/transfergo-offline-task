<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\NotificationRecordsRepository;
use App\ValueObject\Notification;
use App\ValueObject\NotificationRecord;

readonly class RecordNotificationLog
{
    public function __construct(
        private NotificationRecordsRepository $notificationRecordsRepository,
    ) {
    }

    public function record(Notification $notification, \DateTimeImmutable $sentAt): void
    {
        $this->notificationRecordsRepository->save(
            new NotificationRecord(
                $notification->channel,
                $notification->receiver,
                $sentAt,
                $notification->content,
                $notification->subject
            )
        );
    }
}
