<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\Channel;

class EnabledChannels
{
    private array $disabledChannels;

    public function __construct(string $disabledChannels)
    {
        $this->disabledChannels = explode(',', $disabledChannels);
    }

    public function isChannelEnabled(Channel $channel): bool
    {
        return !in_array($channel->value, $this->disabledChannels, true);
    }
}
