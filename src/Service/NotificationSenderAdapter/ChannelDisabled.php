<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter;

use App\ValueObject\Channel;

final class ChannelDisabled extends \RuntimeException
{
    public static function withChannel(Channel $channel): self
    {
        return new self(sprintf('Channel "%s" is disabled.', $channel->value));
    }
}
