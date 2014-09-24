<?php

/**
 * @todo missing docblock
 */
class tx_DyncssLess_Parser extends tx_Dyncss_Parser_AbstractParser{

	function __construct() {
		// ensure no one else has loaded lessc already ;)
		if(!class_exists('Less_Cache')) {
			require_once(t3lib_extMgm::extPath('dyncss_less') . 'Resources/Private/Php/less.php/Autoloader.php');
			Less_Autoloader::register();
		}

		$config = array(
			//'cache_dir'=>'/var/www/writable_folder',
		);

		if($this->config['enableDebug']) {
			$config['sourceMap'] = TRUE;
		}

		$this->parser = NULL;
	}

	/**
	 * @param $string
	 * @param null $name
	 * @return mixed
	 *
	 * @todo add typehinting
	 */
	public function compile($string, $name = null) {
		return $this->_compile($string, $name);
	}

	/**
	 * @param $string
	 * @param null $name
	 * @return mixed
	 *
	 * @todo missing typehinting
	 */
	protected function _compile($string, $name = null) {
		// TODO: Implement _compile() method.
	}

	/**
	 * @param $string
	 * @return mixed
	 *
	 * @todo missing typehinting
	 */
	protected function _prepareCompile($string) {
		return $string;
	}

	/**
	 * @param $inputFilename
	 * @param $outputFilename
	 * @param $cacheFilename
	 *
	 * @todo missing typehinting
	 */
	protected function _compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename) {
		try {
			$options = array(
				'import_dirs' => array(
					dirname($inputFilename) => dirname($inputFilename),
					PATH_site               => PATH_site
				),
				'cache_dir' => t3lib_div::getFileAbsFileName('typo3temp/DynCss/Cache')
			);

			$files = array(
				$inputFilename => ''
			);

			$compiledFile = $options['cache_dir'] . '/' . Less_Cache::Get($files, $options, $this->overrides);

			return file_get_contents($compiledFile);
		} catch(Exception $e) {
			return $e;
		}
	}

	protected function _checkIfCompileNeeded($inputFilename) {
		return true;
	}
}