<?php

declare(strict_types=1);

namespace App\Http\Api\V1\Request;

use App\ValueObject\Channel;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

final class SendLocalizedNotificationRequest
{
    #[NotBlank]
    public ?string $receiver = null;
    #[NotBlank]
    public ?string $contentTranslationKey = null;
    #[When(
        expression: 'this.channel == "email"',
        constraints: [new NotBlank()]
    )]
    public ?string $subjectTranslationKey = null;
    #[NotBlank]
    #[Locale]
    public ?string $locale = null;
    #[NotBlank]
    #[Choice(callback: [Channel::class, 'values'])]
    public ?string $channel = null;
}
