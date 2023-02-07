<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter;

use App\ValueObject\Channel;

final class SendingFailed extends \RuntimeException
{
    public static function withAdapter(Adapter $adapter): self
    {
        return new self(sprintf('Sending notification failed using "%s" adapter.', get_class($adapter)));
    }

    public static function withChannel(Channel $channel): self
    {
        return new self(
            sprintf(
                'Sending notification failed. Unable to find adapter supporting given channel "%s"',
                $channel->value
            )
        );
    }
}
