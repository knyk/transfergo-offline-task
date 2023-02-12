<?php

declare(strict_types=1);

namespace App\Http\Api\V1;

use App\Command\SendLocalizedNotification as SendLocalizedNotificationCommand;
use App\Command\SendNotification as SendNotificationCommand;
use App\Http\Api\V1\Request\SendLocalizedNotificationRequest;
use App\Http\Api\V1\Request\SendNotificationRequest;
use App\Service\NotificationSenderAdapter\SendingFailed;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract readonly class AbstractSendNotification
{
    public function __construct(
        private ValidatorInterface $validator,
        private NormalizerInterface $normalizer,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function send(
        SendNotificationRequest|SendLocalizedNotificationRequest $request,
        callable $createCommandFallback
    ): JsonResponse {
        $errors = $this->validator->validate($request);
        if ($errors->count() > 0) {
            return new JsonResponse($this->normalizer->normalize($errors), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->messageBus->dispatch($createCommandFallback($request));
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() instanceof SendingFailed) {
                return new JsonResponse(
                    ['error' => $exception->getPrevious()->getMessage()],
                    Response::HTTP_FAILED_DEPENDENCY
                );
            }

            return new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
