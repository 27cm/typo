<?php

use Wheels\Loader;

/**
 * Разделитель директорий. Для Windows - "\", для Linux и остальных — "/".
 */
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Разделитель пути к файлу. Для Windows - ";", для Linux и остальных — ":".
 */
if(!defined('PS')) define('PS', PATH_SEPARATOR);

/**
 * Каталог с библиотекой Wheels.
 */
define('WHEELS_DIR', dirname(__FILE__));

require_once WHEELS_DIR . DS . 'Wheels' . DS . 'Loader.php';
$loader = new Loader('Wheels', WHEELS_DIR);
$loader->register();
