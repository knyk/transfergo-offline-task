<?php

declare(strict_types=1);

namespace App\Event;

use App\ValueObject\Notification;

final readonly class NotificationSent
{
    public function __construct(public Notification $notification, public \DateTimeImmutable $sentAt)
    {
    }
}
