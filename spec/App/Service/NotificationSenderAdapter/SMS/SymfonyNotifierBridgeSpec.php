<?php

namespace spec\App\Service\NotificationSenderAdapter\SMS;

use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class SymfonyNotifierBridgeSpec extends ObjectBehavior
{
    private const TRANSPORT = 'fakeTransport';

    public function let(TexterInterface $texter, LoggerInterface $logger): void
    {
        $this->beConstructedWith($texter, $logger, self::TRANSPORT);
    }

    public function it_should_support_sms_channel(): void
    {
        $this->supports(Channel::SMS)->shouldBe(true);
    }

    public function it_shouldnt_support_other_channels_than_sms(): void
    {
        $channels = array_filter(Channel::values(), static fn(string $value) => $value !== Channel::SMS->value);

        foreach ($channels as $channel) {
            $this->supports(Channel::from($channel))->shouldBe(false);
        }
    }

    public function it_throws_exception_on_handler_exception(TexterInterface $texter): void
    {
        $notification = new Notification('+48666666666', 'content');

        $texter->send(Argument::any())->shouldBeCalledOnce()->willThrow(HandlerFailedException::class);

        $this->shouldThrow(SendingFailed::withAdapter($this->getWrappedObject()))->during('send', [$notification]);
    }

    public function it_sends_sms_using_notifiier(TexterInterface $texter): void
    {
        $notification = new Notification('+48666666666', 'content');

        $sms = new SmsMessage('+48666666666', 'content');
        $sms->transport(self::TRANSPORT);

        $texter->send($sms)->shouldBeCalledOnce();

        $this->send($notification);
    }
}
