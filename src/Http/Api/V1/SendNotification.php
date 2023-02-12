<?php

declare(strict_types=1);

namespace App\Http\Api\V1;

use App\Command\SendNotification as SendNotificationCommand;
use App\Http\Api\V1\Request\SendNotificationRequest;
use App\ValueObject\Channel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/notifications', methods: ['POST'])]
final readonly class SendNotification extends AbstractSendNotification
{
    public function __invoke(SendNotificationRequest $request): JsonResponse
    {
        return $this->send(
            $request,
            static fn(SendNotificationRequest $request) => new SendNotificationCommand(
                $request->receiver,
                $request->content,
                Channel::from($request->channel),
                $request->subject
            )
        );
    }
}
