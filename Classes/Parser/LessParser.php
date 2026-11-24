<?php

namespace KayStrobach\DyncssLess\Parser;

use KayStrobach\Dyncss\Utilities\LegacyExtensionPath;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class tx_DyncssLess_Parser
 *
 * Adapts the Less.php Parser to compile less files
 */
class LessParser extends \KayStrobach\Dyncss\Parser\AbstractParser
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        // ensure no one else has loaded lessc already ;)
        if (!class_exists('Less_Cache')) {
            require_once(ExtensionManagementUtility::extPath('dyncss_less') . 'Resources/Private/Php/less.php/Autoloader.php');
            \Less_Autoloader::register();
        }
    }

    public function getVersion()
    {
        return \Less_Version::version . ' - compat less.js version ' . \Less_Version::less_version;
    }

    /**
     * returns the homepage of the parser
     * @return string
     */
    public function getParserHomepage()
    {
        return 'http://lessphp.gpeasy.com';
    }

    /**
     * return readable name of the project
     * @return string
     */
    public function getParserName()
    {
        return 'Less.php';
    }

    /**
     * @param $string
     * @param null $name
     * @return string
     */
    public function compile($string, $name = null)
    {
        return $this->_compile($string, $name);
    }

    /**
     * @param $string
     * @param null $name
     * @return string
     */
    protected function _compile($string, $name = null)
    {
        $errorReporting = error_reporting(0);
        try {
            $parser = new \Less_Parser();
            $parser->ModifyVars($this->overrides);
            $parser->parse((string)$string);
            return $parser->getCss();
        } catch (\Exception $e) {
            return '/* ' . str_replace('*/', '* /', $e->getMessage()) . ' */';
        } finally {
            error_reporting($errorReporting);
        }
    }

    /**
     * @param $string
     * @return mixed
     *
     * @return string
     */
    protected function _prepareCompile($string)
    {
        return $string;
    }

    /**
     * @param $inputFilename
     * @param $outputFilename
     * @param $cacheFilename
     *
     * @return string
     */
    protected function _compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename)
    {
        try {
            $options = array(
                'import_dirs' => array(
                    dirname($inputFilename) => dirname($inputFilename),
                    Environment::getPublicPath() . '/'               => Environment::getPublicPath() . '/',
                    // imports between extensions written for typo3conf/ext/ (composer mode), a callable is
                    // supported by the bundled less.php and wikimedia/less.php
                    'legacy-extension-paths' => [self::class, 'resolveImport'],
                ),
                'cache_dir' => GeneralUtility::getFileAbsFileName('typo3temp/DynCss/Cache'),
            );

            if (isset($this->config['enableDebugMode']) && ($this->config['enableDebugMode'])) {
                $options['sourceMap'] = true;
                $options['sourceMapRootpath'] = '/';
                $options['sourceMapBasepath'] = GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT');
            }

            $files = array(
                $inputFilename => ''
            );

            // less.php 1.7 is not free of PHP 8 warnings
            $errorReporting = error_reporting(0);
            try {
                $compiledFile = $options['cache_dir'] . '/' . \Less_Cache::Get($files, $options, $this->overrides);
            } finally {
                error_reporting($errorReporting);
            }

            return file_get_contents($compiledFile);
        } catch (\Exception $e) {
            // visible in the generated stylesheet instead of breaking the page
            return '/* ' . str_replace('*/', '* /', $e->getMessage()) . ' */';
        }
    }

    /**
     * Import directory of less.php for imports like "../../../../theme_bootstrap/Resources/Private/Dyncss/import.less".
     *
     * @return array{0: string|null, 1: string|null} path and uri of the import, null if not resolved
     */
    public static function resolveImport(string $path): array
    {
        $file = LegacyExtensionPath::resolve($path, ['less']);
        return [$file, $file === null ? null : dirname($path)];
    }

    protected function _checkIfCompileNeeded($inputFilename)
    {
        return true;
    }
}
