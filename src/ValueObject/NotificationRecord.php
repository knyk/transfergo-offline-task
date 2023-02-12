<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class NotificationRecord
{
    public function __construct(
        public Channel $channel,
        public string $receiver,
        public \DateTimeImmutable $sentAt,
        public string $content,
        public ?string $subject = null
    ) {
    }
}
