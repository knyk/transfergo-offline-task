<?php

declare(strict_types=1);

namespace App\Service\Translation;

class FakeTranslator implements Translator
{
    public function translate(string $key, string $locale, string $fallbackLocale): string
    {
        return $key;
    }
}
