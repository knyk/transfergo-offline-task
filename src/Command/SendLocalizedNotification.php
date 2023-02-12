<?php

declare(strict_types=1);

namespace App\Command;

use App\ValueObject\Channel;

final readonly class SendLocalizedNotification
{
    public function __construct(
        public string $receiver,
        public string $contentTranslationKey,
        public Channel $channel,
        public string $locale,
        public ?string $subjectTranslationKey = null
    ) {
    }
}
