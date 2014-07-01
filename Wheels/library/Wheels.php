<?php

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    trigger_error('Для работы библиотеки требуется версия php 5.3.0 или выше', E_USER_ERROR);
}

if (!extension_loaded('mbstring')) {
    $ext = ((substr(PHP_OS, 0, 3) == 'WIN') ? 'dll' : 'so');
//    if (!ini_get('enable_dl') || !dl('mbstring' . $ext)) {
        trigger_error('Для работы библиотеки требуется расширение mbstring', E_USER_ERROR);
//    }
}

use Wheels\Loader;

/**
 * Разделитель директорий. Для Windows - "\", для Linux и остальных — "/".
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Разделитель пути к файлу. Для Windows - ";", для Linux и остальных — ":".
 */
if (!defined('PS')) {
    define('PS', PATH_SEPARATOR);
}

/**
 * Минимальное целое число.
 */
if (!defined('PHP_INT_MIN')) {
    define('PHP_INT_MIN', ~PHP_INT_MAX);
}

/**
 * Каталог с библиотекой Wheels.
 */
define('WHEELS_DIR', dirname(__FILE__));

require_once(WHEELS_DIR . DS . 'Wheels' . DS . 'functions.php');

require_once(WHEELS_DIR . DS . 'Wheels' . DS . 'Loader.php');

Loader::staticRegister('Wheels', WHEELS_DIR);
