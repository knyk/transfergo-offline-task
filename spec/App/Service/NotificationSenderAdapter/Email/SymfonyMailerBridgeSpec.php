<?php

namespace spec\App\Service\NotificationSenderAdapter\Email;

use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SymfonyMailerBridgeSpec extends ObjectBehavior
{
    private const SENDER = 'example@example.com';
    private const TRANSPORT = 'fakeTransport';

    public function let(MailerInterface $mailer, LoggerInterface $logger): void
    {
        $this->beConstructedWith($mailer, $logger, self::TRANSPORT, self::SENDER);
    }

    public function it_should_support_email_channel(): void
    {
        $this->supports(Channel::Email)->shouldBe(true);
    }

    public function it_shouldnt_support_other_channels_than_email(): void
    {
        $channels = array_filter(Channel::values(), static fn(string $value) => $value !== Channel::Email->value);

        foreach ($channels as $channel) {
            $this->supports(Channel::from($channel))->shouldBe(false);
        }
    }

    public function it_throws_exception_on_transport_exception(MailerInterface $mailer): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');

        $mailer->send(Argument::any())->shouldBeCalledOnce()->willThrow(TransportException::class);

        $this->shouldThrow(SendingFailed::withAdapter($this->getWrappedObject()))->during('send', [$notification]);
    }

    public function it_sends_email_using_mailer(MailerInterface $mailer): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');

        $email = new Email();
        $email->getHeaders()->addTextHeader('X-Transport', self::TRANSPORT);
        $email->from(self::SENDER)
            ->to($notification->receiver)
            ->subject($notification->subject)
            ->text($notification->content);

        $mailer->send($email)->shouldBeCalledOnce();

        $this->send($notification);
    }
}
