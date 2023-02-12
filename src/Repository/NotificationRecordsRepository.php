<?php

declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\NotificationRecord;

interface NotificationRecordsRepository
{
    public function save(NotificationRecord $notificationRecord): void;
}
