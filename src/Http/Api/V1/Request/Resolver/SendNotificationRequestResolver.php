<?php

declare(strict_types=1);

namespace App\Http\Api\V1\Request\Resolver;

use App\Http\Api\V1\Request\SendNotificationRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SendNotificationRequestResolver implements ValueResolverInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== SendNotificationRequest::class) {
            return [];
        }

        return [$this->serializer->deserialize($request->getContent(), SendNotificationRequest::class, 'json')];
    }
}
