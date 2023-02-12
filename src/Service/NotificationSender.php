<?php

declare(strict_types=1);

namespace App\Service;

use App\Event\NotificationSent;
use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\ChannelDisabled;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Notification;
use Psr\Clock\ClockInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NotificationSender
{
    public function __construct(
        private readonly EnabledChannels $enabledChannels,
        private readonly ClockInterface $clock,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @var Adapter[]
     */
    private array $adapters = [];

    public function send(Notification $notification): void
    {
        $channel = $notification->channel;

        if (!$this->enabledChannels->isChannelEnabled($channel)) {
            throw ChannelDisabled::withChannel($channel);
        }

        foreach ($this->adapters as $adapter) {
            if (!$adapter->supports($channel)) {
                continue;
            }

            try {
                $adapter->send($notification);

                $this->dispatchSentEvent($notification);

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

    private function dispatchSentEvent(Notification $notification): void
    {
        $event = new NotificationSent($notification, $this->clock->now());

        $this->eventDispatcher->dispatch($event);
    }
}
