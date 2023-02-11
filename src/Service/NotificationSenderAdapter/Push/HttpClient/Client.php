<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Push\HttpClient;

interface Client
{
    /**
     * @throws RequestFailed
     */
    public function sendNotification(string $to, array $data): void;
}
