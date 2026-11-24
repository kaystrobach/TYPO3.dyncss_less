<?php

defined('TYPO3') || die('Access denied.');

\KayStrobach\Dyncss\Configuration\BeRegistry::get()->registerFileHandler('less', \KayStrobach\DyncssLess\Parser\LessParser::class);
