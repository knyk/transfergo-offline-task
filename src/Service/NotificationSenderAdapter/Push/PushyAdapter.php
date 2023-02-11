<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Push;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\Push\HttpClient\Client;
use App\Service\NotificationSenderAdapter\Push\HttpClient\RequestFailed;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;

final class PushyAdapter implements Adapter
{
    public function __construct(private readonly Client $client)
    {
    }

    public function send(Notification $notification): void
    {
        try {
            $this->client->sendNotification($notification->receiver, ['message' => $notification->content]);
        } catch (RequestFailed) {
            throw SendingFailed::withAdapter($this);
        }
    }

    public function supports(Channel $channel): bool
    {
        return Channel::Push === $channel;
    }
}
