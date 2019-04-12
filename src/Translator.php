<?php

namespace Scaleplan\Translator;

use Scaleplan\Helpers\FileHelper;
use function Scaleplan\Helpers\get_required_env;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator AS SymfonyTranslator;

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

    /**
     * @var string
     */
    protected $lang;

    /**
     * @var string
     */
    protected $translatesDirPath;

    /**
     * Translator constructor.
     *
     * @param string|null $lang
     * @param string|null $translatesDirPath
     *
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(string $lang = null, string $translatesDirPath = null)
    {
        $this->translatesDirPath = $translatesDirPath
            ?? (get_required_env('BUNDLE_PATH') . get_required_env('TRANSLATES_PATH'));
        $this->lang = $lang ?? get_required_env('DEFAULT_LANG');
    }

    /**
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function loadTranslatesFromDir() : void
    {
        foreach (FileHelper::getRecursivePaths("{$this->translatesDirPath}/{$this->lang}") as $file) {
            $fileInfo = pathinfo($file, PATHINFO_EXTENSION | PATHINFO_FILENAME);
            $ext = $fileInfo['extension'];
            $domain = $fileInfo['filename'];
            if (\in_array($ext, static::SUPPORTING_LOADERS, true)) {
                $this->getTranslator()->addResource($ext, $file, $this->lang, $domain);
            }
        }
    }

    /**
     * @return SymfonyTranslator
     *
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function getTranslator() : SymfonyTranslator
    {
        static $translator;
        if (!$translator) {
            $translator = new SymfonyTranslator($this->lang);
            $translator->addLoader(static::PHP_LOADER, new ArrayLoader());
            $translator->addLoader(static::YML_LOADER, new YamlFileLoader());
            $translator->addLoader(static::XLF_LOADER, new XliffFileLoader());

            $translator->setFallbackLocales([get_required_env('DEFAULT_LANG')]);
        }

        return $translator;
    }
}
