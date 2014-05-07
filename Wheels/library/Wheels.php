<?php

/**
 * @version 1.0beta
 */

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

require_once(WHEELS_DIR . DS . 'Wheels' . DS . 'Loader.php');

$loader = new Loader('Wheels', WHEELS_DIR);
$loader->register();
