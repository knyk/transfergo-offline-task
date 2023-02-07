<?php

declare(strict_types=1);

namespace App\Http\Api\V1\Request;

use App\ValueObject\Channel;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SendNotificationRequest
{
    #[NotBlank]
    public ?string $receiver = null;
    #[NotBlank]
    public ?string $content = null;
    public ?string $subject = null;
    #[NotBlank]
    #[Choice(callback: [Channel::class, 'values'])]
    public ?string $channel = null;
}
