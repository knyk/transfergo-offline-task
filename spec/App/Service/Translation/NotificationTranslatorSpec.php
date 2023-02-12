<?php

namespace spec\App\Service\Translation;

use App\Service\Translation\NotificationTranslator;
use App\Service\Translation\Translator;
use App\ValueObject\Channel;
use App\ValueObject\Notification;
use PhpSpec\ObjectBehavior;

class NotificationTranslatorSpec extends ObjectBehavior
{
    private const FALLBACK_LOCALE = 'en_US';

    public function let(Translator $translator): void
    {
        $this->beConstructedWith($translator);
    }

    public function it_should_return_new_notification_object_with_content_and_subject_translated(
        Translator $translator
    ): void {
        $notification = new Notification(
            Channel::Email,
            'example@example.com',
            'contentTranslationKey',
            'subjectTranslationKey'
        );

        $translator->translate($notification->content, 'pl_PL', self::FALLBACK_LOCALE)->willReturn('translatedContent');
        $translator->translate($notification->subject, 'pl_PL', self::FALLBACK_LOCALE)->willReturn('translatedSubject');

        $this->translate($notification, 'pl_PL')->shouldBeLike(
            new Notification($notification->channel, $notification->receiver, 'translatedContent', 'translatedSubject')
        );
    }

    public function it_should_return_new_notification_object_with_content_translated_only(
        Translator $translator
    ): void {
        $notification = new Notification(
            Channel::Email,
            'example@example.com',
            'contentTranslationKey',
        );

        $translator->translate($notification->content, 'pl_PL', self::FALLBACK_LOCALE)->willReturn('translatedContent');

        $this->translate($notification, 'pl_PL')->shouldBeLike(
            new Notification($notification->channel, $notification->receiver, 'translatedContent')
        );
    }
}
