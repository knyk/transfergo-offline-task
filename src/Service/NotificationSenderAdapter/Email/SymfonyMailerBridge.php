<?php

declare(strict_types=1);

namespace App\Service\NotificationSenderAdapter\Email;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class SymfonyMailerBridge implements Adapter
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $transport,
        private string $sender
    ) {
    }

    public function send(Notification $notification): void
    {
        if (!$notification->subject) {
            throw MissingSubject::create();
        }

        $email = new Email();
        $email->getHeaders()->addTextHeader('X-Transport', $this->transport);
        $email->from($this->sender)
            ->to($notification->receiver)
            ->subject($notification->subject)
            ->text($notification->content);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception->getMessage());

            throw SendingFailed::withAdapter($this);
        }
    }

    public function supports(Channel $channel): bool
    {
        return Channel::Email === $channel;
    }
}
