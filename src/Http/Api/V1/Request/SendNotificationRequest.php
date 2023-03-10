<?php

declare(strict_types=1);

namespace App\Http\Api\V1\Request;

use App\ValueObject\Channel;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

final class SendNotificationRequest
{
    #[NotBlank]
    public ?string $receiver = null;
    #[NotBlank]
    public ?string $content = null;
    #[When(
        expression: 'this.channel == "email"',
        constraints: [new NotBlank()]
    )]
    public ?string $subject = null;
    #[NotBlank]
    #[Choice(callback: [Channel::class, 'values'])]
    public ?string $channel = null;
}
