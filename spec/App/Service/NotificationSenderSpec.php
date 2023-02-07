<?php

namespace spec\App\Service;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;

class NotificationSenderSpec extends ObjectBehavior
{
    public function it_throws_exception_if_adapters_array_is_empty(): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, Channel::Email]);
    }

    public function it_throws_exception_if_adapter_fails(Adapter $adapter): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($notification)->shouldBeCalledOnce()->willThrow(SendingFailed::withChannel($channel));
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, $channel]);
    }

    public function it_throws_exception_if_adapter_doesnt_support_channel(Adapter $adapter): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(false);
        $adapter->send($notification)->shouldNotBeCalled();
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$notification, $channel]);
    }

    public function it_should_send_notification_using_supported_adapter(Adapter $adapter): void
    {
        $notification = new Notification('example@example.com', 'content', 'subject');
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($notification)->shouldBeCalledOnce();
        $this->addAdapter($adapter);

        $this->send($notification, $channel);
    }


    public function it_should_send_notification_using_next_adapter_if_previous_fails(
        Adapter $adapterFailed,
        Adapter $adapterSucceed
    ): void {
        $notification = new Notification('example@example.com', 'content', 'subject');
        $channel = Channel::Email;

        $adapterFailed->supports($channel)->willReturn(true);
        $adapterFailed->send($notification)->shouldBeCalledOnce()->willThrow(
            SendingFailed::withAdapter($adapterFailed->getWrappedObject())
        );
        $this->addAdapter($adapterFailed);

        $adapterSucceed->supports($channel)->willReturn(true);
        $adapterSucceed->send($notification)->shouldBeCalledOnce();
        $this->addAdapter($adapterSucceed);

        $this->send($notification, $channel);
    }
}
