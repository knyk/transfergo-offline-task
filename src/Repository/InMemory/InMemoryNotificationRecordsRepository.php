<?php

declare(strict_types=1);

namespace App\Repository\InMemory;

use App\Repository\NotificationRecordsRepository;
use App\ValueObject\NotificationRecord;

class InMemoryNotificationRecordsRepository implements NotificationRecordsRepository
{
    public array $records = [];

    public function save(NotificationRecord $notificationRecord): void
    {
        $this->records[] = $notificationRecord;
    }
}
