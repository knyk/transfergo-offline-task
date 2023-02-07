<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Email;

final class MissingSubject extends \InvalidArgumentException
{
    public static function create(): self
    {
        return new self('Subject is required to send email notification.');
    }
}
