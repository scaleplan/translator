<?php

namespace Scaleplan\Translator;

use Scaleplan\File\FileHelper;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use function Scaleplan\Helpers\get_required_env;

/**
 * Class Translator
 *
 * @package Scaleplan\Translator
 */
class Translator
{
    public const PHP_LOADER = 'php';
    public const YML_LOADER = 'yml';
    public const XLF_LOADER = 'xlf';

    public const SUPPORTING_LOADERS = [self::PHP_LOADER, self::YML_LOADER, self::XLF_LOADER,];

    public const DEFAULT_LOCALE        = 'en_US';
    public const SECOND_DEFAULT_LOCALE = 'ru_RU';

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $translatesDirPath;

    /**
     * @var SymfonyTranslator
     */
    protected $translator;

    /**
     * Translator constructor.
     *
     * @param string|null $locale
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(string $locale = null)
    {
        $this->locale = $locale ?? get_required_env('DEFAULT_LANG');
        $this->translatesDirPath = get_required_env('BUNDLE_PATH') . get_required_env('TRANSLATES_PATH');
    }

    /**
     * @param string $locale
     * @param string $translatesDirPath
     *
     * @return string
     */
    public static function getRealLocale(string $locale, string $translatesDirPath) : string
    {
        $lang = explode('_', $locale)[0];
        if ($lang && !is_dir("$translatesDirPath/$locale")) {
            $similar = glob("$translatesDirPath/$lang*");
            if ($similar) {
                return basename($similar[0]);
            }

            return static::DEFAULT_LOCALE;
        }

        return $locale;
    }

    /**
     * @param string|null $translatesDirPath
     *
     * @throws TranslatableException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function loadTranslatesFromDir(string $translatesDirPath = null) : void
    {
        $translatesDirPath = $translatesDirPath ?: $this->translatesDirPath;
        $locale = static::getRealLocale($this->locale, $translatesDirPath);
        $lang = explode('_', $locale)[0];

        foreach (FileHelper::getRecursivePaths("$translatesDirPath/" . static::DEFAULT_LOCALE) as $enFile) {
            $fileInfo = pathinfo($enFile);
            $ext = $fileInfo['extension'];
            if (!in_array($ext, static::SUPPORTING_LOADERS, true)) {
                throw new TranslatableException('Translate file extension not supported.');
            }

            $domain = $fileInfo['filename'];
            $this->getTranslator()->addResource($ext, $enFile, static::DEFAULT_LOCALE, $domain);
            $ruFile = str_replace(static::DEFAULT_LOCALE, static::SECOND_DEFAULT_LOCALE, $enFile);
            file_exists($ruFile) && $this->getTranslator()->addResource(
                $ext,
                $ruFile,
                static::SECOND_DEFAULT_LOCALE,
                $domain
            );
            if (in_array($this->locale, [static::DEFAULT_LOCALE, static::SECOND_DEFAULT_LOCALE,], true)) {
                continue;
            }

            $file = str_replace(static::DEFAULT_LOCALE, $locale, $enFile);
            if (file_exists($file)) {
                $this->getTranslator()->addResource($ext, $file, $this->locale, $domain);
                continue;
            }

            $similar = glob("$translatesDirPath/$lang*");
            if ($similar
                && file_exists($fallbackFile = str_replace("$translatesDirPath/$locale", $similar[0], $file))
            ) {
                $this->getTranslator()->addResource($ext, $fallbackFile, $this->locale, $domain);
            }
        }
    }

    /**
     * @return SymfonyTranslator
     */
    public function getTranslator() : SymfonyTranslator
    {
        if (!$this->translator) {
            $this->translator = new SymfonyTranslator($this->locale);
//            $translator->addLoader(static::PHP_LOADER, new ArrayLoader());
            $this->translator->addLoader(static::YML_LOADER, new YamlFileLoader());
//            $translator->addLoader(static::XLF_LOADER, new XliffFileLoader());

            $this->translator->setFallbackLocales(['en_US', 'ru_RU',]);
        }

        return $this->translator;
    }
}
