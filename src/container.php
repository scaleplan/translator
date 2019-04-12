<?php

use Scaleplan\Translator\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    TranslatorInterface::class => static function (string $lang = null) : TranslatorInterface {
        $translator = new Translator($lang);
        $translator->loadTranslatesFromDir();

        return $translator->getTranslator();
    },
];
