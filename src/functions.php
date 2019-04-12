<?php

use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\DependencyInjection\get_static_container;
use Scaleplan\Main\App;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @param $id
 * @param array $parameters
 *
 * @return string
 *
 * @throws ReflectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
 * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
 * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
 */
function translate($id, array $parameters = []) : string
{
    /** @var App $app */
    $app = get_static_container(App::class);
    /** @var TranslatorInterface $translator */
    $translator = get_required_container(TranslatorInterface::class, [$app::getLang()]);

    return $translator->trans($id, $parameters);
}
