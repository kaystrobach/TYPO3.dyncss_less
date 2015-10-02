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
			$options['relativeUrls'] = FALSE;

			$files = array(
				$inputFilename => ''
			);

			$compiledFile = $options['cache_dir'] . '/' . \Less_Cache::Get($files, $options, $this->overrides);

			return file_get_contents($compiledFile);
		} catch(\Exception $e) {
			return $e;
		}
	}

	/**
	 * Fixes pathes to compliant with original location of the file.
	 *
	 * @param $string
	 * @return mixed
	 *
	 * @todo add typehinting
	 */
	public function _postCompile($string) {
		/**
		 * $relativePath seems to be unused?
		 * @todo missing declaration of inputFilename
		 */
		$relativePath = dirname(substr($this->inputFilename, strlen(PATH_site))) . '/';

		/**
		 * @todo missing declaration of $matches
		 */
		preg_match_all('|url[\s]*\([\s]*(?<url>[^\)]*)[\s]*\)[\s]*|Ui', $string, $matches, PREG_SET_ORDER);

		if(is_array($matches) && count($matches)) {
			foreach($matches as $key => $value) {
				$url = trim($value['url'], '\'"');
				$newPath = $this->resolveUrlInCss($url);
				$string = preg_replace('|'.preg_quote($url).'|', $newPath, $string, 1);
			}
		}
		return $string;
	}

	/**
	 * fixes URLs for use in CSS files
	 *
	 * @param $url
	 * @return string
	 *
	 * @todo add typehinting
	 */
	public function resolveUrlInCss($url) {
		if(strpos($url, '://') !== FALSE) {
			// http://, ftp:// etc. urls leave untouched
			return $url;
		} elseif(substr($url, 0, 1) === '/') {
			// absolute path, should not be touched
			return $url;
		} else {
			// anything inside TYPO3 has to be adjusted
			return '../../' . dirname($this->removePrefixFromString(PATH_site, $this->inputFilename)) . '/' . $url;
		}
	}

	protected function _checkIfCompileNeeded($inputFilename) {
		return true;
	}
}
