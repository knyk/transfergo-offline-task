<?php

namespace spec\App\Service\NotificationSenderAdapter\Push\HttpClient;

use App\Service\NotificationSenderAdapter\Push\HttpClient\RequestFailed;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyHttpClientSpec extends ObjectBehavior
{
    private const TOKEN = 'token';

    public function let(HttpClientInterface $client, LoggerInterface $logger): void
    {
        $this->beConstructedWith($client, self::TOKEN, $logger);
    }

    public function it_throws_exception_on_transport_exception(
        HttpClientInterface $client,
        ResponseInterface $response,
        ClientExceptionInterface $exception
    ): void {
        $to = 'device';
        $data = ['message' => 'dummy message content'];
        $response->getContent()->willThrow($exception->getWrappedObject());
        $response->getContent(false)->willReturn('content');
        $exception->getResponse()->willReturn($response);

        $client->request(
            'POST',
            '/push',
            [
                'query' => ['api_key' => self::TOKEN],
                'json' => ['to' => $to, 'data' => $data]
            ]
        )->shouldBeCalledOnce()->willReturn($response);

        $this->shouldThrow(RequestFailed::create())->during('sendNotification', [$to, $data]);
    }


    public function it_sends_notification_using_http_client(
        HttpClientInterface $client,
        ResponseInterface $response,
    ): void {
        $to = 'device';
        $data = ['message' => 'dummy message content'];

        $response->getContent()->willReturn('content');

        $client->request(
            'POST',
            '/push',
            [
                'query' => ['api_key' => self::TOKEN],
                'json' => ['to' => $to, 'data' => $data]
            ]
        )->shouldBeCalledOnce()->willReturn($response);

        $this->sendNotification($to, $data);
    }
}
