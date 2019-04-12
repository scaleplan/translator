<?php

namespace Scaleplan\Translator;

/**
 * Class TranslatableException
 *
 * @package Scaleplan\Translator
 */
class TranslatableException extends \Exception
{
    public const DEFAULT_MESSAGE = 'Unwritten error.';

    /**
     * TranslatableException constructor.
     *
     * @param string $message
     * @param array $parameters
     * @param int $code
     * @param \Throwable|null $previous
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function __construct(string $message = '', array $parameters = [], $code = 0, \Throwable $previous = null)
    {
        parent::__construct(translate($message, $parameters) ?? static::DEFAULT_MESSAGE, $code, $previous);
    }
}
