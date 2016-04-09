<?php

if(class_exists('\KayStrobach\Dyncss\Configuration\BeRegistry')) {
    \KayStrobach\Dyncss\Configuration\BeRegistry::get()->registerFileHandler('less', 'KayStrobach\DyncssLess\Parser\LessParser');
}
