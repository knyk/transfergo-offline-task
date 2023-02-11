<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Push\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SymfonyHttpClient implements Client
{
    public function __construct(
        private HttpClientInterface $pushyClient,
        private string $token,
        private LoggerInterface $logger
    ) {
    }

    public function sendNotification(string $to, array $data): void
    {
        try {
            $this->pushyClient->request(
                'POST',
                '/push',
                (new HttpOptions())
                    ->setQuery(['api_key' => $this->token])
                    ->setJson(['to' => $to, 'data' => $data])
                    ->toArray()
            )->getContent();
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw RequestFailed::create();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $exception) {
            $this->logger->error(
                $exception->getMessage(),
                ['response' => $exception->getResponse()->getContent(false)]
            );

            throw RequestFailed::create();
        }
    }
}
