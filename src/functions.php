<?php

namespace Scaleplan\Translator;

use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\DependencyInjection\get_static_container;
use Scaleplan\Main\App;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @param string $id
 * @param array $parameters
 *
 * @return string|null
 * @throws \ReflectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
 * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
 */
function translate(string $id, array $parameters = []) : ?string
{
    /** @var App $app */
    $app = get_static_container(App::class);
    /** @var TranslatorInterface $translator */
    $translator = get_required_container(TranslatorInterface::class, [$app::getLocale()]);
    $idArray = explode('.', $id);
    $domain = array_shift($idArray);
    $id = implode('.', $idArray);

    $translation = $translator->trans($id, $parameters, $domain);

    return $translation !== $id ? $translation : null;
}
