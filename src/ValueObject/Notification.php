<?php

declare(strict_types=1);

namespace App\ValueObject;

readonly class Notification
{
    public function __construct(public string $receiver, public string $content, public ?string $subject = null)
    {
    }
}
