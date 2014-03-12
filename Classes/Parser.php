<?php

class tx_DyncssLess_Parser extends tx_Dyncss_Parser_AbstractParser{
	function __construct() {
		// ensure no one else has loaded lessc already ;)
		if(!class_exists('lessc')) {
			include_once(t3lib_extMgm::extPath('dyncss_less') . 'Resources/Private/Php/less.php/Autoloader.php');
			Less_Autoloader::register();
		}
		// build instance to usage
		$this->parser = new Less_Parser(
			array(
				//'cache_dir'=>'/var/www/writable_folder',
				'sourceMap' => true,

			)
		);
	}
	/**
	 * @param $string
	 * @param null $name
	 * @return mixed
	 */
	protected function _compile($string, $name = null) {
		// TODO: Implement _compile() method.
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	protected function _prepareCompile($string) {
		/**
		 * Change the initial value of a less constant before compiling the file
		 */
		if(is_array($this->overrides)) {
			foreach($this->overrides as $key => $value) {
				$string = preg_replace(
					'/@' . $key . ':(.*);/U',
					 '@' . $key . ': ' . $value . ';',
					 $string,
					 1
				);
			}
		}
		return $string;
	}

	/**
	 * @param $inputFilename
	 * @param $outputFilename
	 * @param $cacheFilename
	 */
	protected function _compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename) {
		try {
			$this->parser->setImportDirs(
				array(
					dirname($inputFilename),
					PATH_site
				)
			);
			$this->parser->parseFile($preparedFilename);
			return $this->parser->getCss();
		} catch(Exception $e) {
			return $e;
		}
	}

	protected function _checkIfCompileNeeded($inputFilename) {
		return true;
	}
}