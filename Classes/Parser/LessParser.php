<?php

namespace KayStrobach\DyncssLess\Parser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class tx_DyncssLess_Parser
 *
 * Adapts the Less.php Parser to compile less files
 */
class LessParser extends \KayStrobach\Dyncss\Parser\AbstractParser{

	/**
	 *
	 */
	function __construct() {
		parent::__construct();

		// ensure no one else has loaded lessc already ;)
		if(!class_exists('Less_Cache')) {
			require_once(ExtensionManagementUtility::extPath('dyncss_less') . 'Resources/Private/Php/less.php/Autoloader.php');
			\Less_Autoloader::register();
		}

		$this->parser = NULL;
	}

	public function getVersion() {
		return \Less_Version::version . ' - compat less.js version ' . \Less_Version::less_version;
	}

	/**
	 * @param $string
	 * @param null $name
	 * @return string
	 */
	public function compile($string, $name = null) {
		return $this->_compile($string, $name);
	}

	/**
	 * @param $string
	 * @param null $name
	 * @return string
	 */
	protected function _compile($string, $name = null) {
		// TODO: Implement _compile() method.
	}

	/**
	 * @param $string
	 * @return mixed
	 *
	 * @return string
	 */
	protected function _prepareCompile($string) {
		return $string;
	}

	/**
	 * @param $inputFilename
	 * @param $outputFilename
	 * @param $cacheFilename
	 *
	 * @return string
	 */
	protected function _compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename) {
		try {
			$options = array(
				'import_dirs' => array(
					dirname($inputFilename) => dirname($inputFilename),
					PATH_site               => PATH_site
				),
				'cache_dir' => GeneralUtility::getFileAbsFileName('typo3temp/DynCss/Cache')
			);

			if($this->config['enableDebugMode']) {
				$options['sourceMap'] = TRUE;
			}

			$files = array(
				$inputFilename => ''
			);

			$compiledFile = $options['cache_dir'] . '/' . \Less_Cache::Get($files, $options, $this->overrides);

			return file_get_contents($compiledFile);
		} catch(\Exception $e) {
			return $e;
		}
	}

	protected function _checkIfCompileNeeded($inputFilename) {
		return true;
	}
}