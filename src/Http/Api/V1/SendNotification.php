<?php

declare(strict_types=1);

namespace App\Http\Api\V1;

use App\Http\Api\V1\Request\SendNotificationRequest;
use App\ValueObject\Channel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/v1/notifications', methods: ['POST'])]
final readonly class SendNotification
{
    public function __construct(
        private ValidatorInterface $validator,
        private NormalizerInterface $normalizer,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(SendNotificationRequest $request): JsonResponse
    {
        $errors = $this->validator->validate($request);
        if ($errors->count() > 0) {
            return new JsonResponse($this->normalizer->normalize($errors), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->messageBus->dispatch(
            new \App\Command\SendNotification($request->receiver, Channel::from($request->channel))
        );

        return new JsonResponse(null, Response::HTTP_ACCEPTED);
    }
}
