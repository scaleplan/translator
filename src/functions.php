<?php

namespace Scaleplan\Translator;

use Scaleplan\Main\App;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\DependencyInjection\get_static_container;

/**
 * @param string $id
 * @param array $parameters
 * @param string|null $locale
 *
 * @return string
 *
 * @throws \ReflectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
 * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
 */
function translate(string $id, array $parameters = [], string $locale = null) : string
{
    if (!$id) {
        return '';
    }

    /** @var App $app */
    $app = get_static_container(App::class);
    if (!$locale) {
        $locale = $app::getLocale();
    }
    /** @var \Symfony\Component\Translation\Translator $translator */
    $translator = get_required_container(TranslatorInterface::class, [$locale]);
    $idArray = explode('.', $id);
    $domain = array_shift($idArray);
    $id = implode('.', $idArray);

    $translation = $translator->trans($id, [], $domain, $locale);
    if ($translation !== $id) {
        foreach ($parameters as $key => $value) {
            $translation = str_replace(":$key", $value, $translation);
        }
    }

    return $translation !== $id ? $translation : '';
}
