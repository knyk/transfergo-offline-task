<?php

namespace spec\App\Service;

use App\Service\NotificationSenderAdapter\Adapter;
use App\Service\NotificationSenderAdapter\SendingFailed;
use App\ValueObject\Channel;
use PhpSpec\ObjectBehavior;

class NotificationSenderSpec extends ObjectBehavior
{
    public function it_throws_exception_if_adapters_array_is_empty()
    {
        $this->shouldThrow(SendingFailed::class)->during('send', ['example@example.com', Channel::Email]);
    }

    public function it_throws_exception_if_adapter_fails(Adapter $adapter)
    {
        $email = 'example@example.com';
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($email)->shouldBeCalledOnce()->willThrow(SendingFailed::withChannel($channel));
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$email, $channel]);
    }

    public function it_throws_exception_if_adapter_doesnt_support_channel(Adapter $adapter)
    {
        $email = 'example@example.com';
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(false);
        $adapter->send($email)->shouldNotBeCalled();
        $this->addAdapter($adapter);

        $this->shouldThrow(SendingFailed::class)->during('send', [$email, $channel]);
    }

    public function it_should_send_notification_using_supported_adapter(Adapter $adapter)
    {
        $email = 'example@example.com';
        $channel = Channel::Email;

        $adapter->supports($channel)->willReturn(true);
        $adapter->send($email)->shouldBeCalledOnce();
        $this->addAdapter($adapter);

        $this->send($email, $channel);
    }


    public function it_should_send_notification_using_next_adapter_if_previous_fails(
        Adapter $adapterFailed,
        Adapter $adapterSucceed
    ) {
        $email = 'example@example.com';
        $channel = Channel::Email;

        $adapterFailed->supports($channel)->willReturn(true);
        $adapterFailed->send($email)->shouldBeCalledOnce()->willThrow(
            SendingFailed::withAdapter($adapterFailed->getWrappedObject())
        );
        $this->addAdapter($adapterFailed);

        $adapterSucceed->supports($channel)->willReturn(true);
        $adapterSucceed->send($email)->shouldBeCalledOnce();
        $this->addAdapter($adapterSucceed);

        $this->send($email, $channel);
    }
}
