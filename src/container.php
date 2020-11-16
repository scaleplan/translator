<?php

use Scaleplan\Translator\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => static function (string $locale = null) : TranslatorInterface {
        $translator = new Translator($locale);
        $translator->loadTranslatesFromDir();

        return $translator->getTranslator();
    },
];
