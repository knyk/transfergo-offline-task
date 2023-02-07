<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;

class NotificationSender
{
    /**
     * @var Adapter[]
     */
    private array $adapters = [];

    public function send(string $receiver, Channel $channel): void
    {
        foreach ($this->adapters as $adapter) {
            if (!$adapter->supports($channel)) {
                continue;
            }

            try {
                $adapter->send($receiver);

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
