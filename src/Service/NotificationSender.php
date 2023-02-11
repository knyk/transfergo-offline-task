<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\ChannelDisabled;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;

class NotificationSender
{
    public function __construct(private readonly EnabledChannels $enabledChannels)
    {
    }

    /**
     * @var Adapter[]
     */
    private array $adapters = [];

    public function send(Notification $notification, Channel $channel): void
    {
        if (!$this->enabledChannels->isChannelEnabled($channel)) {
            throw ChannelDisabled::withChannel($channel);
        }

        foreach ($this->adapters as $adapter) {
            if (!$adapter->supports($channel)) {
                continue;
            }

            try {
                $adapter->send($notification);

                return;
            } catch (SendingFailed) {
                continue;
            }
        }

        throw SendingFailed::withChannel($channel);
    }

    public function addAdapter(Adapter $adapter): void
    {
        $this->adapters[] = $adapter;
    }
}
