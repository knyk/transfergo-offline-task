<?php

declare(strict_types=1);

namespace App\Http\Api\V1;

use App\Command\SendLocalizedNotification as SendLocalizedNotificationCommand;
use App\Http\Api\V1\Request\SendLocalizedNotificationRequest;
use App\ValueObject\Channel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/localizedNotifications', methods: ['POST'])]
final readonly class SendLocalizedNotification extends AbstractSendNotification
{
    public function __invoke(SendLocalizedNotificationRequest $request): JsonResponse
    {
        return $this->send(
            $request,
            static fn(SendLocalizedNotificationRequest $request) => new SendLocalizedNotificationCommand(
                $request->receiver,
                $request->contentTranslationKey,
                Channel::from($request->channel),
                $request->locale,
                $request->subjectTranslationKey
            )
        );
    }
}
