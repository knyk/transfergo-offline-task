<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Push\HttpClient;

final class RequestFailed extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Error occurred while trying to communicate with Pushy API.');
    }
}
