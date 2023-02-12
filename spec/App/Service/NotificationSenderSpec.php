<?php

namespace spec\App\Service;

use App\Event\NotificationSent;
use App\Service\EnabledChannels;
use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\ChannelDisabled;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;
use Psr\Clock\ClockInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NotificationSenderSpec extends ObjectBehavior
{
    private const DATETIME = '2023-01-01 00:00:00';

    public function let(
        EnabledChannels $enabledChannels,
        ClockInterface $clock,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $clock->now()->willReturn(new \DateTimeImmutable(self::DATETIME));

        $this->beConstructedWith($enabledChannels, $clock, $eventDispatcher);
    }

    public function it_throws_exception_if_adapters_array_is_empty(EnabledChannels $enabledChannels): void
    {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(true);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, $channel]);
    }

    public function it_throws_exception_if_adapter_fails(Adapter $adapter, EnabledChannels $enabledChannels): void
    {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(true);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($notification)->shouldBeCalledOnce()->willThrow(SendingFailed::withChannel($channel));
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, $channel]);
    }

    public function it_throws_exception_if_adapter_doesnt_support_channel(
        Adapter $adapter,
        EnabledChannels $enabledChannels
    ): void {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(true);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $adapter->supports($channel)->willReturn(false);
        $adapter->send($notification)->shouldNotBeCalled();
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, $channel]);
    }

    public function it_should_send_notification_using_supported_adapter(
        Adapter $adapter,
        EnabledChannels $enabledChannels,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(true);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($notification)->shouldBeCalledOnce();
        $this->addAdapter($adapter);

        $event = new NotificationSent($notification, new \DateTimeImmutable(self::DATETIME));
        $eventDispatcher->dispatch($event)->shouldBeCalledOnce()->willReturn($event);

        $this->send($notification, $channel);
    }


    public function it_should_send_notification_using_next_adapter_if_previous_fails(
        Adapter $adapterFailed,
        Adapter $adapterSucceed,
        EnabledChannels $enabledChannels,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(true);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $adapterFailed->supports($channel)->willReturn(true);
        $adapterFailed->send($notification)->shouldBeCalledOnce()->willThrow(
            SendingFailed::withAdapter($adapterFailed->getWrappedObject())
        );
        $this->addAdapter($adapterFailed);

        $adapterSucceed->supports($channel)->willReturn(true);
        $adapterSucceed->send($notification)->shouldBeCalledOnce();
        $this->addAdapter($adapterSucceed);

        $event = new NotificationSent($notification, new \DateTimeImmutable(self::DATETIME));
        $eventDispatcher->dispatch($event)->shouldBeCalledOnce()->willReturn($event);

        $this->send($notification, $channel);
    }

    public function it_throws_exception_if_channel_is_disabled(EnabledChannels $enabledChannels): void
    {
        $channel = Channel::Email;

        $enabledChannels->isChannelEnabled($channel)->willReturn(false);

        $notification = new Notification(Channel::Email, 'example@example.com', 'content', 'subject');

        $this->shouldThrow(ChannelDisabled::withChannel($channel))->during('send', [$notification, $channel]);
    }
}
