<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\SMS;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

readonly class SymfonyNotifierBridge implements Adapter
{
    public function __construct(
        private TexterInterface $texter,
        private LoggerInterface $logger,
        private string $transport,
    ) {
    }

    public function send(Notification $notification): void
    {
        $sms = new SmsMessage($notification->receiver, $notification->content);
        $sms->transport($this->transport);

        try {
            $this->texter->send($sms);
        } catch (HandlerFailedException $exception) {
            $this->logger->error($exception->getMessage());

            throw SendingFailed::withAdapter($this);
        }
    }

    public function supports(Channel $channel): bool
    {
        return Channel::SMS === $channel;
    }
}
